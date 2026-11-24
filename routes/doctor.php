<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Doctor', 'as' => 'doctor.'], function () {
    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit')->middleware('actch');
        Route::get('verify-otp', 'LoginController@verifyotp')->name('verify-otp');
        Route::post('verify-otp', 'LoginController@submitotp')->name('sumbit-otp');
        Route::get('resend-otp', 'LoginController@resendOtp')->name('resend-otp');


        Route::get('logout', 'LoginController@logout')->name('logout');
    });
    /*authentication*/

    Route::group(['middleware' => ['doctor']], function () {

        Route::get('lab-test-booking-list', 'OrderController@labTestBookinglist')->name('lab-test-booking-list');
        Route::get('booking-detail', 'OrderController@labTestBookingDetail')->name('booking-detail');
        Route::get('generate-invoice/{id}', 'OrderController@generateInvoiceForLabTest')->name('generate-invoice');
        Route::get('download-invoice/{id}', 'OrderController@downloadInvoiceForLabTest')->name('download-invoice');
        Route::get('search-lab-test', 'OrderController@searchLabTest')->name('search-lab-test');

        Route::get('appointment-list/{status}', 'AppointmentController@list')->name('appointment-list');
        Route::get('search-appointment', 'AppointmentController@search')->name('search-appointment');
        Route::get('settings', 'SystemController@settings')->name('settings');
        Route::post('settings', 'SystemController@settings_update');
        Route::post('settings-password', 'SystemController@settings_password_update')->name('settings-password');
        Route::get('/get-restaurant-data', 'SystemController@restaurant_data')->name('get-restaurant-data');
        Route::post('add-slots','AppointmentController@addSlots');
        Route::get('get-slots','AppointmentController@mgetSlots');
        Route::get('add-slots','AppointmentController@addSlot');
        Route::get('edit-slot/{id}','AppointmentController@editSlot');
        Route::post('update-slots','AppointmentController@updateSlots');
        Route::delete('delete-slot/{id}','AppointmentController@deleteSlot');
        
         Route::post('filter', 'AppointmentController@filter')->name('appointmentfilter');
          Route::get('filter/reset', 'AppointmentController@filter_reset');
        //------------------------Rifaul changes-------------------------------//
        Route::delete('/appointment/{id}', 'AppointmentController@destroy')->name('appointment.destroy');

        Route::get('slot-list','AppointmentController@slotList')->name('slot-list');
        Route::get('manage-appointment-status/{id}/{status}','AppointmentController@manageAppointmentStatus');


        
        //dashboard
        Route::get('/', 'DashboardController@dashboard')->name('dashboard');
        


        
        Route::group(['prefix' => 'dashboard-stats', 'as' => 'dashboard-stats.'], function () {
            Route::post('order', 'DashboardController@order')->name('order');
            Route::post('year-wise-data', 'DashboardController@yearWiseData')->name('year-wise-data');
            Route::post('user-overview', 'DashboardController@user_overview')->name('user-overview');
            Route::post('business-overview', 'DashboardController@business_overview')->name('business-overview');
        });



       
        Route::group(['prefix' => 'patient', 'as' => 'patient.'], function () {
            Route::get('add-new', 'CustomerController@add_new')->name('add-new');
            Route::post('add-new', 'CustomerController@store');
            Route::get('list', 'CustomerController@list')->name('list');
            Route::get('update/{id}', 'CustomerController@edit')->name('edit');
            Route::post('update/{id}', 'CustomerController@update')->name('update');
            Route::delete('delete/{id}', 'CustomerController@distroy')->name('delete');
            Route::post('search', 'CustomerController@search')->name('search');
        });


        Route::group(['prefix' => 'doctor', 'as' => 'doctor.'], function () {
            Route::get('add-new', 'DoctorController@add_new')->name('add-new');
            Route::post('add-new', 'DoctorController@store');
            Route::get('list', 'DoctorController@list')->name('list');
            Route::get('update/{id}', 'DoctorController@edit')->name('edit');
            Route::post('update/{id}', 'DoctorController@update')->name('update');
            Route::delete('delete/{id}', 'DoctorController@distroy')->name('delete');
            Route::post('search', 'DoctorController@search')->name('search');
        });




        Route::group(['prefix' => 'booking', 'as' => 'order.', 'middleware' => ['module:order']], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::get('status', 'OrderController@status')->name('status');
            // Route::put('status-update/{id}', 'OrderController@status')->name('status-update');
            Route::get('download-invoice/{id}', 'OrderController@downloadInvoice')->name('download-invoice');
            Route::get('view/{id}', 'OrderController@view')->name('view');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice')->name('generate-invoice');
            Route::post('add-payment-ref-code/{id}', 'OrderController@add_payment_ref_code')->name('add-payment-ref-code');
            Route::get('restaurant-filter/{restaurant_id}', 'AppointmentController@restaurnt_filter')->name('restaurant-filter');
            // Route::get('filter/reset', 'AppointmentController@filter_reset');
            // Route::post('filter', 'AppointmentController@filter')->name('appointmentfilter');
            Route::get('search', 'OrderController@search')->name('search');
            Route::post('initiate-refund', 'OrderController@initiateRefund')->name('initiate-refund');

         
          


        });

    });

});
