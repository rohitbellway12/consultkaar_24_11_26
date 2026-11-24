<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\MedicionVendor;
use App\Models\Product;
use App\CentralLogics\Helpers;
use Illuminate\Http\Request;
use DB;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Pagination\LengthAwarePaginator;
 use Illuminate\Support\Facades\Storage;
 use Brian2694\Toastr\Facades\Toastr;

class MedicionVendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
//     public function index(Request $request)
// {
//     $category_id = $request->query('category_id', 'all');
//     $vendorId = Auth::guard('vendor')->id(); // Get the logged-in vendor's ID

//     // Fetch products only for the logged-in vendor
//     $productsQuery = DB::table('products')
//         ->select('products.*')
//         ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, pid FROM products GROUP BY pid) as sub'), function ($join) {
//             $join->on('products.created_at', '=', 'sub.min_created_at')
//                  ->on('products.pid', '=', 'sub.pid');
//         })
//         ->where('products.user_id', $vendorId)
//         ->orderBy('products.created_at', 'desc');

//     $products = $productsQuery->get();

//     // Manual Pagination
//     $page = LengthAwarePaginator::resolveCurrentPage();
//     $perPage = 10; // Adjust as needed
//     $items = collect($products);
//     $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

//     $products = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
//     $products->withPath($request->url());

//     // Fetch category details if a specific category is selected
//     $category = ($category_id !== 'all') ? ProductCategory::find($category_id) : null;

