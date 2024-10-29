<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\MonsterController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\NoCacheMiddleware;
use Illuminate\Support\Facades\Route;

// ミドルウェアのルートを通す(キャッシュを保存しない)
Route::middleware([NoCacheMiddleware::class])->group(function () {

    // ユーザーの登録
    Route::post('users/store', [UserController::class, 'store'])->name('users.store');

    // 認証ミドルウェアのRouteを通す
    Route::middleware('auth:sanctum')->group(function () {

        // [ ユーザー ] #####################################################################################################
        Route::prefix('users')->name('users.')->controller(UserController::class)
            ->group(function () {
                // ユーザー情報取得
                Route::get('show', 'show')->name('show');
                // プレイデータ取得
                Route::get('play-data', 'playData')->name('play-data');
                // ユーザー情報取得更新
                Route::post('update', 'update')->name('update');
            });

        // [ アイテム ] #####################################################################################################
        Route::prefix('items')->name('items.')->controller(ItemController::class)
            ->group(function () {
                // 全アイテム情報取得
                Route::get('/', 'index')->name('index');
                // ユーザーの所持品情報取得
                Route::get('show', 'show')->name('show');
                // アイテム入手・更新処理
                Route::post('update', 'update')->name('update');
            });

        // [ モンスター ] ####################################################################################################
        Route::prefix('monsters')->name('monsters.')->controller(MonsterController::class)
            ->group(function () {
                // 全モンスター情報取得
                Route::get('/', 'index')->name('index');
                // 育成完了したモンスターIDの取得
                Route::get('nurtured', 'nurtured')->name('nurtured');
                // 育成中のモンスター情報を取得
                Route::get('nurturing', 'nurturing')->name('nurturing');
                // 育成済モンスターを新着順に30件取得
                Route::get('new30', 'new30')->name('new30');
                // 初回育成モンスター登録
                Route::post('init-store', 'initStore')->name('init-store');
                // 育成中モンスターの情報更新
                Route::post('update', 'update')->name('update');
                // 運動処理
                Route::post('exercise', 'exercise')->name('exercise');
                // 食事処理
                Route::post('meal', 'meal')->name('meal');
                // ミラクル配合
                Route::post('mix/miracle', 'miracle')->name('miracle');
                // 指定配合
                Route::post('mix/designation', 'designation')->name('designation');
            });
    });
});
