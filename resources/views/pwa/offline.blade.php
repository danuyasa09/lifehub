<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Offline | {{ config('app.name', 'LifeHub') }}</title>
        
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#4F46E5">
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f9fafb;
                color: #111827;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
                text-align: center;
                padding: 1rem;
            }
            .icon {
                width: 80px;
                height: 80px;
                color: #9ca3af;
                margin-bottom: 1.5rem;
            }
            h1 {
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
            }
            p {
                color: #6b7280;
                margin-bottom: 2rem;
                max-width: 400px;
            }
            .btn {
                background-color: #4F46E5;
                color: white;
                padding: 0.75rem 1.5rem;
                border-radius: 0.5rem;
                text-decoration: none;
                font-weight: 500;
                border: none;
                cursor: pointer;
                transition: background-color 0.2s;
            }
            .btn:hover {
                background-color: #4338ca;
            }
        </style>
    </head>
    <body>
        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path></svg>
        
        <h1>You are offline</h1>
        <p>It seems you've lost your internet connection. LifeHub requires an active connection to sync your latest tasks and habits.</p>
        
        <button onclick="window.location.reload()" class="btn">Try Again</button>
    </body>
</html>
