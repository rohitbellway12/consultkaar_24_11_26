<?php

use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Doctor\DomainController;
use App\Http\Controllers\API\Doctor\AppointmentController;
use App\Http\Controllers\API\Doctor\Auth\AuthController;

use App\Http\Controllers\API\Doctor\Speciality\SpecialityController;
use App\Http\Controllers\API\Doctor\UsersAddress\UsersAddressController;
use App\Http\Controllers\API\Doctor\Allergy\AllergyController;
use App\Http\Controllers\API\Doctor\Medication\MedicationController;
use App\Http\Controllers\API\Doctor\Injury\InjuryController;
use App\Http\Controllers\API\Doctor\Surgery\SurgeryController;
use App\Http\Controllers\API\Doctor\Category\CategoryController;
use App\Http\Controllers\API\Doctor\ProductCategory\ProductCategoryController;
use App\Http\Controllers\API\Doctor\Banner\BannerController;
use App\Http\Controllers\API\Doctor\Location\LocationController;
use App\Http\Controllers\API\Doctor\BankController;
use App\Http\Controllers\API\Doctor\BlogController;
use App\Http\Controllers\API\Doctor\Disease\DiseaseController;
use App\Http\Controllers\API\Doctor\DoctorController;
use App\Http\Controllers\API\Doctor\Product\ProductController;
use App\Http\Controllers\API\Doctor\Coupon\CouponController;
use App\Http\Controllers\API\Doctor\FAQ\FAQController;
use App\Http\Controllers\API\Doctor\Cart\CartController;
use App\Http\Controllers\API\Doctor\Booking\BookingController;

