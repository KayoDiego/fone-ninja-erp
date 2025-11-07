<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ERP Fone Ninja - Sistema de Estoque</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --verde-principal: #339A4F;
            --verde-hover: #28753B;
            --fundo-claro: #E9FDEE;
            --bordas-suave: #CDE9D4;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--fundo-claro);
            color: #1f2d25;
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.85);
            color: var(--verde-principal);
            padding: 1rem 2rem;
            box-shadow: 0 2px 6px rgba(51, 154, 79, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            border: 1px solid rgba(51, 154, 79, 0.12);
            backdrop-filter: blur(12px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .brand img {
            height: 38px;
        }

        .brand span {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--verde-principal);
        }

        .nav-links {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .nav-link {
            color: var(--verde-principal);
            text-decoration: none;
            padding: 0.45rem 0.9rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            background-color: rgba(51, 154, 79, 0.08);
        }

        .nav-link:hover {
            background-color: rgba(51, 154, 79, 0.18);
        }

        .nav-link.active {
            background-color: var(--verde-principal);
            color: white;
            font-weight: 600;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 2rem;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .brand span {
                font-size: 1.25rem;
            }

            .nav-links {
                flex-direction: column;
                align-items: flex-start;
            }

            .container {
                padding: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div id="app"></div>

    @vite(['resources/js/app.js'])
</body>
</html>

