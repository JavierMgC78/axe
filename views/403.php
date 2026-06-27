<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo_error) ?> - Axe Framework</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background-color: #0F172A;
            color: #F8FAFC;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .error-container {
            background: #1E293B;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 40px;
            max-width: 480px;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5);
        }
        .error-icon {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .error-icon svg {
            width: 32px;
            height: 32px;
        }
        h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #F1F5F9;
        }
        p {
            color: #94A3B8;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-back {
            display: inline-block;
            background: #4F46E5;
            color: #FFFFFF;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.3s ease, transform 0.1s ease;
        }
        .btn-back:hover {
            background: #4338CA;
        }
        .btn-back:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

    <div class="error-container">
        <div class="error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        
        <h1><?= htmlspecialchars($titulo_error) ?></h1>
        <p><?= htmlspecialchars($mensaje_error) ?></p>
        
        <a href="/" class="btn-back">Regresar al Inicio</a>
    </div>

</body>
</html>