<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{config("app.name")}}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Admin, Dashboard, Bootstrap" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" sizes="196x196" href="../assets/images/logo.png">

    <link rel="stylesheet" href="{{ asset('libs/bower/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/bower/material-design-iconic-font/dist/css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/bower/animate.css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/misc-pages.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300">
</head>
<body class="simple-page">
<div class="simple-page-wrap">
    <div class="simple-page-form animated flipInY" id="login-form">
{{--        <div class="simple-page-logo animated swing">--}}
{{--            <a href="{{config("app.url")}}">--}}
{{--                <span><img src="{{asset('assets/images/zeus-logo.png')}}" alt=""></span>--}}
{{--            </a>--}}
{{--        </div><!-- logo -->--}}
        <h4 class="form-title m-b-xl text-center">Iniciar Sesion</h4>
        <div class="panel-body">
            <form class="form-horizontal" method="POST" action="{{ route('login') }}" id="login-form-submit">
                @csrf

                <div class="form-group{{ $errors->has('login') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input id="login" type="text" class="form-control" name="login" placeholder="Email, Usuario o NIT" value="{{ old('login') }}" required autofocus>
                        <small class="text-muted">Ingresa tu correo electrónico, nombre de usuario o NIT</small>

                        @if ($errors->has('login'))
                            <span class="help-block">
                                <strong>{{ $errors->first('login') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <div class="col-md-12 position-relative">
        <div class="input-group">
                        <input id="password" type="password" class="form-control" placeholder="Contraseña" name="password" required>
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary" id="toggle-password" tabindex="8">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
        </div>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" id="keep_me_logged_in" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="keep_me_logged_in">Mantener Sesion</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            Login
                        </button>
                    </div>
                </div>
            </form>
            <div id="debug-info" style="display:none;"></div>
        </div>
        <form class="d-none" action="#">
            <div class="form-group">
                <input id="sign-in-email" type="email" class="form-control" placeholder="Email">
            </div>

            <div class="form-group">
                <input id="sign-in-password" type="password" class="form-control" placeholder="Password">
            </div>

            <div class="form-group m-b-xl">
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" id="keep_me_logged_in"/>
                    <label for="keep_me_logged_in">Mantener sesion</label>
                </div>
            </div>
            <input type="submit" class="btn btn-primary" value="SING IN">
        </form>
    </div><!-- #login-form -->

    <div class="simple-page-footer">
        <p><a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a></p>
    </div><!-- .simple-page-footer -->


</div><!-- .simple-page-wrap -->
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
        // Configuración global para AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Mostrar/ocultar contraseña
        $('#toggle-password').on('click', function() {
            const passwordInput = $('#password');
            const icon = $(this).find('i');

            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
</script>

   <!-- <script>
        $(document).ready(function() {
            // Configuración global para AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Manejar envío de formulario directamente sin AJAX
            $('#login-form-submit').on('submit', function() {
                // Permitir que el formulario se envíe normalmente
                return true;
            });
        });
    </script>-->
</body>
</html>
