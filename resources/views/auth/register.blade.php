<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }

        .register-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .register-title {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .welcome-text {
            color: #666;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-input.is-invalid {
            border-color: #dc3545;
        }

        .register-button {
            width: 100%;
            padding: 0.75rem;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 1rem;
        }

        .register-button:hover {
            background-color: #1976D2;
        }

        .login-text {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #666;
        }

        .login-link {
            color: #2196F3;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1 class="register-title">Welcome, Create your account</h1>
        <p class="welcome-text">It is our great pleasure to have you join us!</p>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <x-input-error :messages="$errors->get('name')" class="error-message" />
                <x-text-input 
                    id="name"
                    name="name"
                    type="text"
                    class="form-input"
                    :value="old('name')"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Enter your name"
                />
            </div>

            <div class="form-group">
                <x-input-error :messages="$errors->get('email')" class="error-message" />
                <x-text-input 
                    id="email"
                    name="email"
                    type="email"
                    class="form-input"
                    :value="old('email')"
                    required
                    autocomplete="username"
                    placeholder="Enter your email"
                />
            </div>

            <div class="form-group">
                <x-input-error :messages="$errors->get('password')" class="error-message" />
                <x-text-input 
                    id="password"
                    name="password"
                    type="password"
                    class="form-input"
                    required
                    autocomplete="new-password"
                    placeholder="Enter password"
                />
            </div>

            <div class="form-group">
                <x-input-error :messages="$errors->get('password_confirmation')" class="error-message" />
                <x-text-input 
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="form-input"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm password"
                />
            </div>

            <x-primary-button class="register-button">
                {{ __('Register') }}
            </x-primary-button>

            <p class="login-text">
                Already have an account? 
                <a class="login-link" href="{{ route('login') }}">
                    {{ __('Login here') }}
                </a>
            </p>
        </form>
    </div>
</body>
</html>