//     return view('vendor-views.medicion.list', compact('products', 'category'));
// }
public function index(Request $request)
{
    $category_id = $request->query('category_id', 'all');
    $search = $request->query('search');
    $vendorId = Auth::guard('vendor')->id();

    // Start query from Eloquent model if possible
    $productsQuery = Product::select('products.*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, pid FROM products GROUP BY pid) as sub'), function ($join) {
            $join->on('products.created_at', '=', 'sub.min_created_at')
                 ->on('products.pid', '=', 'sub.pid');
        })
        ->where('products.user_id', $vendorId);

    // Apply search if present
    if (!empty($search)) {
        $keywords = explode(' ', $search);
        $productsQuery->where(function ($query) use ($keywords) {
            foreach ($keywords as $word) {
                $query->where('products.name', 'like', "%{$word}%");
            }
        });
    }

    $products = $productsQuery->orderBy('products.created_at', 'desc')->paginate(10);

    $products->appends($request->query()); // Retain search/category in pagination links

    $category = ($category_id !== 'all') ? ProductCategory::find($category_id) : null;

    return view('vendor-views.medicion.list', compact('products', 'category'));
}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $categories =  DB::table('product_categories')->where('lang_code','en')->where('status',1)->get();  
        $french_categories =  DB::table('product_categories')->where('lang_code','fr')->where('status',1)->get(); 

        $product_types =  DB::table('product_types')->where('lang_code','en')->where('status',1)->get(); 
        $french_product_types =  DB::table('product_types')->where('lang_code','fr')->where('status',1)->get(); 
        
       
        return view('vendor-views.medicion.create', compact('categories','product_types','french_categories','french_product_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   

public function store(Request $request)
{
    // dd($request->all());
    $langName = $request->lang_name;
    $vendorId = Auth::guard('vendor')->id(); 

    if ($langName == 'en') {
        $request->validate([
            'name_en' => 'required|regex:/^[A-Za-z\s]+$/',
            'image' => 'required',
            'price_en' => 'required|numeric|min:.01',
        ]);
    } else {
        $request->validate([
            'name_fr' => 'required|regex:/^[A-Za-z\s]+$/',
            'image' => 'required',
            'price_fr' => 'required|numeric|min:.01',
        ]);
    }

    if ($langName == 'en') {
        $nameExist = DB::table('products')->where('name', $request->name_en)->exists();

        if ($request->manufacturing_date_en > $request->expiry_date_en) {
            return response()->json([
                'status' => 'error', 
                'code' => 200, 
                'message' => 'Expiry date should be greater than manufacturing date'
            ]);
        }
    } else {
        $nameExist = DB::table('products')->where('name', $request->name_fr)->exists();

        if ($request->manufacturing_date_fr > $request->expiry_date_fr) {
            return response()->json([
                'status' => 'error', 
                'code' => 200, 
                'message' => 'Expiry date should be greater than manufacturing date'
            ]);
        }
    }

    if ($nameExist) {
        Toastr::error('Product Name already exists');
        return redirect()->route('admin.doctor.add-new');
    }

    $Product = new Product;
    $Product->user_id = $vendorId; 

    if ($langName == 'en') {
        $Product->name = $request->name_en;
        $Product->category_id = $request->category_id_en;
        $Product->description = $request->description_en;
        $Product->total_stock = $request->total_stock_en;
        $Product->product_type_id = $request->product_type_en;
        $Product->unit_weight = $request->unit_weight_en;
       $Product->unit_pieces = $request->total_pieces_en; 
        $Product->unit = $request->unit_en;
        $Product->batch_number = $request->batch_number_en;
        $Product->manufacturing_date = $request->manufacturing_date_en;
        $Product->expiry_date = $request->expiry_date_en;
        $Product->manufactured_by = $request->manufactured_by_en;
        $Product->price = $request->price_en;
        $Product->gst = $request->gst_en;
    } else {
        $Product->name = $request->name_fr;
        $Product->category_id = $request->category_id_fr;
        $Product->description = $request->description_fr;
        $Product->total_stock = $request->total_stock_fr;
        $Product->product_type_id = $request->product_type_fr;
        $Product->unit_weight = $request->unit_weight_fr;
         $product->unit_pieces = $request->total_pieces_fr;
        $Product->unit = $request->unit_fr;
        $Product->batch_number = $request->batch_number_fr;
        $Product->manufacturing_date = $request->manufacturing_date_fr;
        $Product->expiry_date = $request->expiry_date_fr;
        $Product->manufactured_by = $request->manufactured_by_fr;
        $Product->price = $request->price_fr;
        $Product->gst = $request->gst_fr;
    }

    $Product->lang_code = $langName;

    // Handle product images
    $img_names = [];
    if (!empty($request->file('product_images'))) {
        foreach ($request->file('product_images') as $img) {
            $image_name = Helpers::upload('product/', 'png', $img);
            array_push($img_names, $image_name);
        }
    }
    $Product->image = Helpers::upload('product/', 'png', $request->file('image'));
    $Product->images = $img_names;
    $Product->save();

    $productId = $Product->id;

    DB::table('products')->where(['id' => $productId])->update([
        'pid' => "p" . $productId
    ]);

    return response()->json([
        'status' => 'success', 
        'code' => 200, 
        'message' => 'Product added successfully'
    ]);
}


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MedicionVendor  $medicionVendor
     * @return \Illuminate\Http\Response
     */
     
    public function show($id)
    {
         $product = Product::where(['id' => $id])->first();
        $product_fr = Product::where(['pid' => "p".$id,'lang_code' => 'fr'])->first();
        return view('vendor-views.medicion.view', compact('product','product_fr'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MedicionVendor  $medicionVendor
     * @return \Illuminate\Http\Response
     */
     public function edit($id)
    {
        $product = Product::find($id);
        $product_fr = Product::where(['pid' => "p".$product->id,'lang_code' => 'fr'])->first();
      
        if(!$product)
        {
            Toastr::error(trans('messages.Product').' '.trans('messages.not_found'));
            return back();
        }

        $categories =  DB::table('product_categories')->where('lang_code','en')->where('status',1)->get();  
        $french_categories =  DB::table('product_categories')->where('lang_code','fr')->where('status',1)->get(); 



        $product_types =  DB::table('product_types')->where('lang_code','en')->where('status',1)->get(); 
        $french_product_types =  DB::table('product_types')->where('lang_code','fr')->where('status',1)->get();

        return view('vendor-views.medicion.edit', compact('product', 'categories','product_types','french_categories','french_product_types','product_fr'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MedicionVendor  $medicionVendor
     * @return \Illuminate\Http\Response
     */
       public function remove_image(Request $request)
    {
        if (Storage::disk('public')->exists('product/' . $request['name'])) {
            Storage::disk('public')->delete('product/' . $request['name']);
        }
        $item = Product::find($request['id']);
        $array = [];
        
        foreach ($item['images'] as $image) {
            if ($image != $request['name']) {
                array_push($array, $image);
            }
        }
        Product::where('id', $request['id'])->update([
            'images' => json_encode($array),
        ]);
        Toastr::success("Product image removed successfully");
        return back();
    }
  

    // public function update(Request $request, $id)
    // {

    //   $langName = $request->lang_code;
    

    //     if($langName == 'en'){
    //         $request->validate([
    //             'name_en' => 'required|regex:/^[A-Za-z\s]+$/',
    //             'price_en' => 'required|numeric|min:.01',
    //         ]);
    //     } else {
    //         $request->validate([
    //             'name_fr' => 'required|regex:/^[A-Za-z\s]+$/',
    //             'price_fr' => 'required|numeric|min:.01',
    //         ]);
    //     }

    //     if($request->manufacturing_date > $request->expiry_date){
    //         return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Expiry date should be greater than manufacturing date']);
    //     }

    //     $p = Product::find($id);
    //     $p->name = $request['name'];
    //     $category = [];        

    //     $p->category_id = $request->category_id;
    //     $p->description = $request->description;
    //     $item_images = $p['images'];
    //     $images = [];    
        

    //     if ($request->has('product_images')){
    //         foreach ($request->product_images as $img) {
    //             $image = Helpers::upload('product/', 'png', $img);
    //             array_push($images, $image);
    //         }
    //     } 

    //     $p->images = array_merge($images,$item_images);
    //     $img_names = [];
    //     // dd($img_names);
    //         // Check if the record already exists
    //     $product = DB::table('products')->where('pid', "p".$id)->where('lang_code', $langName)->first();
    //      if($langName == 'en'){
    //         if ($product) {
    //             // Update the existing record
    //             DB::table('products')
    //                 ->where('pid', "p".$id)
    //                 ->where('lang_code', $langName)
    //                 ->update([
    //                     'name' => $request->name_en,
    //                     'description' => $request->description_en,
    //                     'category_id' => $request->category_id_en,
    //                     'price' => $request->price_en,
    //                     'image' => $request->has('image') ? Helpers::update('product/', $product->image, 'png', $request->file('image')) : $product->image,
    //                     'images'=>$img_names,
    //                     'total_stock' => $request->total_stock_en,
    //                     'product_type_id' => $request->product_type_en,
    //                     'unit_weight' => $request->unit_weight_en,
    //                     'unit_pieces' => $request->total_pieces_en,
    //                     'unit' => $request->unit_en,
    //                     'total_stock' => $request->total_stock_en,
    //                     'batch_number' => $request->batch_number_en,
    //                     'manufacturing_date' => $request->manufacturing_date_en,
    //                     'expiry_date' => $request->expiry_date_en,
    //                     'manufactured_by' => $request->manufactured_by_en,
    //                     'gst' => $request->gst_en,
    //                     'updated_at' => now(),
    //                 ]);
    //         } else {
    //             // Insert a new record
    //             DB::table('products')->insert([
    //                 'pid' => "p".$id,
    //                 'lang_code' => $langName,
    //                 'name' => $request->name_en,
    //                 'description' => $request->description_en,
    //                 'category_id' => $request->category_id_en,
    //                 'price' => $request->price_en,
    //                 'image' => $request->has('image') ? Helpers::update('product/', null, 'png', $request->file('image')) : null,
    //                  'images'=>$img_names,
    //                 'total_stock' => $request->total_stock_en,
    //                 'product_type_id' => $request->product_type_en,
    //                 'unit_weight' => $request->unit_weight_en,
    //                 'unit_pieces' => $request->total_pieces_en,
    //                 'unit' => $request->unit_en,
    //                 'total_stock' => $request->total_stock_en,
    //                 'batch_number' => $request->batch_number_en,
    //                 'manufacturing_date' => $request->manufacturing_date_en,
    //                 'expiry_date' => $request->expiry_date_en,
    //                 'manufactured_by' => $request->manufactured_by_en,
    //                 'gst' => $request->gst_en,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //         }
             
    //      } else {
    //           if ($product) {
    //             // Update the existing record
    //             DB::table('products')
    //                 ->where('pid', "p".$id)
    //                 ->where('lang_code', $langName)
    //                 ->update([
    //                     'name' => $request->name_fr,
    //                     'description' => $request->description_fr,
    //                     'category_id' => $request->category_id_fr,
    //                     'price' => $request->price_fr,
    //                     'image' => $request->has('image') ? Helpers::update('product/', $productFr->image, 'png', $request->file('image')) : $productFr->image,
    //                      'images'=>$img_names,
    //                     'total_stock' => $request->total_stock_fr,
    //                     'product_type_id' => $request->product_type_fr,
    //                     'unit_weight' => $request->unit_weight_fr,
    //                     'unit_pieces' => $request->total_pieces_fr,
    //                     'unit' => $request->unit_fr,
    //                     'total_stock' => $request->total_stock_fr,
    //                     'batch_number' => $request->batch_number_fr,
    //                     'manufacturing_date' => $request->manufacturing_date_fr,
    //                     'expiry_date' => $request->expiry_date_fr,
    //                     'manufactured_by' => $request->manufactured_by_fr,
    //                     'gst' => $request->gst_fr,
    //                     'updated_at' => now(),
    //                 ]);
    //         } else {
    //             // Insert a new record
    //             DB::table('products')->insert([
    //                 'pid' => "p".$id,
    //                 'lang_code' => $langName,
    //                 'name' => $request->name_fr,
    //                 'description' => $request->description_fr,
    //                 'category_id' => $request->category_id_fr,
    //                 'price' => $request->price_fr,
    //                 'image' => $request->has('image') ? Helpers::update('product/', null, 'png', $request->file('image')) : null,
    //                  'images'=>$img_names,
    //                 'total_stock' => $request->total_stock_fr,
    //                 'product_type_id' => $request->product_type_fr,
    //                 'unit_weight' => $request->unit_weight_fr,
    //                 'unit_pieces' => $request->total_pieces_fr,
    //                 'unit' => $request->unit_fr,
    //                 'total_stock' => $request->total_stock_fr,
    //                 'batch_number' => $request->batch_number_fr,
    //                 'manufacturing_date' => $request->manufacturing_date_fr,
    //                 'expiry_date' => $request->expiry_date_fr,
    //                 'manufactured_by' => $request->manufactured_by_fr,
    //                 'gst' => $request->gst_fr,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //         }
    //      }
    //     return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Product updated successfully']);

        
    // }

public function update(Request $request, $id)
{
    $langName = $request->lang_code;

    // Validation based on language
    if($langName == 'en'){
        $request->validate([
            'name_en' => 'required|regex:/^[A-Za-z\s]+$/',
            'price_en' => 'required|numeric|min:.01',
        ]);
    } else {
        $request->validate([
            'name_fr' => 'required|regex:/^[A-Za-z\s]+$/',
            'price_fr' => 'required|numeric|min:.01',
        ]);
    }

    // Date validation
    if ($request->manufacturing_date_en > $request->expiry_date_en) {
        return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Expiry date should be greater than manufacturing date']);
    }

    // Fetch product from DB
    $p = Product::find($id);
    $old_images = $p->images ?? [];

    // Upload and merge new images
    $img_names = $old_images;
    if ($request->has('product_images')) {
        foreach ($request->product_images as $img) {
            $image = Helpers::upload('product/', 'png', $img);
            $img_names[] = $image;
        }
    }

    // Save to main product table
    $p->name = $request['name'];
    $p->category_id = $request->category_id;
    $p->description = $request->description;
    $p->images = $img_names;
    $p->save();

    // Handle translation table
    $product = DB::table('products')->where('pid', "p".$id)->where('lang_code', $langName)->first();

    if($langName == 'en') {
        if ($product) {
            DB::table('products')
                ->where('pid', "p".$id)
                ->where('lang_code', $langName)
                ->update([
                    'name' => $request->name_en,
                    'description' => $request->description_en,
                    'category_id' => $request->category_id_en,
                    'price' => $request->price_en,
                    'image' => $request->has('image') ? Helpers::update('product/', $product->image, 'png', $request->file('image')) : $product->image,
                    'images' => $img_names,
                    'total_stock' => $request->total_stock_en,
                    'product_type_id' => $request->product_type_en,
                    'unit_weight' => $request->unit_weight_en,
                    'unit_pieces' => $request->total_pieces_en,
                    'unit' => $request->unit_en,
                    'batch_number' => $request->batch_number_en,
                    'manufacturing_date' => $request->manufacturing_date_en,
                    'expiry_date' => $request->expiry_date_en,
                    'manufactured_by' => $request->manufactured_by_en,
                    'gst' => $request->gst_en,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('products')->insert([
                'pid' => "p".$id,
                'lang_code' => $langName,
                'name' => $request->name_en,
                'description' => $request->description_en,
                'category_id' => $request->category_id_en,
                'price' => $request->price_en,
                'image' => $request->has('image') ? Helpers::update('product/', null, 'png', $request->file('image')) : null,
                'images' => $img_names,
                'total_stock' => $request->total_stock_en,
                'product_type_id' => $request->product_type_en,
                'unit_weight' => $request->unit_weight_en,
                'unit_pieces' => $request->total_pieces_en,
                'unit' => $request->unit_en,
                'batch_number' => $request->batch_number_en,
                'manufacturing_date' => $request->manufacturing_date_en,
                'expiry_date' => $request->expiry_date_en,
                'manufactured_by' => $request->manufactured_by_en,
                'gst' => $request->gst_en,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    } else {
        if ($product) {
            DB::table('products')
                ->where('pid', "p".$id)
                ->where('lang_code', $langName)
                ->update([
                    'name' => $request->name_fr,
                    'description' => $request->description_fr,
                    'category_id' => $request->category_id_fr,
                    'price' => $request->price_fr,
                    'image' => $request->has('image') ? Helpers::update('product/', $product->image, 'png', $request->file('image')) : $product->image,
                    'images' => $img_names,
                    'total_stock' => $request->total_stock_fr,
                    'product_type_id' => $request->product_type_fr,
                    'unit_weight' => $request->unit_weight_fr,
                    'unit_pieces' => $request->total_pieces_fr,
                    'unit' => $request->unit_fr,
                    'batch_number' => $request->batch_number_fr,
                    'manufacturing_date' => $request->manufacturing_date_fr,
                    'expiry_date' => $request->expiry_date_fr,
                    'manufactured_by' => $request->manufactured_by_fr,
                    'gst' => $request->gst_fr,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('products')->insert([
                'pid' => "p".$id,
                'lang_code' => $langName,
                'name' => $request->name_fr,
                'description' => $request->description_fr,
                'category_id' => $request->category_id_fr,
                'price' => $request->price_fr,
                'image' => $request->has('image') ? Helpers::update('product/', null, 'png', $request->file('image')) : null,
                'images' => $img_names,
                'total_stock' => $request->total_stock_fr,
                'product_type_id' => $request->product_type_fr,
                'unit_weight' => $request->unit_weight_fr,
                'unit_pieces' => $request->total_pieces_fr,
                'unit' => $request->unit_fr,
                'batch_number' => $request->batch_number_fr,
                'manufacturing_date' => $request->manufacturing_date_fr,
                'expiry_date' => $request->expiry_date_fr,
                'manufactured_by' => $request->manufactured_by_fr,
                'gst' => $request->gst_fr,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Product updated successfully']);
}

   public function destroy(Request $request)
{
    $product = Product::find($request->id);

    // Check if product exists before accessing its properties
    if (!$product) {
        Toastr::error(trans('messages.product_not_found'));
        return back();
    }

    // Delete product image if it exists
    if (!empty($product->image) && Storage::disk('public')->exists('product/' . $product->image)) {
        Storage::disk('public')->delete('product/' . $product->image);
    }

    $product->delete();

    Toastr::success(trans('messages.product_deleted_successfully'));
    return back();
}

 public function search(Request $request){
        $key = explode(' ', $request['search']);
        $products = Product::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->where('name', 'like', "%{$value}%");
            }
        })->orderBy('created_at','desc')->get();
        return response()->json(['count'=> count($products),
            'view'=>view('vendor-views.medicion.partials._table',compact('products'))->render()
        ]);
    }

 public function status(Request $request)
    {
        $product = Product::find($request->id);
        $product->status = $request->status;
        $product->save();
        Toastr::success('Status changed');
        return back();
    }

}
