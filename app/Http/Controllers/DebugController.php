<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DebugController extends Controller
{
    public function testLogin(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        
        // Verificar si existe un usuario con ese email o nit
        $user = User::where('email', $email)
            ->orWhere('nit', $email)
            ->orWhere('name', $email)
            ->first();
            
        $output = [];
        
        // Verificar si la tabla users tiene las columnas necesarias
        $columns = Schema::getColumnListing('users');
        $output['table_columns'] = $columns;
        
        if ($user) {
            $output['user_found'] = true;
            $output['user_data'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nit' => $user->nit,
                'role' => $user->role,
                'level' => $user->level,
                'status' => $user->status ?? null,
                'active_user' => $user->active_user ?? null,
                'password_hash' => $user->password,
            ];
            
            // Probar la contraseña
            $checkPassword = Hash::check($password, $user->password);
            $output['password_matches'] = $checkPassword;
            
            // Intentar el login
            $loginResult = Auth::attempt([
                'email' => $user->email,
                'password' => $password
            ]);
            
            $output['login_result'] = $loginResult;
            
            if ($loginResult) {
                $output['authenticated_user'] = Auth::user()->name;
            } else {
                // Verificar posibles causas de fallo de login
                $output['possible_issues'] = [];
                
                if (!$checkPassword) {
                    $output['possible_issues'][] = 'La contraseña no coincide';
                }
                
                if (isset($user->active_user) && $user->active_user !== 'enabled') {
                    $output['possible_issues'][] = 'El usuario está desactivado (active_user: ' . $user->active_user . ')';
                }
                
                if (isset($user->status) && $user->status !== 'good') {
                    $output['possible_issues'][] = 'El estado del usuario no es correcto (status: ' . $user->status . ')';
                }
            }
        } else {
            $output['user_found'] = false;
            
            // Verificar todos los usuarios para depuración
            $allUsers = User::select('id', 'name', 'email', 'nit', 'role', 'level', 'status', 'active_user')
                ->orderBy('id')
                ->get();
                
            $output['all_users'] = $allUsers;
        }
        
        return response()->json($output);
    }
    
    public function dumpUsers()
    {
        // Obtener todos los usuarios
        $users = User::select('id', 'name', 'email', 'nit', 'role', 'level', 'status', 'active_user')
            ->orderBy('id')
            ->get();
        
        $columns = Schema::getColumnListing('users');
        
        return response()->json([
            'columns' => $columns,
            'users_count' => $users->count(),
            'users' => $users
        ]);
    }
}
