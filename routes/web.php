
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;

if (!function_exists('statusStok')) {
    function statusStok($stok)
    {
        return $stok > 0 ? 'Tersedia' : 'Habis';
    }
}

Route::get('/produk', [ProdukController::class, 'index'])
    ->name('produk.index');

Route::post('/produk', [ProdukController::class, 'store'])
    ->name('produk.store');

Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])
    ->name('produk.edit');

Route::put('/produk/{id}', [ProdukController::class, 'update'])
    ->name('produk.update');

Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])
    ->name('produk.destroy');
