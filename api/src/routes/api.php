<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\NoCacheMiddleware;
use Illuminate\Http\Request;
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

    // [ マスタデータ ] ##################################################################################################

    // メール取得
    Route::get('mail', [MailController::class, 'index'])->name('mails.index');
    // アイテム取得
    Route::get('item', [ItemController::class, 'index'])->name('items.index');
});
