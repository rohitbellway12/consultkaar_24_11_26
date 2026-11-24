<?php

// namespace App\Http\Controllers\Lab;
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
   public function dashboard(Request $request)
{
    $vendorId = Auth::guard('vendor')->id();

    $data = [
        'total_orders' => Order::where('vendor_id', $vendorId)->count(),
        'total_categories' => ProductCategory::count(),
        'total_products' => Product::where('user_id', $vendorId)->count(),
    ];
// dd($data['total_categories']);
    return view('vendor-views.dashboard', compact('data'));
}

  function list(Request $request)
    {
        $categories = DB::table('product_categories')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, cid FROM product_categories GROUP BY cid) as sub'), function ($join) {
            $join->on('product_categories.created_at', '=', 'sub.min_created_at')
                 ->on('product_categories.cid', '=', 'sub.cid');
        })->orderBy('created_at','desc')->get();
        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Change this as needed
        $items = collect($categories);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

        $categories = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        // Set path for pagination links
        $categories->withPath($request->url());
        return view('vendor-views.product-category.list',compact('categories'));
    }

}
