<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlogController;

Route::group(['namespace' => 'Admin', 'as' => 'admin.'], function () {
    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit')->middleware('actch');
        Route::get('logout', 'LoginController@logout')->name('logout');
    });
    /*authentication*/

    Route::group(['middleware' => ['admin']], function () {

        Route::get('lab-test-booking-list', 'OrderController@labTestBookinglist')->name('lab-test-booking-list');
        Route::get('booking-detail', 'OrderController@labTestBookingDetail')->name('booking-detail');
        Route::get('generate-invoice/{id}', 'OrderController@generateInvoiceForLabTest')->name('generate-invoice');
        Route::get('download-invoice/{id}', 'OrderController@downloadInvoiceForLabTest')->name('download-invoice');
        Route::get('search-lab-test', 'OrderController@searchLabTest')->name('search-lab-test');

        Route::get('appointment-list/{status}', 'AppointmentController@list')->name('appointment-list');
        Route::get('appointment-list/all', [AppointmentController::class, 'list'])->name('appointment-list-all');

        // routes/web.php or routes/admin/appointment.php
         Route::get('appointment/view', [AppointmentController::class, 'view'])->name('appointment-view');
        Route::get('search-appointment', 'AppointmentController@search')->name('search-appointment');
        Route::get('wallet-withdrawal-requests', 'AppointmentController@walletWithdrawRequests')->name('wallet-withdrawal-requests');
        Route::get('search-wallet-withdrawal-requests', 'AppointmentController@searchWalletWithdrawRequests')->name('search-wallet-withdrawal-requests');
        Route::post('manage-wallet-withdrawal-request', 'AppointmentController@manageWalletWithdrawRequest')->name('manage-wallet-withdrawal-request');
         Route::get('wallet-withdraw-requests/view/{id}', 'AppointmentController@viewWalletWithdrawRequest')->name('withdraw.view');
        Route::post('wallet-withdraw-requests/delete', 'AppointmentController@deleteWalletWithdrawRequest')->name('withdraw.delete');

Route::post('/wallet-withdrawal-requests/approve', 'AppointmentController@approveWithdraw')->name('withdraw.approve');
Route::post('/wallet-withdrawal-requests/reject', 'AppointmentController@rejectWithdraw')->name('withdraw.reject');
     
     
        
           Route::get('lab-test-booking/filter/reset', 'OrderController@LabTestfilter_reset')->name('lab-test-booking-filter-reset');
            Route::post('lab-test-booking/filter', 'OrderController@LabTestfilter')->name('lab-test-booking-filter');
        
        Route::get('appointment/filter/reset', 'AppointmentController@appointmentfilter_reset')->name('appointment-filter-reset');
            Route::post('appointment/filter', 'AppointmentController@appointmentfilter')->name('appointment-filter');
            
            
            // Wallet routes
    
        
// Replace lines 26-31 with this:

Route::get('wallet-list/', 'WalletController@index')->name('wallet.transactions');
Route::get('wallet/view/{id}/', 'WalletController@viewCustomerWallet')->name('wallet.view');
Route::post('wallet/delete', 'WalletController@deleteTransaction')->name('wallet.delete');
Route::post('wallet/status', 'WalletController@updateTransactionStatus')->name('wallet.status');
Route::post('wallet/filter', 'WalletController@filter')->name('wallet.filter');
Route::post('wallet/filter-reset', 'WalletController@resetFilter')->name('wallet.filter.reset');
Route::post('wallet/search', 'WalletController@search')->name('wallet.search');

         
 Route::get('wallet-list-advisor/', 'AdvisorWalletController@index')->name('wallet.advisor');
// Route::get('wallet/view/{id}/', 'AdvisorWalletController@viewTransaction')->name('wallet.view');
Route::post('wallet/delete', 'WalletController@deleteTransaction')->name('wallet.delete');
Route::post('wallet/status', 'WalletController@updateTransactionStatus')->name('wallet.status');
Route::post('wallet/filter', 'WalletController@filter')->name('wallet.filter');
Route::post('wallet/filter-reset', 'WalletController@resetFilter')->name('wallet.filter.reset');
Route::post('wallet/search', 'WalletController@search')->name('wallet.search');
    Route::get('wallet/advisors/{doctor_id}', 'AdvisorWalletController@show')
         ->name('wallet.advisor.show');

    Route::post('wallet/advisor/credit', 'AdvisorWalletController@credit')->name('wallet.advisor.credit');
    Route::post('wallet/advisor/debit', 'AdvisorWalletController@debit')->name('wallet.advisor.debit');

            
     
        
        Route::get('settings', 'SystemController@settings')->name('settings');
        Route::post('settings', 'SystemController@settings_update');
        Route::post('settings-password', 'SystemController@settings_password_update')->name('settings-password');
        Route::get('/get-restaurant-data', 'SystemController@restaurant_data')->name('get-restaurant-data');

        //dashboard
        Route::get('/', 'DashboardController@dashboard')->name('dashboard');

        Route::resource('account-transaction', 'AccountTransactionController')->middleware('module:account');

        Route::resource('provide-deliveryman-earnings', 'ProvideDMEarningController')->middleware('module:provide_dm_earning');

        Route::get('maintenance-mode', 'SystemController@maintenance_mode')->name('maintenance-mode');
        
        Route::group(['prefix' => 'dashboard-stats', 'as' => 'dashboard-stats.'], function () {
            Route::post('order', 'DashboardController@order')->name('order');
            Route::post('zone', 'DashboardController@zone')->name('zone');
            Route::post('user-overview', 'DashboardController@user_overview')->name('user-overview');
            Route::post('business-overview', 'DashboardController@business_overview')->name('business-overview');
        });




        //              Route::group(['prefix' => 'blog', 'as' => 'blog.'], function () {
        //   Route::get('request-list', 'BlogControlle@requestList')->name('request-list');
        //   Route::get('view/{id}', 'BlogController@view')->name('view');
        //   Route::get('approve/{id}', 'BlogController@approve')->name('approve');
        //   Route::post('reject/{id}', 'BlogController@reject')->name('reject');
        // });
        
        
Route::group([
    'prefix' => 'reel',
    'as' => 'reel.'
], function () {

    Route::get('list', 'ReelController@index')->name('list');

    Route::get('view/{id}', 'ReelController@view')->name('view');

    Route::delete('delete/{id}', 'ReelController@delete')->name('delete');

    Route::get('status/{id}/{status}', 'ReelController@status')->name('status');

    Route::get('earning-settings', 'ReelController@earningSettings')->name('earning-settings');
    Route::post('earning-settings', 'ReelController@updateEarningSettings');

    Route::get('notifications', 'ReelController@reelNotifications')->name('notifications');

    Route::get('report', 'ReelController@report')->name('report');
});

Route::group([
    'prefix' => 'blog',
    'as' => 'blog.'
], function () {

    Route::get('request-list', [BlogController::class, 'requestList'])
        ->name('request-list');

    Route::get('view/{id}', [BlogController::class, 'view'])
        ->name('view');

    Route::get('approve/{id}', [BlogController::class, 'approve'])
        ->name('approve');

    Route::post('reject/{id}', [BlogController::class, 'reject'])
        ->name('reject');
        
     Route::delete('delete/{id}', [BlogController::class, 'destroy'])
        ->name('delete');
        
});





        Route::group(['prefix' => 'allergy', 'as' => 'allergy.'], function () {
            Route::get('add-new', 'AllergyController@index')->name('add-new');
            Route::post('store', 'AllergyController@store')->name('store');
            Route::get('status/{id}/{status}', 'AllergyController@status')->name('status');
            Route::get('edit/{id}', 'AllergyController@edit')->name('edit');
            Route::post('update/{id}', 'AllergyController@update')->name('update');
            Route::delete('delete/{id}', 'AllergyController@delete')->name('delete');
            Route::post('search', 'AllergyController@search')->name('search');
        });

        Route::group(['prefix' => 'medication', 'as' => 'medication.'], function () {
            Route::get('add-new', 'MedicationController@index')->name('add-new');
            Route::post('store', 'MedicationController@store')->name('store');
            Route::get('status/{id}/{status}', 'MedicationController@status')->name('status');
            Route::get('edit/{id}', 'MedicationController@edit')->name('edit');
            Route::post('update/{id}', 'MedicationController@update')->name('update');
            Route::delete('delete/{id}', 'MedicationController@delete')->name('delete');
            Route::post('search', 'MedicationController@search')->name('search');
        });

        Route::group(['prefix' => 'injury', 'as' => 'injury.'], function () {
            Route::get('add-new', 'InjuryController@index')->name('add-new');
            Route::post('store', 'InjuryController@store')->name('store');
            Route::get('status/{id}/{status}', 'InjuryController@status')->name('status');
            Route::get('edit/{id}', 'InjuryController@edit')->name('edit');
            Route::post('update/{id}', 'InjuryController@update')->name('update');
            Route::delete('delete/{id}', 'InjuryController@delete')->name('delete');
            Route::post('search', 'InjuryController@search')->name('search');
        });

        Route::group(['prefix' => 'surgery', 'as' => 'surgery.'], function () {
            Route::get('add-new', 'SurgeryController@index')->name('add-new');
            Route::post('store', 'SurgeryController@store')->name('store');
            Route::get('status/{id}/{status}', 'SurgeryController@status')->name('status');
            Route::get('edit/{id}', 'SurgeryController@edit')->name('edit');
            Route::post('update/{id}', 'SurgeryController@update')->name('update');
            Route::delete('delete/{id}', 'SurgeryController@delete')->name('delete');
            Route::post('search', 'SurgeryController@search')->name('search');
        });

        Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.', 'middleware' => ['module:custom_role']], function () {
            Route::get('create', 'CustomRoleController@create')->name('create');
            Route::post('create', 'CustomRoleController@store');
            Route::get('edit/{id}', 'CustomRoleController@edit')->name('edit');
            Route::post('update/{id}', 'CustomRoleController@update')->name('update');
            Route::delete('delete/{id}', 'CustomRoleController@distroy')->name('delete');
            Route::post('search', 'CustomRoleController@search')->name('search');
        });

        Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['module:employee']], function () {
            Route::get('add-new', 'EmployeeController@add_new')->name('add-new');
            Route::post('add-new', 'EmployeeController@store');
            Route::get('list', 'EmployeeController@list')->name('list');
            Route::get('update/{id}', 'EmployeeController@edit')->name('edit');
            Route::post('update/{id}', 'EmployeeController@update')->name('update');
            Route::delete('delete/{id}', 'EmployeeController@distroy')->name('delete');
            Route::post('search', 'EmployeeController@search')->name('search');
        });

        Route::group(['prefix' => 'patient', 'as' => 'patient.'], function () {
            Route::get('add-new', 'CustomerController@add_new')->name('add-new');
            Route::post('add-new', 'CustomerController@store');
            Route::get('view/{id}','CustomerController@view')->name('view');
            Route::get('list', 'CustomerController@list')->name('list');
            Route::get('update/{id}', 'CustomerController@edit')->name('edit');
            Route::post('update/{id}', 'CustomerController@update')->name('update');
            Route::delete('delete/{id}', 'CustomerController@destroy')->name('delete');
            Route::post('search', 'CustomerController@search')->name('search');
        });


        Route::group(['prefix' => 'doctor', 'as' => 'doctor.'], function () {
            Route::get('add-new', 'DoctorController@add_new')->name('add-new');
            Route::post('add-new', 'DoctorController@store');
            Route::get('list', 'DoctorController@list')->name('list');
            Route::get('request-list', 'DoctorController@request_list')->name('request-list');
            Route::get('approve/{id}', 'DoctorController@approve_form')->name('approve');
            Route::post('approve/{id}', 'DoctorController@approve_submit')->name('approve.submit');
            Route::post('reject/{id}', 'DoctorController@reject_submit')->name('reject.submit');
            Route::get('update/{id}', 'DoctorController@edit')->name('edit');
           Route::get('bank-view/{id}', 'DoctorController@viewbankdetails')->name('view');

            Route::post('update/{id}', 'DoctorController@update')->name('update');
            Route::delete('delete/{id}', 'DoctorController@distroy')->name('delete');
            Route::post('search', 'DoctorController@search')->name('search');
        });
        
        //----------------------------------Rifaul changes---------------------------------------//
       Route::group(['prefix' => 'medical-store', 'as' => 'medical-store.'], function () {
            Route::get('add-new', 'MedicalStoreController@add_new')->name('add-new');
            Route::post('add-new', 'MedicalStoreController@store');
            Route::get('list', 'MedicalStoreController@list')->name('list');
            Route::get('update/{id}', 'MedicalStoreController@edit')->name('edit');
            Route::post('update', 'MedicalStoreController@update')->name('update');
            Route::delete('delete/{id}', 'MedicalStoreController@destroy')->name('delete');
            Route::post('search', 'MedicalStoreController@search')->name('search');
        });



        Route::post('food/variant-price', 'ProductController@variant_price')->name('food.variant-price');

        Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
            Route::get('add-new', 'ProductController@index')->name('add-new');
            Route::post('variant-combination', 'ProductController@variant_combination')->name('variant-combination');
            Route::post('store', 'ProductController@store')->name('store');
            Route::get('edit/{id}', 'ProductController@edit')->name('edit');
            Route::post('update/{id}', 'ProductController@update')->name('update');
            Route::get('list', 'ProductController@list')->name('list');
            Route::delete('delete/{id}', 'ProductController@delete')->name('delete');
            Route::get('status/{id}/{status}', 'ProductController@status')->name('status');
            Route::get('review-status/{id}/{status}', 'ProductController@reviews_status')->name('reviews.status');
            Route::post('search', 'ProductController@search')->name('search');
            Route::get('remove-image', 'ProductController@remove_image')->name('remove-image');
            Route::get('remove-color-image', 'ProductController@remove_color_image')->name('remove-color-image');
            Route::get('view/{id}', 'ProductController@view')->name('view');
            //ajax request
            Route::get('get-categories', 'ProductController@get_categories')->name('get-categories');
            Route::get('get-foods', 'ProductController@get_foods')->name('getfoods');
            Route::get('get-products', 'ProductController@get_products')->name('get-products');
            
            //Import and export
            Route::get('bulk-import', 'ProductController@bulk_import_index')->name('bulk-import');
            Route::post('bulk-import', 'ProductController@bulk_import_data');
            Route::get('bulk-export', 'ProductController@bulk_export_index')->name('bulk-export-index');
            Route::post('bulk-export', 'ProductController@bulk_export_data')->name('bulk-export');
        });

        Route::group(['prefix' => 'banner', 'as' => 'banner.', 'middleware' => ['module:banner']], function () {
            Route::get('add-new', 'BannerController@index')->name('add-new');
            Route::post('store', 'BannerController@store')->name('store');
            Route::get('edit/{banner}', 'BannerController@edit')->name('edit');
            Route::post('update/{banner}', 'BannerController@update')->name('update');
            Route::get('status/{id}/{status}', 'BannerController@status')->name('status');
            Route::delete('delete/{banner}', 'BannerController@delete')->name('delete');
            Route::post('search', 'BannerController@search')->name('search');
            //-------------------------Rifaul changes---------------------------------//
            Route::get('get-lab-tests', 'BannerController@getLabTests')->name('get-lab-tests');

        });

      
        Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'middleware' => ['module:coupon']], function () {
            Route::get('add-new', 'CouponController@add_new')->name('add-new');
            Route::post('store', 'CouponController@store')->name('store');
            Route::get('update/{id}', 'CouponController@edit')->name('update');
            Route::post('update/{id}', 'CouponController@update');
            Route::get('status/{id}/{status}', 'CouponController@status')->name('status');
            Route::delete('delete/{id}', 'CouponController@delete')->name('delete');
            Route::post('search', 'CouponController@search')->name('search');
        });

        Route::group(['prefix' => 'faq', 'as' => 'faq.', 'middleware' => ['module:coupon']], function () {
            Route::get('add-new', 'FAQController@add_new')->name('faq-add-new');
            Route::post('store', 'FAQController@store')->name('faq-store');
            Route::get('update/{id}', 'FAQController@edit')->name('faq-update');
            Route::post('update/{id}', 'FAQController@update');
            Route::get('status/{id}/{status}', 'FAQController@status')->name('faq-status');
            Route::delete('delete/{id}', 'FAQController@delete')->name('faq-delete');
            Route::post('search', 'FAQController@search')->name('search');
        });

        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::group(['middleware' => ['module:category']], function () {
                Route::get('add', 'CategoryController@index')->name('add-new');
                Route::get('list', 'CategoryController@list')->name('list');
                Route::post('store', 'CategoryController@store')->name('store');
                Route::get('edit/{id}', 'CategoryController@edit')->name('edit');
                Route::post('update/{id}', 'CategoryController@update')->name('update');
                Route::post('store', 'CategoryController@store')->name('store');
                Route::get('status/{id}/{status}', 'CategoryController@status')->name('status');
                Route::delete('delete/{id}', 'CategoryController@delete')->name('delete');
                Route::post('search', 'CategoryController@search')->name('search');
            });
        });

        Route::group(['prefix' => 'product-category', 'as' => 'product-category.'], function () {
            Route::group(['middleware' => ['module:category']], function () {
                Route::get('add', 'ProductCategoryController@index')->name('add-new');
                Route::get('list', 'ProductCategoryController@list')->name('list');
                Route::post('store', 'ProductCategoryController@store')->name('store');
                Route::get('edit/{id}', 'ProductCategoryController@edit')->name('edit');
                Route::post('update/{id}', 'ProductCategoryController@update')->name('update');
                Route::post('store', 'ProductCategoryController@store')->name('store');
                Route::get('status/{id}/{status}', 'ProductCategoryController@status')->name('status');
                Route::delete('delete/{id}', 'ProductCategoryController@delete')->name('delete');
                Route::post('search', 'ProductCategoryController@search')->name('search');
            });
        });

        Route::group(['prefix' => 'product-type', 'as' => 'product-type.'], function () {
            Route::get('add', 'ProductTypeController@index')->name('add-new');
            Route::get('list', 'ProductTypeController@list')->name('list');
            Route::post('store', 'ProductTypeController@store')->name('store');
            Route::get('edit/{id}', 'ProductTypeController@edit')->name('edit');
            Route::post('update/{id}', 'ProductTypeController@update')->name('update');
            Route::post('store', 'ProductTypeController@store')->name('store');
            Route::get('status/{id}/{status}', 'ProductTypeController@status')->name('status');
            Route::delete('delete/{id}', 'ProductTypeController@delete')->name('delete');
            Route::post('search', 'ProductTypeController@search')->name('search');
        });

        Route::group(['prefix' => 'domain', 'as' => 'domain.'], function () {
            Route::get('add', 'DomainController@index')->name('add-new');
            Route::get('list', 'DomainController@index')->name('list'); // Reusing index as list
            Route::post('store', 'DomainController@store')->name('store');
            Route::get('edit/{id}', 'DomainController@edit')->name('edit');
            Route::post('update/{id}', 'DomainController@update')->name('update');
            Route::delete('delete/{id}', 'DomainController@delete')->name('delete');
            Route::post('search', 'DomainController@search')->name('search');
        });

        Route::group(['prefix' => 'speciality', 'as' => 'speciality.'], function () {
            Route::get('add', 'SpecialityController@index')->name('add-new');
            Route::get('list', 'SpecialityController@list')->name('list');
            Route::post('store', 'SpecialityController@store')->name('store');
            Route::get('edit/{id}', 'SpecialityController@edit')->name('edit');
            Route::post('update/{id}', 'SpecialityController@update')->name('update');
            Route::post('store', 'SpecialityController@store')->name('store');
            Route::get('status/{id}/{status}', 'SpecialityController@status')->name('status');
            Route::delete('delete/{id}', 'SpecialityController@delete')->name('delete');
            Route::post('search', 'SpecialityController@search')->name('search');
        });
        
        //-----------------------------------------Rifaul changes--------------------------------------------//
        Route::group(['prefix' => 'degree', 'as' => 'degree.'], function () {
            Route::get('add', 'DegreeController@index')->name('add-new');
            Route::get('list', 'DegreeController@list')->name('list');
            Route::post('store', 'DegreeController@store')->name('store');
            Route::get('edit/{id}', 'DegreeController@edit')->name('edit');
            Route::post('update/{id}', 'DegreeController@update')->name('update');
            Route::get('status/{id}/{status}', 'DegreeController@status')->name('status');
            Route::delete('delete/{id}', 'DegreeController@delete')->name('delete');
            Route::post('search', 'DegreeController@search')->name('search');
        });

        Route::group(['prefix' => 'identity-proof', 'as' => 'identity-proof.'], function () {
            Route::get('add', 'IdentityProofController@index')->name('add-new');
            Route::get('list', 'IdentityProofController@list')->name('list');
            Route::post('store', 'IdentityProofController@store')->name('store');
            Route::get('edit/{id}', 'IdentityProofController@edit')->name('edit');
            Route::post('update/{id}', 'IdentityProofController@update')->name('update');
            Route::get('status/{id}/{status}', 'IdentityProofController@status')->name('status');
            Route::delete('delete/{id}', 'IdentityProofController@delete')->name('delete');
            Route::post('search', 'IdentityProofController@search')->name('search');
        });

        // Relevant Document Types (for doctor registration)
        Route::group(['prefix' => 'relevant-document-type', 'as' => 'relevant-document-type.'], function () {
            Route::get('add', 'RelevantDocumentTypeController@index')->name('add-new');
            Route::post('store', 'RelevantDocumentTypeController@store')->name('store');
            Route::get('edit/{id}', 'RelevantDocumentTypeController@edit')->name('edit');
            Route::post('update/{id}', 'RelevantDocumentTypeController@update')->name('update');
            Route::get('status/{id}/{status}', 'RelevantDocumentTypeController@status')->name('status');
            Route::delete('delete/{id}', 'RelevantDocumentTypeController@delete')->name('delete');
            Route::post('search', 'RelevantDocumentTypeController@search')->name('search');
        });

        Route::group(['prefix' => 'disease', 'as' => 'disease.'], function () {
            Route::get('add', 'DiseaseController@index')->name('add-new');
            Route::get('list', 'DiseaseController@list')->name('list');
            Route::post('store', 'DiseaseController@store')->name('store');
            Route::get('edit/{id}', 'DiseaseController@edit')->name('edit');
            Route::post('update/{id}', 'DiseaseController@update')->name('update');
            Route::post('store', 'DiseaseController@store')->name('store');
            Route::get('status/{id}/{status}', 'DiseaseController@status')->name('status');
            Route::delete('delete/{id}', 'DiseaseController@delete')->name('delete');
            Route::post('search', 'Disease.Controller@search')->name('search');
        });

        Route::group(['prefix' => 'booking', 'as' => 'order.', 'middleware' => ['module:order']], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::get('status', 'OrderController@status')->name('status');
            // Route::put('status-update/{id}', 'OrderController@status')->name('status-update');
            Route::get('download-invoice/{id}', 'OrderController@downloadInvoice')->name('download-invoice');
            Route::get('view/{id}', 'OrderController@view')->name('view');
            Route::post('update-shipping/{order}', 'OrderController@update_shipping')->name('update-shipping');
            Route::delete('delete/{id}', 'OrderController@delete')->name('delete');

            Route::get('add-delivery-man/{order_id}/{delivery_man_id}', 'OrderController@add_delivery_man')->name('add-delivery-man');
            Route::get('payment-status', 'OrderController@payment_status')->name('payment-status');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice')->name('generate-invoice');
            Route::post('add-payment-ref-code/{id}', 'OrderController@add_payment_ref_code')->name('add-payment-ref-code');
            Route::get('restaurant-filter/{restaurant_id}', 'OrderController@restaurnt_filter')->name('restaurant-filter');
            Route::get('filter/reset', 'OrderController@filter_reset');
            Route::post('filter', 'OrderController@filter')->name('filter');
            Route::get('search', 'OrderController@search')->name('search');
            //order update
            Route::post('add-to-cart', 'OrderController@add_to_cart')->name('add-to-cart');
            Route::post('remove-from-cart', 'OrderController@remove_from_cart')->name('remove-from-cart');
            Route::get('update/{order}', 'OrderController@update')->name('update');
            Route::get('edit-order/{order}', 'OrderController@edit')->name('edit');
            Route::get('quick-view', 'OrderController@quick_view')->name('quick-view');
            Route::get('quick-view-cart-item', 'OrderController@quick_view_cart_item')->name('quick-view-cart-item');
            Route::post('initiate-refund', 'OrderController@initiateRefund')->name('initiate-refund');         
            Route::post('assign-lab', 'OrderController@assignLab')->name('assign-lab');

        });

        Route::group(['prefix' => 'dispatch', 'as' => 'dispatch.', 'middleware' => ['module:order']],function(){
            Route::get('list/{status}', 'OrderController@dispatch_list')->name('list');
        });

        Route::group(['prefix' => 'location', 'as' => 'zone.', 'middleware' => ['module:zone']], function () {
            Route::get('/', 'LocationController@index')->name('home');
            Route::post('store', 'LocationController@store')->name('store');
            Route::get('edit/{id}', 'LocationController@edit')->name('edit');
            Route::post('update/{id}', 'LocationController@update')->name('update');
            Route::delete('delete/{zone}', 'LocationController@destroy')->name('delete');
            Route::get('status/{id}/{status}', 'LocationController@status')->name('status');
            Route::post('search', 'LocationController@search')->name('search');
            Route::get('zone-filter/{id}', 'LocationController@zone_filter')->name('zonefilter');
            Route::get('get-all-zone-cordinates/{id?}', 'LocationController@get_all_zone_cordinates')->name('zoneCoordinates');
        });

        Route::group(['prefix' => 'notification', 'as' => 'notification.', 'middleware' => ['module:notification']], function () {
            Route::get('add-new', 'NotificationController@index')->name('add-new');
            Route::post('store', 'NotificationController@store')->name('store');
            Route::get('edit/{id}', 'NotificationController@edit')->name('edit');
            Route::post('update/{id}', 'NotificationController@update')->name('update');
            Route::get('status/{id}/{status}', 'NotificationController@status')->name('status');
            Route::delete('delete/{id}', 'NotificationController@delete')->name('delete');
        });

        Route::group(['prefix' => 'web-app-settings', 'as' => 'business-settings.', 'middleware' => ['module:settings','actch']], function () {
            Route::get('business-setup', 'BusinessSettingsController@business_index')->name('business-setup');
            Route::get('apis', 'BusinessSettingsController@config_setup')->name('config-setup');
            Route::post('config-update', 'BusinessSettingsController@config_update')->name('config-update');
            Route::post('update-setup', 'BusinessSettingsController@business_setup')->name('update-setup');
            Route::get('app-settings', 'BusinessSettingsController@app_settings')->name('app-settings2');
            Route::POST('app-settings', 'BusinessSettingsController@update_app_settings')->name('app-settings');
            Route::get('landing-page-settings/{tab?}', 'BusinessSettingsController@landing_page_settings')->name('landing-page-settings');
            Route::POST('landing-page-settings/{tab}', 'BusinessSettingsController@update_landing_page_settings')->name('add-landing-page-settings');
            Route::DELETE('landing-page-settings/{tab}/{key}', 'BusinessSettingsController@delete_landing_page_settings')->name('landing-page-settings-delete');


            Route::get('pages/terms-and-conditions', 'BusinessSettingsController@terms_and_conditions')->name('terms-and-conditions');
            Route::post('pages/terms-and-conditions', 'BusinessSettingsController@terms_and_conditions_update');

            Route::get('pages/privacy-policy', 'BusinessSettingsController@privacy_policy')->name('privacy-policy');
            Route::post('pages/privacy-policy', 'BusinessSettingsController@privacy_policy_update');

            Route::get('pages/about-us', 'BusinessSettingsController@about_us')->name('about-us');
            Route::post('pages/about-us', 'BusinessSettingsController@about_us_update');

             //recaptcha
            Route::get('recaptcha', 'BusinessSettingsController@recaptcha_index')->name('recaptcha_index');
            Route::post('recaptcha-update', 'BusinessSettingsController@recaptcha_update')->name('recaptcha_update');

        });


         Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.', 'middleware' => ['module:settings','actch']], function () {

            Route::get('toggle-settings/{key}/{value}', 'BusinessSettingsController@toggle_settings')->name('toggle-settings');

            Route::get('notification-setting', 'BusinessSettingsController@fcm_index')->name('fcm-index');
            Route::post('update-fcm', 'BusinessSettingsController@update_fcm')->name('update-fcm');

            Route::post('update-fcm-messages', 'BusinessSettingsController@update_fcm_messages')->name('update-fcm-messages');

            Route::get('mail-config', 'BusinessSettingsController@mail_index')->name('mail-config');
            Route::post('mail-config', 'BusinessSettingsController@mail_config');

            Route::get('payment-method', 'BusinessSettingsController@payment_index')->name('payment-method');
            Route::post('payment-method-update/{payment_method}', 'BusinessSettingsController@payment_update')->name('payment-method-update');

            Route::get('currency-add', 'BusinessSettingsController@currency_index')->name('currency-add');
            Route::post('currency-add', 'BusinessSettingsController@currency_store');
            Route::get('currency-update/{id}', 'BusinessSettingsController@currency_edit')->name('currency-update');
            Route::put('currency-update/{id}', 'BusinessSettingsController@currency_update');
            Route::delete('currency-delete/{id}', 'BusinessSettingsController@currency_delete')->name('currency-delete');

            Route::get('sms-module', 'SMSModuleController@sms_index')->name('sms-module');
            Route::post('sms-module-update/{sms_module}', 'SMSModuleController@sms_update')->name('sms-module-update');
            Route::post('remove-dynamic-time-slot', 'BusinessSettingsController@removeDynamicTimeSlot')->name('remove-dynamic-time-slot');
            Route::post('remove-dynamic-delivery-charge-slab', 'BusinessSettingsController@removeDynamicDeliveryChargeSlab')->name('remove-dynamic-delivery-charge-slab');

        });

        Route::group(['prefix' => 'message', 'as' => 'message.'], function () {
            Route::get('list', 'ConversationController@list')->name('list');
            Route::post('store/{user_id}', 'ConversationController@store')->name('store');
            Route::get('view/{user_id}', 'ConversationController@view')->name('view');
        });

        Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.'], function () {
            Route::get('get-account-data/{deliveryman}', 'DeliveryManController@get_account_data')->name('restaurantfilter');
            Route::group(['middleware' => ['module:deliveryman']], function () {
                Route::get('add', 'DeliveryManController@index')->name('add');
                Route::post('store', 'DeliveryManController@store')->name('store');
                Route::get('list', 'DeliveryManController@list')->name('list');
                Route::get('preview/{id}/{tab?}', 'DeliveryManController@preview')->name('preview');
                Route::get('status/{id}/{status}', 'DeliveryManController@status')->name('status');
                Route::get('earning/{id}/{status}', 'DeliveryManController@earning')->name('earning');
                Route::get('update-application/{id}/{status}', 'DeliveryManController@update_application')->name('application');
                Route::get('edit/{id}', 'DeliveryManController@edit')->name('edit');
                Route::post('update/{id}', 'DeliveryManController@update')->name('update');
                Route::delete('delete/{id}', 'DeliveryManController@delete')->name('delete');
                Route::post('search', 'DeliveryManController@search')->name('search');
                Route::get('get-deliverymen', 'DeliveryManController@get_deliverymen')->name('get-deliverymen');

                Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
                    Route::get('list', 'DeliveryManController@reviews_list')->name('list');
                    Route::get('status/{id}/{status}', 'DeliveryManController@reviews_status')->name('status');
                });
            });
        });


        Route::group(['prefix' => 'reviews', 'as' => 'reviews.', 'middleware' => ['module:customerList']], function () {
            Route::get('list', 'ReviewsController@list')->name('list');
            Route::post('search', 'ReviewsController@search')->name('search');
        });

       
       Route::group(['prefix' => 'report-and-analytics', 'as' => 'report.', 'middleware' => ['module:report']], function () {
            Route::get('order', 'ReportController@order_index')->name('order');
            Route::get('day-wise-report', 'ReportController@day_wise_report')->name('day-wise-report');
            Route::get('service-wise-report', 'ReportController@food_wise_report')->name('food-wise-report');
            Route::post('service-wise-report-search', 'ReportController@food_search')->name('food-wise-report-search');
            Route::get('order-transactions', 'ReportController@order_transaction')->name('order-transaction');
            Route::get('earning', 'ReportController@earning_index')->name('earning');
            Route::get('earning-export', 'ReportController@earning_export')->name('earning-export');
            Route::get('customer-report', 'ReportController@customer_index')->name('customer-report');
            Route::get('customer-report-export', 'ReportController@customer_export')->name('customer-report-export');
            Route::get('advisor-report', 'ReportController@advisor_index')->name('advisor-report');
            Route::get('advisor-report-export', 'ReportController@advisor_export')->name('advisor-report-export');
            Route::post('set-date', 'ReportController@set_date')->name('set-date');
        });


        Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['module:customerList']], function () {
            Route::get('list', 'CustomerController@list')->name('list');
            Route::get('view/{user_id}', 'CustomerController@view')->name('view');
            Route::post('search', 'CustomerController@search')->name('search');
            Route::get('status/{customer}/{status}', 'CustomerController@status')->name('status');
        });


        Route::group(['prefix' => 'file-manager', 'as' => 'file-manager.'], function () {
            Route::get('/download/{file_name}', 'FileManagerController@download')->name('download');
            Route::get('/index/{folder_path?}', 'FileManagerController@index')->name('index');
            Route::post('/image-upload', 'FileManagerController@upload')->name('image-upload');
            Route::delete('/delete/{file_path}', 'FileManagerController@destroy')->name('destroy');
        });


        Route::group(['prefix' => 'lab-test-category', 'as' => 'lab-test-category.'], function () {
            Route::group(['middleware' => ['module:lab-test-category']], function () {
                Route::get('add', 'LabTestCategoryController@index')->name('add-new');
                Route::get('list', 'LabTestCategoryController@list')->name('list');
                Route::post('store', 'LabTestCategoryController@store')->name('store');
                Route::get('edit/{id}', 'LabTestCategoryController@edit')->name('edit');
                Route::post('update/{id}', 'LabTestCategoryController@update')->name('update');
                Route::post('store', 'LabTestCategoryController@store')->name('store');
                Route::get('status/{id}/{status}', 'LabTestCategoryController@status')->name('status');
                Route::delete('delete/{id}', 'LabTestCategoryController@delete')->name('delete');
                Route::post('search', 'LabTestCategoryController@search')->name('search');
            });
        });


        Route::group(['prefix' => 'lab-test', 'as' => 'lab-test.'], function () {
            Route::group(['middleware' => ['module:lab-test']], function () {
                Route::get('add', 'LabTestController@index')->name('add-new');
                Route::get('list', 'LabTestController@list')->name('list');
                Route::post('store', 'LabTestController@store')->name('store');
                Route::get('edit/{id}', 'LabTestController@edit')->name('edit');
                Route::post('update/{id}', 'LabTestController@update')->name('update');
                Route::post('store', 'LabTestController@store')->name('store');
                Route::get('status/{id}/{status}', 'LabTestController@status')->name('status');
                Route::delete('delete/{id}', 'LabTestController@delete')->name('delete');
                Route::post('search', 'LabTestController@search')->name('search');
            });
        });

        Route::group(['prefix' => 'lab', 'as' => 'lab.'], function () {
            Route::group(['middleware' => ['module:lab']], function () {
                Route::get('add', 'LabController@index')->name('add-new');
                Route::get('list', 'LabController@list')->name('list');
                Route::post('store', 'LabController@store')->name('store');
                Route::get('edit/{id}', 'LabController@edit')->name('edit');
                Route::post('update/{id}', 'LabController@update')->name('update');
                Route::post('store', 'LabController@store')->name('store');
                Route::get('status/{id}/{status}', 'LabController@status')->name('status');
                Route::delete('delete/{id}', 'LabController@delete')->name('delete');
                Route::post('search', 'LabController@search')->name('search');
            });
        });



        //social media login
        // Route::group(['prefix' => 'social-login', 'as' => 'social-login.','middleware'=>['module:business_settings']], function () {
        //     Route::get('view', 'BusinessSettingsController@viewSocialLogin')->name('view');
        //     Route::post('update/{service}', 'BusinessSettingsController@updateSocialLogin')->name('update');

        // });
    });

    Route::get('zone/get-coordinates/{id}', 'LocationController@get_coordinates')->name('zone.get-coordinates');
});