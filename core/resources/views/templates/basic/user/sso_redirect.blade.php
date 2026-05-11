<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connecting to SparkProxy...</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #0f172a;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #e2e8f0;
        }

        .card {
            text-align: center;
            padding: 3rem 2.5rem;
            background: #1e293b;
            border-radius: 1rem;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            max-width: 380px;
            width: 90%;
        }

        .spinner {
            width: 56px;
            height: 56px;
            border: 4px solid #334155;
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 1.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #f1f5f9;
        }

        p {
            font-size: 0.9rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner"></div>
        <h2>Connecting to SparkProxy</h2>
        <p>Please wait while we sign you in&hellip;</p>
    </div>

    <script>
        setTimeout(function () {
            window.location.href = "{{ $redirectUrl }}";
        }, 1500);
    </script>
</body>
</html>
