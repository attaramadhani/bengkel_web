<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bengkel App | Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --accent-primary: #3b82f6;
            --accent-secondary: #8b5cf6;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            padding: 3rem;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .form-group {
            text-align: left;
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #486581;
        }
        .form-control {
            width: 100%;
            padding: 14px 20px;
            border-radius: 15px;
            border: 2px solid #f0f4f8;
            box-sizing: border-box;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent-primary);
            background: white;
        }
        .btn-login {
            width: 100%;
            padding: 16px;
            border-radius: 15px;
            border: none;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
        }
        .alert {
            background: #fff5f5;
            color: #c53030;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">BENGKEL PRO</div>
        <p style="color: #627d98; margin-bottom: 2rem;">Silakan login untuk masuk ke sistem.</p>

        @if($errors->any())
            <div class="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-login">Login Sekarang</button>
        </form>
        
        <p style="margin-top: 2rem; font-size: 0.85rem; color: #9fb3c8;">
            Lupa password? Hubungi Admin IT.
        </p>
    </div>
</body>
</html>
