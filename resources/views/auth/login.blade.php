<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>Iniciar Sesión | {{ config('app.name') }}</title>

    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>

<body class="c-app flex-row align-items-center">
    <div class="container">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-center">
                <img width="200" src="{{ asset('images/mi-logo.png') }}" alt="Logo">
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-5">

                @if(Session::has('account_deactivated'))
                    <div class="alert alert-danger">
                        {{ Session::get('account_deactivated') }}
                    </div>
                @endif

                <div class="card p-4 border-0 shadow-sm">
                    <div class="card-body">
                        <form id="login" method="post" action="{{ url('/login') }}">
                            @csrf

                            <h1>Iniciar Sesión</h1>
                            <p class="text-muted">Ingresa a tu cuenta</p>

                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" placeholder="Correo electrónico">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="input-group mb-4">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Contraseña" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-4">
                                    <button id="submit" class="btn btn-primary px-4 d-flex align-items-center"
                                        type="submit">
                                        Ingresar
                                        <div id="spinner"
                                            class="spinner-border spinner-border-sm text-light ms-2 d-none"></div>
                                    </button>
                                </div>
                                <div class="col-8 text-end">
                                    <a class="btn btn-link px-0" href="{{ route('password.request') }}">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <p class="text-center mt-5 lead">
                    Desarrollado por
                    <a href="wa.link/7y9r4b" class="fw-bold text-primary">Thiago Alvarez</a>
                </p>
            </div>
        </div>
    </div>

    <script src="{{ mix('js/app.js') }}" defer></script>
</body>

</html>