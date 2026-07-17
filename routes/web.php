<?php

use App\Http\Controllers\PageController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/'.config('app.locale')));

Route::get('sitemap.xml', [PageController::class, 'sitemap']);
Route::get('robots.txt', fn () => response(
    "User-agent: *\nDisallow:\n\nSitemap: ".url('/sitemap.xml')."\n",
    200,
    ['Content-Type' => 'text/plain'],
));

/*
 * Localized path per page: [id, en]. English URLs are unchanged; Indonesian URLs
 * use the slugs from combiphar.com. Route names are suffixed with the locale
 * (e.g. "about.id" / "about.en"); generate URLs via App\Support\Localize::url().
 */
$slugs = [
    'about' => ['id' => 'tentang-kami', 'en' => 'about'],
    'products' => ['id' => 'produk', 'en' => 'products'],
    'csr' => ['id' => 'csr-komunitas', 'en' => 'csr-community'],
    'news' => ['id' => 'berita', 'en' => 'news'],
    'investor' => ['id' => 'investor', 'en' => 'investor'],
    'contact' => ['id' => 'kontak-kami', 'en' => 'contact'],
    'terms' => ['id' => 'syarat-ketentuan', 'en' => 'terms-of-use'],
    'privacy' => ['id' => 'kebijakan-privasi', 'en' => 'privacy-policy'],
];

foreach (SetLocale::SUPPORTED as $loc) {
    Route::prefix($loc)
        ->middleware(SetLocale::class.':'.$loc)
        ->group(function () use ($loc, $slugs) {
            Route::get('/', [PageController::class, 'home'])->name("home.$loc");
            Route::get($slugs['about'][$loc], [PageController::class, 'about'])->name("about.$loc");
            Route::get($slugs['products'][$loc], [PageController::class, 'products'])->name("products.$loc");
            Route::get($slugs['csr'][$loc], [PageController::class, 'csr'])->name("csr.$loc");
            Route::get($slugs['csr'][$loc].'/{slug}', [PageController::class, 'csrShow'])->name("csr.show.$loc");
            Route::get($slugs['news'][$loc], [PageController::class, 'news'])->name("news.$loc");
            Route::get($slugs['news'][$loc].'/{slug}', [PageController::class, 'newsShow'])->name("news.show.$loc");
            Route::get('search', [PageController::class, 'search'])->name("search.$loc");
            Route::get($slugs['investor'][$loc], [PageController::class, 'investor'])->name("investor.$loc");
            Route::get($slugs['contact'][$loc], [PageController::class, 'contact'])->name("contact.$loc");
            Route::post($slugs['contact'][$loc], [PageController::class, 'contactSubmit'])->name("contact.submit.$loc");
            Route::get($slugs['terms'][$loc], [PageController::class, 'terms'])->name("terms.$loc");
            Route::get($slugs['privacy'][$loc], [PageController::class, 'privacy'])->name("privacy.$loc");

            /*
             * Unknown path under this locale prefix. Registering it inside the group
             * (rather than only handling the 404 exception) keeps SetLocale and the web
             * middleware in play, so the page renders in the right locale with the
             * shared nav/footer props. Left unnamed on purpose: a null route name makes
             * HandleInertiaRequests fall altUrls back to the locale homes.
             */
            Route::fallback([PageController::class, 'notFound']);
        });
}

/*
 * 301 redirects: old Indonesian (English-style) paths => new localized slugs.
 * English paths are unchanged, so only /id/* needs redirecting.
 */
Route::redirect('id/about', '/id/tentang-kami', 301);
Route::redirect('id/products', '/id/produk', 301);
Route::redirect('id/csr', '/id/csr-komunitas', 301);
Route::redirect('id/news', '/id/berita', 301);
Route::redirect('id/contact', '/id/kontak-kami', 301);
Route::redirect('id/terms-of-use', '/id/syarat-ketentuan', 301);
Route::redirect('id/privacy-notice', '/id/kebijakan-privasi', 301);
Route::get('id/csr/{slug}', fn ($slug) => redirect('/id/csr-komunitas/'.$slug, 301));
Route::get('id/news/{slug}', fn ($slug) => redirect('/id/berita/'.$slug, 301));
Route::redirect('en/csr', '/en/csr-community', 301);
Route::redirect('en/privacy-notice', '/en/privacy-policy', 301);
Route::get('en/csr/{slug}', fn ($slug) => redirect('/en/csr-community/'.$slug, 301));

/*
 * Unknown path with no locale prefix (e.g. /nope). Renders in the default locale;
 * the per-locale fallbacks above are registered first, so they win for /id/* and /en/*.
 */
Route::fallback([PageController::class, 'notFound']);
