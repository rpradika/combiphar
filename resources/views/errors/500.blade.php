<!DOCTYPE html>
{{-- Standalone on purpose: no Vite/Inertia — a 500 may be a failure of those. --}}
<html lang="{{ app()->getLocale() ?: 'id' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>500 — Combiphar</title>
    <style>
        body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;background:#532882;color:#fff;font-family:'Albert Sans','Segoe UI',system-ui,sans-serif;padding:24px}
        h1{color:#bd106f;font-size:clamp(40px,7vw,96px);font-weight:400;margin:0;line-height:1.1}
        .t{font-size:clamp(20px,3vw,42px);margin:16px 0 0}
        .s{font-size:clamp(14px,1.2vw,20px);margin:24px auto 0;max-width:560px;line-height:1.5}
        a{display:inline-flex;align-items:center;justify-content:center;min-width:170px;min-height:44px;margin-top:24px;padding:0 24px;border-radius:22px;background:#df0077;color:#fff;font-size:14px;text-decoration:none}
    </style>
</head>
<body>
    <main>
        <h1>500</h1>
        <p class="t">Terjadi kesalahan pada server</p>
        <p class="s">Maaf, terjadi kesalahan tak terduga. Silakan coba beberapa saat lagi.<br>Sorry — something went wrong on our end. Please try again shortly.</p>
        <a href="/">Kembali ke Beranda / Back to Homepage</a>
    </main>
</body>
</html>
