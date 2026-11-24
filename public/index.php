<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));


// // --- Deep Link Redirect Logic ---
// $requestUri = $_SERVER['REQUEST_URI'] ?? '';
// $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// // Check if it's an App Deep Link URL (e.g., doctor profile or blog)
// // Make sure it's not an API call or admin/doctor dashboard route
// if (
//     strpos($requestUri, '/api/') === false &&
//     strpos($requestUri, '/admin/') === false &&
//     strpos($requestUri, '/doctor/auth/') === false &&
//     strpos($requestUri, '/vendor/') === false &&
//     strpos($requestUri, '/lab/') === false
// ) {
//     if (strpos($requestUri, '/doctor') !== false || strpos($requestUri, '/blog') !== false) {
//         $isAndroid = stripos($userAgent, 'Android') !== false;
//         if ($isAndroid) {
//             $playStoreUrl = "https://play.google.com/store/apps/details?id=com.consultkaar.userapp";
//             header("Location: " . $playStoreUrl);
//             exit;
//         }
//     }
// }

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists(__DIR__.'/../storage/framework/maintenance.php')) {
    require __DIR__.'/../storage/framework/maintenance.php';
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

// $response = tap($kernel->handle(
//     $request = Request::capture()
// ))->send();

$request = Request::capture();

// --- Deep Link Redirect Logic ---
// We use Laravel's request object to properly parse everything including URLs like /doctor?id=522
if ($request->is('doctor*') || $request->is('blog*')) {
    // Only intercept if 'id' is present (which means it's a shared profile link, not the doctor dashboard login itself)
    if ($request->has('id')) {
        if (preg_match('/android/i', $request->header('User-Agent'))) {
            header("Location: https://play.google.com/store/apps/details?id=com.consultkaar.userapp");
            exit;
        }
    }
}
// --- End Deep Link Redirect Logic ---

$response = tap($kernel->handle($request))->send();

$kernel->terminate($request, $response);
