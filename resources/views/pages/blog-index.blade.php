<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog & Artikel Pakaiapp - Tips Bisnis UMKM</title>
    <meta name="description" content="Baca artikel terbaru seputar tips bisnis UMKM, F&B, Ritel, dan penggunaan sistem kasir cerdas dari Pakaiapp.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
</head>
<body>

<div class="blog-header">
    <div class="container">
        <h1>Blog Pakaiapp</h1>
        <p class="text-muted">Tips, Trik, dan Panduan Seputar Bisnis UMKM & Kasir Web</p>
        <a href="/" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        @forelse($articles as $article)
            <div class="col-md-4">
                <div class="card card-blog h-100">
                    <div class="card-body">
                        <small class="text-muted">{{ $article->published_at->format('d M Y') }}</small>
                        <h5 class="card-title mt-2">
                            <a href="{{ route('blog.show', $article->slug, false) }}" class="text-decoration-none text-dark stretched-link">
                                {{ $article->title }}
                            </a>
                        </h5>
                        <p class="card-text text-muted">{{ $article->excerpt }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">Belum ada artikel yang dipublikasikan.</p>
            </div>
        @endforelse
    </div>
    
    <div class="d-flex justify-content-center mt-5">
        {{ $articles->links() }}
    </div>
</div>

</body>
</html>
