<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\BlogController;
// use App\Http\Controllers\API\DiseaseController;




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

Route::group(['namespace' => 'API'], function () {
    Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
        Route::controller(UserAuthController::class)->group(function () {
            Route::post('register', 'register');
          Route::post('login', 'login');
          Route::post('login1', 'login1');

            Route::post('verify-otp', 'verifyOTP');
            //  Route::post('verify-otp1', 'verifyOTP1');
            // Route::post('resend-otp1', 'resendOTP1');
              Route::post('resend-otp', 'resendOTP');
            //-------------------Static Delete User api(rifaul)-------------------//
            Route::get('delete-user/{id}', 'deleteUser');
            

           
        });
        
    });
    
    Route::get('delete', [UserAuthController::class, 'delete']);
    
    Route::controller(AppointmentController::class)->group(function () {
        Route::get('download-medical-prescription', 'downloadMedicalPrescription');

    });
    
     
    
});



Route::group(['namespace' => 'API'], function () {

    Route::controller(SettingController::class)->group(function () {
        Route::get('get-setting-data', 'index');
    });
        // Route::controller(DiseaseController::class)->group(function () {
        //     Route::get('get-diseases', 'index');
        //     Route::get('get-type', 'search');
        // });


    Route::group(['prefix' => 'auth', 'namespace' => 'Auth', 'middleware' => 'custom.jwt'], function () {
        Route::controller(UserAuthController::class)->group(function () {
            Route::delete('delete-account', 'deleteAccount');
            Route::get('view-profile', 'getUserDetails');
            Route::post('update-profile', 'updateUserDetails');
            Route::post('logout', 'logout');

        });
    });

    Route::group(['middleware' => 'custom.jwt'], function () {


        Route::controller(UsersAddressController::class)->group(function () {
            Route::post('manage-delivery-address', 'manageAddress');
            Route::get('get-delivery-address', 'getAddress');
        });

        Route::controller(AllergyController::class)->group(function () {
            Route::get('get-all-allergies', 'index');
        });

        Route::controller(MedicationController::class)->group(function () {
            Route::get('get-all-medications', 'index');
            Route::get('get-medicine-detail', 'getMedicineDetail');
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

        Route::controller(SpecialityController::class)->group(function () {
            Route::get('get-all-specialities', 'index');
        });

        Route::controller(DiseaseController::class)->group(function () {
            Route::get('get-diseases', 'index');
            Route::get('get-type', 'search');
        });

        Route::controller(SettingController::class)->group(function () {
            Route::put('update-notification-setting', 'updateNotificationSetting');
            Route::get('get-payment-keys', 'getPaymentKeys');
            Route::get('get-order-settings', 'getOrderSettings');
            Route::post('manage-language-setting', 'manageLanuageSetting');
        });

        Route::controller(DoctorController::class)->group(function () {
            Route::get('get-doctors', 'getDoctorList');
            Route::get('get-doctor-detail', 'getDoctorDetail');
            Route::post('mark-doctor-as-favorite-unfavroite','markFavUnFavAsDoctor');
           Route::get('favorite-doctor-list','getTopFavoriteDoctors');
            Route::post('give-rate-review','giveRateReview');
            
             Route::get('get-SpecialitiesByDomain', 'getSpecialitiesByDomain');
               Route::get('get-online-adviser', 'getOnlineAdviser');
                  Route::get('top-favorites',  'getTopFavoriteDoctors');
        });
        

        Route::controller(ProductController::class)->group(function () {
            Route::get('get-product-list', 'getProductList');
            Route::get('product-detail', 'getProductDetail');
             Route::get('get-user-catogery', 'getUserCategoryList');
                Route::get('get-degrees-list', 'getDegrresList');
        });
        
                Route::controller(VendorController::class)->group(function () {
            Route::get('get-vendor-list', 'getVendorList');
        });
        

        Route::controller(CouponController::class)->group(function () {
            Route::get('get-dashboard-coupon-data', 'index');
            Route::get('get-all-coupons', 'getAllCoupons');
            Route::get('get-coupon-detail', 'getCouponDetail');
           
        });

        Route::controller(FAQController::class)->group(function () {
            Route::get('get-faq-data', 'index');
        });

        Route::controller(WalletController::class)->group(function () {
            Route::post('add-amount-in-wallet', 'addAmountInWallet');
            Route::get('get-wallet-transactions', 'getWalletTransactions');
        });

        Route::controller(CartController::class)->group(function () {
            Route::post('add-item-in-cart', 'addItemInCart');
            Route::get('get-cart-items', 'getCartItems');
            Route::delete('delete-cart-item', 'removeItemFromCart');
            Route::delete('empty-cart', 'emptyCart');
        });

        Route::controller(BookingController::class)->group(function () {
              Route::get('lab-list','leblist');
            Route::post('book-items','bookItems');
            Route::post('book-lab-test','bookLabTest');
            Route::put('cancel-item','cancelItem');
            Route::get('get-bookings','getBookings');
            Route::get('get-booking-detail','getBookingDetail');
             Route::get('get-userLab-test','getUserLabTests');
      
        });

        Route::controller(AppointmentController::class)->group(function () {
            Route::post('book-appointment','book');
            Route::get('get-appointments','getAppointments');
            Route::get('get-appointment-detail','getAppointmentDetail');
            Route::get('get-payment-detail','getPaymentDetail');
            Route::get('get-payment-logs', 'getPaymentLogs');
      
        });


        Route::controller(ConsultationController::class)->group(function () {
            Route::post('consult','consult');
            Route::get('get-consultations','getConsultations');
            Route::post('reply-to-patient-query','replyPatientQuery');
      
        });
        
        Route::get('blogs', [BlogController::class, 'Blogs']);

  //comment routes

   Route::post('patient/comments', [CommentController::class, 'addComment']);
    Route::get('/comments/appointment/{appointment_id}', [CommentController::class, 'getComments']); 
    Route::get('/doctor/{doctor_id}/comments', [CommentController::class, 'getDoctorComments']); 
    Route::put('/comments/{comment_id}', [CommentController::class, 'updateComment']);
    Route::delete('/comments/{comment_id}', [CommentController::class, 'deleteComment']);


    

        Route::controller(NotificationController::class)->group(function () {
            Route::get('get-notifications', 'index');
            Route::get('get-unread-notification-count', 'getUnreadNotificationsCount');
            Route::post('send-notification-of-completed-order', 'sendNotificationOfCompletedOrder');
            Route::post('send-notification-of-due-amount', 'sendNotificationOfDueAmountPending');
        });

        Route::controller(VideoCallController::class)->group(function () {
            Route::post('generate-token', 'generateToken');
             Route::get('get-request', 'getRequest');
            Route::post('end-call', 'endCall');
             Route::get('instant-call-count','getInstantCallCount');
              Route::get('user-call-history', 'getUserCallHistory');

        });
        
         Route::controller(ChatController::class)->group(function () {
         Route::post('chat/start','startChat');
    Route::post('chat/end',  'endChat');
 });

        Route::controller(LabTestCategoryController::class)->group(function () {
            Route::get('get-all-lab-test-categories', 'index');
        });

        Route::controller(LabTestController::class)->group(function () {
            Route::get('get-lab-test-list', 'getLabTestList');
            Route::get('get-lab-test-detail', 'getLabTestDetail');
        });
        
        
        
           Route::controller('LiveStreamController')->group(function () {
            Route::post('agora/rtc-token', 'generateRtcToken');
            Route::post('agora/rtm-token', 'generateRtmToken');
            Route::post('live/start', 'startLive');
            Route::post('live/end', 'endLive');
            Route::get('live/active', 'getActiveLives');
            Route::post('live/join', 'joinLive');
            Route::post('live/leave', 'leaveLive');
        });

        // Advisor reels: advisor-only management (upload, list own, delete, performance)
        Route::controller(AdvisorReelController::class)->group(function () {
            Route::post('add-reel', 'addReel');
            Route::get('get-reels', 'getReels');
            Route::delete('delete-reel', 'deleteReel');
            Route::get('get-reel-performance', 'getReelPerformance');
        });

        // Public reel interactions: available to any authenticated user
        Route::controller(ReelController::class)->group(function () {
            Route::get('reel-categories', 'getCategories');
            Route::get('reel-list', 'reelList');
            Route::get('reel-detail', 'reelDetail');
            Route::post('increment-reel-view', 'incrementReelView');
            Route::post('add-reel-value', 'addReelValue');
            Route::post('add-reel-share', 'addReelShare');
            Route::post('add-reel-comment', 'addComment');
            Route::get('get-reel-comments', 'getComments');
            Route::delete('delete-reel-comment', 'deleteComment');
            Route::post('add-reel-reply', 'addReelReply');
            Route::get('get-reel-replies', 'getReelReplies');
        });

    });
});