use App\Http\Controllers\API\Doctor\Consultation\ConsultationController;
use App\Http\Controllers\API\Doctor\NotificationController;
use App\Http\Controllers\API\Doctor\VideoCallController;
use App\Http\Controllers\API\Doctor\ChatController;
use App\Http\Controllers\API\Doctor\LabTestCategory\LabTestCategoryController;
use App\Http\Controllers\API\Doctor\LabTest\LabTestController;
use App\Http\Controllers\API\Doctor\SettingController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('login', 'login');
            Route::post('verify-otp', 'verifyOTP');
            Route::post('resend-otp', 'resendOTP');
           Route::post('online-status','OnlineStatus');
        });
    });
});

    
    Route::post('comment-visibility', [AuthController::class, 'updateCommentVisibility']);
     Route::group([], function () {

    Route::controller(SettingController::class)->group(function () {
        Route::get('get-setting-data', 'index');
    });
         Route::controller(SpecialityController::class)->group(function () {
            Route::get('get-all-specialities', 'index');
     Route::get('experts', [SpecialityController::class, 'listFive']);
        });


    Route::get('/domains', [DomainController::class, 'getDomains']);
    Route::get('/specializations', [DomainController::class, 'getSpecializations']);
    Route::get('/countries', [DomainController::class, 'getCountries']);
    Route::get('/states', [DomainController::class, 'getStates']);
    Route::get('/cities', [DomainController::class, 'getCities']);




    Route::group(['prefix' => 'auth', 'middleware' => 'custom.jwt'], function () {
        Route::controller(AuthController::class)->group(function () {
            Route::delete('delete-account', 'deleteAccount');
            Route::get('view-profile', 'getProfileDetails');
            Route::post('complete-profile', 'completeProfile');
            Route::post('update-profile', 'updateProfileDetails');
            Route::post('logout', 'logout');
        });
        Route::controller(DoctorController::class)->group(function () {
            Route::get('get-identity-proofs', 'getIdentityProofs');
            Route::get('get-relevant-documents', 'getRelevantDocuments'); // supports ?domain_id=X
            Route::get('get-profile-status', 'getProfileStatus'); // New: Get approval status
            Route::get('favorite-count/{doctor_id}', 'doctorFavoriteCount');
           
             Route::get('top-favorites',  'getTopFavoriteDoctors');
            
        });
    });

        Route::group(['middleware' => ['custom.jwt', 'doctor.approval']], function () {


        Route::controller(UsersAddressController::class)->group(function () {
            Route::post('manage-delivery-address', 'manageAddress');
            Route::get('get-delivery-address', 'getAddress');
        });

        Route::controller(AllergyController::class)->group(function () {
            Route::get('get-all-allergies', 'index');
        });

        Route::controller(MedicationController::class)->group(function () {
            Route::get('get-all-medications', 'index');
        });

        Route::controller(InjuryController::class)->group(function () {
            Route::get('get-all-injuries', 'index');
        });

        Route::controller(SurgeryController::class)->group(function () {
            Route::get('get-all-surgeries', 'index');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('get-all-categories', 'index');
        });

        Route::controller(ProductCategoryController::class)->group(function () {
            Route::get('get-all-product-categories', 'index');
        });

        Route::controller(BannerController::class)->group(function () {
            Route::get('get-all-banners', 'index');
        });

        Route::controller(LocationController::class)->group(function () {
            Route::get('get-all-locations', 'index');
        });
        
        
        
        Route::controller(BankController::class)->group(function () {
            Route::post('manage-bank-details', 'manageBankDetails');
            Route::get('get-bank-detail', 'getBankDetail');

        });
        
        // Route::controller(BlogController::class)->group(function(){
             Route::post('/blogs', [BlogController::class, 'store']);
    Route::get('/my-blogs', [BlogController::class, 'myBlogs']);
    Route::get('/blogs/{id}/edit', [BlogController::class, 'edit']);
    Route::post('/blogs/{id}', [BlogController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
        // });

       //comment visibility
  
    // --- Doctor Settings Route ---
 

        Route::controller(DiseaseController::class)->group(function () {
            Route::get('get-diseases', 'index');
        });

        Route::controller(SettingController::class)->group(function () {
            Route::put('update-notification-setting', 'updateNotificationSetting');
            Route::get('get-payment-keys', 'getPaymentKeys');
            Route::get('get-order-settings', 'getOrderSettings');
            Route::post('manage-language-setting', 'manageLanuageSetting');
        });

        Route::controller(DoctorController::class)->group(function () {
            Route::post('set-doctor-availablity','setDoctorAvailablity');
        });
        

        Route::controller(ProductController::class)->group(function () {
            Route::get('get-product-list', 'getProductList');
            Route::get('product-detail', 'getProductDetail');

           
        });

        Route::controller(CouponController::class)->group(function () {
            Route::get('get-dashboard-coupon-data', 'index');
            Route::get('get-all-coupons', 'getAllCoupons');
            Route::get('get-coupon-detail', 'getCouponDetail');
           
        });

        Route::controller(FAQController::class)->group(function () {
            Route::get('get-faq-data', 'index');
        });

        Route::controller(CartController::class)->group(function () {
            Route::post('add-item-in-cart', 'addItemInCart');
            Route::get('get-cart-items', 'getCartItems');
            Route::delete('delete-cart-item', 'removeItemFromCart');
            Route::delete('empty-cart', 'emptyCart');
        });

        Route::controller(BookingController::class)->group(function () {
            Route::post('book-items','bookItems');
            Route::post('book-lab-test','bookLabTest');
            Route::get('get-bookings','getBookings');
            Route::get('get-booking-detail','getBookingDetail');
      
        });

        Route::controller(AppointmentController::class)->group(function () {
            Route::post('book-appointment','book');
            Route::get('get-appointments','getAppointments');
            Route::get('get-earning-report','getEarningReport');
            Route::get('get-earning-report-graph','getEarningReportGraph');
            Route::get('get-appointment-detail','getAppointmentDetail');
            Route::post('manage-appointment-status','manageAppointmentStatus');
            Route::get('get-total-earnings','getTotalEarnings');
            Route::get('get-total-orders-and-earrnings','getTotalOrdersAndEarnings');
            Route::get('get-total-appointments','getTotalAppointments');
            Route::post('add-medicines', 'addMedicines');
            Route::get('get-wallet-transactions', 'getWalletTransactions');
            Route::get('get-payment-logs', 'getPaymentLogs');
            Route::post('send-withdrawal-request', 'sendWithDrawalRequest');
            Route::get('get-pending-withdrawal-requests', 'getPendingWithDrawalRequests');
            Route::post('withdraw-wallet-amount', 'withDrawWalletAmount');
        });


        Route::controller(ConsultationController::class)->group(function () {
            Route::post('consult','consult');
            Route::get('get-consultations','getConsultations');
            Route::post('reply-to-patient-query','replyPatientQuery');
      
        });
    

        Route::controller(NotificationController::class)->group(function () {
            Route::get('get-notifications', 'index');
            Route::get('get-unread-notification-count', 'getUnreadNotificationsCount');
            Route::post('send-notification-of-completed-order', 'sendNotificationOfCompletedOrder');
            Route::post('send-notification-of-due-amount', 'sendNotificationOfDueAmountPending');
        });

        Route::controller(VideoCallController::class)->group(function () {
            Route::post('generate-token', 'generateToken');
            Route::post('end-call', 'endCall');
            Route::get('get-call-history', 'getCallHistory');
            Route::post('manage-call-request', 'manageCallRequest');
            Route::get('instant-call-count','getInstantCallCount');


        });

   Route::controller(ChatController::class)->group(function () {
        Route::get('chat/incoming','incomingRequests');      // Pending chat requests
        Route::post('chat/manage','manageChatRequest');     // Accept / Reject
        Route::post('chat/end','endChat');                  // End chat
        Route::get('chat/history','chatHistory');           // History
    });

        Route::controller(LabTestCategoryController::class)->group(function () {
            Route::get('get-all-lab-test-categories', 'index');
        });

        Route::controller(LabTestController::class)->group(function () {
            Route::get('get-lab-test-list', 'getLabTestList');

           
        });

    });
});

