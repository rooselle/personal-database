<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.__(config('app.name')) : __(config('app.name')) }}
</title>

<link rel="icon" href="/images/book-stack.png" type="image/png">
<link rel="apple-touch-icon" href="/images/book-stack.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
