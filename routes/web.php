<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\LoaderController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ======= Админ панель =======
Route::prefix('admin')
    ->middleware(['admin'])
    ->group(function () {
        Route::get('/load/pack/form', [LoaderController::class, 'index'])->name('pack-loader');
        Route::post('/load/pack', [LoaderController::class, 'load']);
    });

// ======= Google Auth =======
Route::get('/login/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/login/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

Route::get('/logout', function (Request $request) {
    Auth::logout();

    // Сброс сессии
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Получим откуда пришли (если нет — на главную)
    $previousUrl = $request->headers->get('referer');
    return redirect($previousUrl ?? '/');
})->name('logout');


// ======= Главные маршруты =======
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/terms-of-use', [LegalController::class, 'terms'])->name('terms-of-use');
Route::get('/privacy-policy', [LegalController::class, 'privacy'])->name('privacy-policy');
Route::get('/commission', [CommissionController::class, 'index'])->name('commission');

// ======= Каталог =======
Route::get('/catalog/{param1?}/{param2?}/{param3?}/{param4?}/{param5?}', [CatalogController::class, 'index'])->name('catalog');

// ======= Пакеты и загрузки =======
Route::get('/{section}/{franchise}/{name?}/{action?}', [PackController::class, 'index'])->name('pack');

Route::get('/test-demon', function () {
    return 'OK';
});



