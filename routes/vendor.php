<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\Auth\LoginController;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\MedicionVendorController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\CustomerController;



Route::prefix('auth')->as('auth.')->group(function () {
    Route::get('login', [LoginController::class, 'login'])->name('login');
    Route::post('login', [LoginController::class, 'submit']);
    Route::get('verify-otp', [LoginController::class, 'verifyotp'])->name('verify-otp');
    Route::post('verify-otp', [LoginController::class, 'submitotp'])->name('submit-otp');
    Route::get('resend-otp', [LoginController::class, 'resendOtp'])->name('resend-otp');
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
});

Route::group(['middleware' => ['vendor']], function () {
    Route::get('/test',function(){
       return response()->json("Sss"); 
    });
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('vendor.dashboard');  
    
    // Route::prefix('order')->as('vendor.order.')->group(function () {
        Route::get('order/list', [DashboardController::class, 'dashboard'])->name('vendor.order.list');  
    // });
    
      Route::prefix('medicion')->as('vendor.medicion.')->group(function () {
        Route::get('list', [MedicionVendorController::class, 'index'])->name('list');
        Route::get('create', [MedicionVendorController::class, 'create'])->name('create');
        Route::post('store', [MedicionVendorController::class, 'store'])->name('store');
         Route::get('view/{id}', [MedicionVendorController::class, 'show'])->name('view');
        Route::get('edit/{id}', [MedicionVendorController::class, 'edit'])->name('edit');
        Route::post('update/{id}', [MedicionVendorController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [MedicionVendorController::class, 'destroy'])->name('delete');
         Route::post('search',  [MedicionVendorController::class, 'search'])->name('search');
           Route::get('status/{id}/{status}', [MedicionVendorController::class, 'status'])->name('status');
            Route::get('remove-image', [MedicionVendorController::class, 'remove_image'])->name('remove-image');
    });
    
    // Route::prefix('booking')->as('vendor.order')->group(function () {
    //         Route::get('list/{status}', [OrderController::class,'list'])->name('list');
    // });
Route::group(['prefix' => 'order', 'as' => 'vendor.order.', 'middleware' => ['module:order']], function () {
    
    Route::get('orderlist/{status}', [OrderController::class, 'list'])->name('vendorelist'); 
    //  Route::get('list/{status}', [OrderController::class, 'list'])->name('list'); 
    Route::get('details/{id}', [OrderController::class, 'details'])->name('details');
    Route::get('status', [OrderController::class, 'status'])->name('status');
    Route::get('download-invoice/{id}', [OrderController::class, 'downloadInvoice'])->name('download-invoice');
    Route::get('view/{id}', [OrderController::class, 'view'])->name('view');
    Route::post('update-shipping/{order}', [OrderController::class, 'update_shipping'])->name('update-shipping');
    Route::delete('delete/{id}', [OrderController::class, 'delete'])->name('delete');
      Route::get('generate-invoice/{id}', [OrderController::class, 'generate_invoice'])->name('generate-invoice');
      
      Route::get('medicion/booking/filter/reset', [OrderController::class,'medicionfilter_reset'])->name('medicion-booking-filter-reset');
            Route::post('medicion/booking/filter', [OrderController::class,'medicionfilter'])->name('medicion-booking-filter');
      
});

Route::group(['prefix' => 'customer', 'as' => 'vendor.customer.', 'middleware' => ['module:customerList']], function () {
            Route::get('list', [CustomerController::class,'list'])->name('list');
            Route::get('view/{user_id}',[CustomerController::class,'view'])->name('view');
            Route::post('search', [CustomerController::class,'search'])->name('search');
            Route::get('status/{customer}/{status}', [CustomerController::class,'status'])->name('status');
        });
  
    Route::group(['prefix' => 'product-category', 'as' => 'vendor.product-category.'], function () {
            Route::group(['middleware' => ['module:category']], function () {
                Route::get('list',  [DashboardController::class, 'list'])->name('list');
              
                Route::post('search', 'ProductCategoryController@search')->name('search');
            });
        });
         

});




