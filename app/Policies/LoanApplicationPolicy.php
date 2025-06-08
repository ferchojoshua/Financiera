<?php

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanApplicationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Todos los usuarios autenticados pueden ver la lista
    }

    public function view(User $user, LoanApplication $application)
    {
        return true; // Todos los usuarios autenticados pueden ver los detalles
    }

    public function create(User $user)
    {
        return true; // Todos los usuarios autenticados pueden crear solicitudes
    }

    public function update(User $user, LoanApplication $application)
    {
        // Solo el creador o un administrador pueden editar una solicitud pendiente
        return $application->status === 'pending' && 
               ($user->id === $application->created_by || $user->hasRole('admin'));
    }

    public function delete(User $user, LoanApplication $application)
    {
        // Solo el creador o un administrador pueden eliminar una solicitud pendiente
        return $application->status === 'pending' && 
               ($user->id === $application->created_by || $user->hasRole('admin'));
    }

    public function approve(User $user, LoanApplication $application)
    {
        // Solo analistas y administradores pueden aprobar solicitudes
        return $application->status === 'pending' && 
               ($user->hasRole(['admin', 'analyst']));
    }

    public function reject(User $user, LoanApplication $application)
    {
        // Solo analistas y administradores pueden rechazar solicitudes
        return $application->status === 'pending' && 
               ($user->hasRole(['admin', 'analyst']));
    }

    public function assign(User $user, LoanApplication $application)
    {
        // Solo administradores pueden asignar analistas
        return $user->hasRole('admin');
    }
} 