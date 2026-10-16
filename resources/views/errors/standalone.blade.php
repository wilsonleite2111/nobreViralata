{{-- Self-contained on purpose: it must render even when the build, the database or the main layout is what failed. --}}
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>{{ $title }} — Soul True Noise</title>
        <style>
            body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0e0d10; color: #fff; font-family: system-ui, sans-serif; text-align: center; }
            main { max-width: 36rem; padding: 2rem; }
            .code { margin: 0; font-size: 5rem; font-weight: 700; color: #b6b7b0; }
            h1 { margin: 0.5rem 0 0; font-size: 1.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
            p { color: rgb(255 255 255 / 0.7); font-size: 1.125rem; line-height: 1.6; }
            a { display: inline-block; margin-top: 1.5rem; padding: 0.75rem 2rem; background: #b6b7b0; color: #0e0d10; font-weight: 600; text-decoration: none; text-transform: uppercase; letter-spacing: 0.1em; }
        </style>
    </head>
    <body>
        <main>
            <p class="code">{{ $code }}</p>
            <h1>{{ $title }}</h1>
            <p>{{ $message }}</p>
            <a href="/">Página inicial</a>
        </main>
    </body>
</html>
