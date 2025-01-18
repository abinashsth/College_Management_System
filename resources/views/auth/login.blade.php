<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-title {
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

        .login-button {
            width: 100%;
            padding: 0.75rem;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .login-button:hover {
            background-color: #1976D2;
        }

        .signup-text {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #666;
        }

        .signup-link {
            color: #2196F3;
            text-decoration: none;
        }

        /* Status message styling */
        .status-message {
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 4px;
            background-color: #e3f2fd;
            color: #0d47a1;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">Welcome, Log into your account</h1>
        <p class="welcome-text">It is our great pleasure to have you on board!</p>

        <!-- Session Status -->
        <x-auth-session-status class="status-message" :status="session('status')" />
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <x-input-error :messages="$errors->get('email')" class="error-message" />
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Enter the name" 
                    class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    value="{{ old('email') }}"
                    required
                >
            </div>

            <div class="form-group">
                <x-input-error :messages="$errors->get('password')" class="error-message" />
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Enter Password" 
                    class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    required
                >
            </div>

            <button type="submit" class="login-button">Login</button>
        </form>

        <p class="signup-text">
            If you don't have an account? <a href="{{ route('register') }}" class="signup-link">Sign up</a>
        </p>
    </div>
</body>
</html>