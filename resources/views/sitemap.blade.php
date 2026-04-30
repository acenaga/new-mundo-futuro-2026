@php echo '<?xml version="1.0" encoding="UTF-8"@endphp'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc>{{ route('tutoriales') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc>{{ route('publicaciones') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    @foreach ($tutorials as $post)
        <url>
            <loc>{{ route('tutoriales.show', $post) }}</loc>
            <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

    @foreach ($publicaciones as $post)
        <url>
            <loc>{{ route('publicaciones.show', $post) }}</loc>
            <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

</urlset>
