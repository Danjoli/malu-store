<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Public\ShopController;
use App\Http\Controllers\Public\AuthController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\Public\ProfileController;
use App\Http\Controllers\Public\AddressController;
use App\Http\Controllers\Public\PaymentController;
use App\Http\Controllers\Public\WebhookController;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Public\FreteController;

/*
|--------------------------------------------------------------------------
| WEBHOOK (FORA DO AUTH - ESSENCIAL)
|--------------------------------------------------------------------------
*/

Route::post('/webhook/mercadopago', [WebhookController::class, 'mercadopago'])
    ->name('webhook.mercadopago');


/*
|--------------------------------------------------------------------------
| LOJA PÚBLICA
|--------------------------------------------------------------------------
*/

Route::get('/', [ShopController::class, 'index'])->name('shop.index');

Route::get('/policy', function () {
    return view('public.pages.policy');
})->name('policy');

Route::get('/terms', function () {
    return view('public.pages.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('public.pages.privacy');
})->name('privacy');

Route::get('/product/{id}', [ShopController::class, 'show'])
    ->name('product.show');


/*
|--------------------------------------------------------------------------
| AUTENTICAÇÃO CLIENTE
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| ÁREA DO CLIENTE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PERFIL
    |--------------------------------------------------------------------------
    */

    Route::get('/perfil', [ProfileController::class,'edit'])
        ->name('profile.edit');

    Route::put('/perfil', [ProfileController::class,'update'])
        ->name('profile.update');

    Route::put('/perfil/senha', [ProfileController::class,'updatePassword'])
        ->name('profile.password.update');

    Route::post('/perfil/endereco', [ProfileController::class,'storeAddress'])
        ->name('profile.address.store');

    Route::delete('/perfil/endereco/{id}', [ProfileController::class,'deleteAddress'])
        ->name('profile.address.delete');

    /*
    |--------------------------------------------------------------------------
    | PEDIDOS DO CLIENTE
    |--------------------------------------------------------------------------
    */

    Route::get('/perfil/pedidos', [ProfileController::class,'orders'])
        ->name('profile.orders');

    Route::get('/perfil/pedidos/{id}', [ProfileController::class,'orderShow'])
        ->name('profile.orders.show');

    /*
    |--------------------------------------------------------------------------
    | CARRINHO
    |--------------------------------------------------------------------------
    */

    Route::prefix('cart')->group(function () {

        Route::get('/', [CartController::class,'index'])
            ->name('cart.index');

        Route::post('/add', [CartController::class,'add'])
            ->name('cart.add');

        Route::put('/{id}', [CartController::class,'update'])
            ->name('cart.update');

        Route::delete('/{id}', [CartController::class,'remove'])
            ->name('cart.remove');

    });

    /*
    |--------------------------------------------------------------------------
    | ENDEREÇOS
    |--------------------------------------------------------------------------
    */

    Route::post('/addresses', [AddressController::class,'store'])
        ->name('addresses.store');

    Route::put('/addresses/{id}', [AddressController::class,'update'])
        ->name('addresses.update');

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT
    |--------------------------------------------------------------------------
    */

    Route::get('/checkout', [CheckoutController::class,'index'])
        ->name('checkout');

    Route::post('/checkout/process', [CheckoutController::class,'processOrder'])
        ->name('checkout.process');

    Route::get('/payment/{order}', [CheckoutController::class,'payment'])
        ->name('payment');

    Route::post('/payment/{order}/confirm', [CheckoutController::class,'confirmPayment'])
        ->name('payment.confirm');

    /*
    |--------------------------------------------------------------------------
    | FRETE (MELHOR ENVIO)
    |--------------------------------------------------------------------------
    */

    Route::post('/frete/calcular', [FreteController::class, 'calcular'])
        ->name('frete.calcular');

    /*
    |--------------------------------------------------------------------------
    | PAGAMENTOS
    |--------------------------------------------------------------------------
    */

    Route::get('/payment/pix/{order}', [PaymentController::class, 'createPix'])
        ->name('payment.pix');

    Route::get('/payment/boleto/{order}', [PaymentController::class, 'createBoleto'])
        ->name('payment.boleto');

    Route::get('/payment/card/{order}', [PaymentController::class, 'createCard'])
        ->name('payment.card');

    Route::post('/payment/card/{order}', [PaymentController::class, 'processCard'])
        ->name('payment.card.process');

    /*
    |--------------------------------------------------------------------------
    | STATUS DO PAGAMENTO (AJAX)
    |--------------------------------------------------------------------------
    */

    Route::get('/payment/status/{order}', [PaymentController::class, 'status'])
        ->name('payment.status');

    /*
    |--------------------------------------------------------------------------
    | RESULTADO DO PAGAMENTO
    |--------------------------------------------------------------------------
    */

    Route::get('/payment-success/{order}', [PaymentController::class, 'success'])
        ->name('payment.success');

    Route::get('/payment-error/{order}', [PaymentController::class, 'error'])
        ->name('payment.error');

});


/*
|--------------------------------------------------------------------------
| LOGIN ADMIN
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminAuthController::class,'showLogin'])
        ->name('login');

    Route::post('/login', [AdminAuthController::class,'login'])
        ->name('login.submit');

});


/*
|--------------------------------------------------------------------------
| ÁREA ADMINISTRATIVA
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {

    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/logout', [AdminAuthController::class,'logout'])
        ->name('logout');

    /*
    |--------------------------------------------------------------------------
    | SUPERADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin.role:superadmin')->group(function () {

        Route::resource('admins', AdminController::class);
        Route::resource('clients', ClientController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('orders', OrdersController::class);
        Route::resource('shipments', ShipmentController::class);

    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin.role:admin,superadmin')->group(function () {

        Route::resource('clients', ClientController::class)->only(['index','show']);
        Route::resource('categories', CategoryController::class)->only(['index','show','update','destroy']);
        Route::resource('products', ProductController::class)->only(['index','show','update','destroy']);
        Route::resource('orders', OrdersController::class)->only(['index','show','update']);
        Route::resource('shipments', ShipmentController::class)->only(['index','show','update']);

    });

    /*
    |--------------------------------------------------------------------------
    | SUPORTE
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin.role:suporte,admin,superadmin')->group(function () {

        Route::resource('orders', OrdersController::class)->only(['index','show']);
        Route::resource('shipments', ShipmentController::class)->only(['index','show']);

    });

});

