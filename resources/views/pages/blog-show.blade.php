<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->meta_title ?? $article->title }} - Pakaiapp</title>
    <meta name="description" content="{{ $article->meta_description ?? $article->excerpt }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .blog-header { background: #fff; padding: 2rem 0; border-bottom: 1px solid #eaeaea; }
        .article-content { background: #fff; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .article-content h2 { margin-top: 2rem; margin-bottom: 1rem; }
        .article-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 1.5rem 0; }
    </style>
</head>
<body>

<div class="blog-header">
    <div class="container">
        <a href="{{ route('blog.index', [], false) }}" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left"></i> Semua Artikel</a>
        <h1 class="fw-bold">{{ $article->title }}</h1>
        <p class="text-muted"><i class="bi bi-calendar3"></i> Dipublikasikan pada {{ $article->published_at->format('d F Y') }}</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="article-content fs-5" style="line-height: 1.8;">
                {!! $article->content !!}
            </div>

            <div class="mt-5 text-center">
                <div class="card p-4 border-0 shadow-sm" style="background-color: #f0fdf4;">
                    <h4>Butuh Aplikasi Kasir Gratis?</h4>
                    <p class="text-muted">Pakaiapp adalah aplikasi kasir (POS) berbasis web tanpa biaya langganan bulanan. Daftar sekarang dan kelola bisnis UMKM Anda dengan lebih mudah!</p>
                    <a href="/register" class="btn btn-success">Coba Pakaiapp Gratis</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
