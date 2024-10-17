<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\MonsterController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\NoCacheMiddleware;
use Illuminate\Support\Facades\Route;

// ミドルウェアのルートを通す(キャッシュを保存しない)
Route::middleware([NoCacheMiddleware::class])->group(function () {

    // [ ユーザー ] #####################################################################################################
    Route::prefix('users')->name('users.')->controller(UserController::class)
        ->group(function () {
            // ユーザー情報取得・登録・更新
            Route::get('/show', 'show')->name('show');
            Route::post('/store', 'store')->name('store');
            Route::post('/update', 'update')->name('update');

            // 所持アイテムリスト取得・更新
            Route::get('/item/show', 'showItem')->name('item.show');
            Route::post('/item/update', 'updateItem')->name('item.update');

            // メールリスト取得・開封・削除
            Route::get('/mail/show', 'showMail')->name('mail.show');
            Route::post('/mail/update', 'updateMail')->name('mail.update');
            Route::post('/mail/destroy', 'destroyMail')->name('mail.destroy');
        });

    // [ アイテム ] #####################################################################################################
    Route::prefix('items')->name('items.')->controller(ItemController::class)
        ->group(function () {
            // アイテム情報取得
            Route::get('/show', 'show')->name('show');
        });

    // [ モンスター ] ####################################################################################################
    Route::prefix('monsters')->name('monsters.')->controller(MonsterController::class)
        ->group(function () {
            // モンスター情報取得
            Route::get('/show', 'show')->name('show');
        });
});
