<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use App\CentralLogics\Helpers;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    public function index()
    {
        $categories =  DB::table('product_categories')->where('lang_code','en')->where('status',1)->get();  
        $french_categories =  DB::table('product_categories')->where('lang_code','fr')->where('status',1)->get(); 



        $product_types =  DB::table('product_types')->where('lang_code','en')->where('status',1)->get(); 
        $french_product_types =  DB::table('product_types')->where('lang_code','fr')->where('status',1)->get(); 
        return view('admin-views.product.index', compact('categories','product_types','french_categories','french_product_types'));
    }

    public function get_products()
    {
        $products = Product::where('status',1)->get();
        return $products;
    }

    public function store(Request $request)
    {
    
        $langName = $request->lang_name;

        if($langName == 'en'){
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

  
        if($langName == 'en'){
            $nameExist = DB::table('products')
            ->where('name', $request->name_en)
            ->exists();

            if($request->manufacturing_date_en > $request->expiry_date_en){
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Expiry date should be greater than manufacturing date']);
            }
    
            

        } else {
            $nameExist = DB::table('products')
            ->where('name', $request->name_fr)
            ->exists();
            if($request->manufacturing_date_fr > $request->expiry_date_fr){
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Expiry date should be greater than manufacturing date']);
            }
    
     
          
        

         }

        if ($nameExist) {
            Toastr::error('Product Name already exist');
            return redirect()->route('admin.doctor.add-new');
        }
        $Product = new Product;
        if($langName == 'en'){

           
            $Product->name = $request->name_en;      
            $Product->category_id = $request->category_id_en?$request->category_id_en:$request->category_id_en;
            $Product->description = $request->description_en;      
            $Product->total_stock = $request->total_stock_en;
            $Product->product_type_id = $request->product_type_en;
             $Product->unit_weight = $request->unit_weight_en;
       $Product->unit_pieces = $request->total_pieces_en; 
            $Product->unit = $request->unit_en;
            $Product->total_stock = $request->total_stock_en;
            $Product->batch_number = $request->batch_number_en;
            $Product->manufacturing_date = $request->manufacturing_date_en;
            $Product->expiry_date = $request->expiry_date_en;
            $Product->manufactured_by = $request->manufactured_by_en;      
            $Product->price = $request->price_en;     
            $Product->gst = $request->gst_en;  
            $Product->lang_code = $langName;

        } else {
            $Product->name = $request->name_fr;      
            $Product->category_id = $request->category_id_fr?$request->category_id_fr:$request->category_id_fr;
            $Product->description = $request->description_fr;      
            $Product->total_stock = $request->total_stock_fr;
            $Product->product_type_id = $request->product_type_fr;
            $Product->unit_weight = $request->unit_weight_fr;
         $product->unit_pieces = $request->total_pieces_fr;
            $Product->unit = $request->unit_fr;
            $Product->total_stock = $request->total_stock_fr;
            $Product->batch_number = $request->batch_number_fr;
            $Product->manufacturing_date = $request->manufacturing_date_fr;
            $Product->expiry_date = $request->expiry_date_fr;
            $Product->manufactured_by = $request->manufactured_by_fr;      
            $Product->price = $request->price_fr;     
            $Product->gst = $request->gst_fr;  
            $Product->lang_code = $langName;
        }
 

       

        $img_names = [];
        $images = [];     
       
        if (!empty($request->file('product_images'))) {
            foreach ($request->file('product_images') as $img) {
                $image_name = Helpers::upload('product/', 'png', $img);
                array_push($img_names, $image_name);
            }
            $images = $img_names;
        }
        $Product->image = Helpers::upload('product/', 'png', $request->file('image'));
       
        $Product->images = $images;   
        $Product->save();

        $productId = $Product->id;


        DB::table('products')->where(['id' => $productId])->update([
            'pid' => "p".$productId
        ]);

        return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Product added successfully']);
      
    }

    public function view($id)
    {
        $product = Product::where(['id' => $id])->first();
        $product_fr = Product::where(['pid' => "p".$id,'lang_code' => 'fr'])->first();
        return view('admin-views.product.view', compact('product','product_fr'));
    }

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

        return view('admin-views.product.edit', compact('product', 'categories','product_types','french_categories','french_product_types','product_fr'));
    }

    public function status(Request $request)
    {
        $product = Product::find($request->id);
        $product->status = $request->status;
        $product->save();
        Toastr::success('Status changed');
        return back();
    }

    public function update(Request $request, $id)
    {
        
       $langName = $request->lang_code;
    

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

        if($request->manufacturing_date > $request->expiry_date){
            return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Expiry date should be greater than manufacturing date']);
        }

      
       


        $p = Product::find($id);
        $p->name = $request['name'];
        $category = [];        

        $p->category_id = $request->category_id;
        $p->description = $request->description;
        $item_images = $p['images'];
        $images = [];    
        

        if ($request->has('product_images')){
            foreach ($request->product_images as $img) {
                $image = Helpers::upload('product/', 'png', $img);
                array_push($images, $image);
            }
        } 





  

        $p->images = array_merge($images,$item_images);
        $img_names = [];
            // Check if the record already exists
        $product = DB::table('products')->where('pid', "p".$id)->where('lang_code', $langName)->first();
         if($langName == 'en'){
            if ($product) {
                // Update the existing record
                DB::table('products')
                    ->where('pid', "p".$id)
                    ->where('lang_code', $langName)
                    ->update([
                        'name' => $request->name_en,
                        'description' => $request->description_en,
                        'category_id' => $request->category_id_en,
                        'price' => $request->price_en,
                        'image' => $request->has('image') ? Helpers::update('product/', $product->image, 'png', $request->file('image')) : $product->image,
                        'total_stock' => $request->total_stock_en,
                        'product_type_id' => $request->product_type_en,
                     'unit_weight' => $request->unit_weight_en,
                    'unit_pieces' => $request->total_pieces_en,
                        'unit' => $request->unit_en,
                        'total_stock' => $request->total_stock_en,
                        'batch_number' => $request->batch_number_en,
                        'manufacturing_date' => $request->manufacturing_date_en,
                        'expiry_date' => $request->expiry_date_en,
                        'manufactured_by' => $request->manufactured_by_en,
                        'gst' => $request->gst_en,
                        'updated_at' => now(),
                    ]);
            } else {
                // Insert a new record
                DB::table('products')->insert([
                    'pid' => "p".$id,
                    'lang_code' => $langName,
                    'name' => $request->name_en,
                    'description' => $request->description_en,
                    'category_id' => $request->category_id_en,
                    'price' => $request->price_en,
                    'image' => $request->has('image') ? Helpers::update('product/', null, 'png', $request->file('image')) : null,
                    'total_stock' => $request->total_stock_en,
                    'product_type_id' => $request->product_type_en,
                    'unit_weight' => $request->unit_weight_en,
                    'unit_pieces' => $request->total_pieces_en,
                    'unit' => $request->unit_en,
                    'total_stock' => $request->total_stock_en,
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
                // Update the existing record
                DB::table('products')
                    ->where('pid', "p".$id)
                    ->where('lang_code', $langName)
                    ->update([
                        'name' => $request->name_fr,
                        'description' => $request->description_fr,
                        'category_id' => $request->category_id_fr,
                        'price' => $request->price_fr,
                        'image' => $request->has('image') ? Helpers::update('product/', $productFr->image, 'png', $request->file('image')) : $productFr->image,
                        'total_stock' => $request->total_stock_fr,
                        'product_type_id' => $request->product_type_fr,
                        'unit_weight' => $request->unit_weight_fr,
                    'unit_pieces' => $request->total_pieces_fr,
                        'unit' => $request->unit_fr,
                        'total_stock' => $request->total_stock_fr,
                        'batch_number' => $request->batch_number_fr,
                        'manufacturing_date' => $request->manufacturing_date_fr,
                        'expiry_date' => $request->expiry_date_fr,
                        'manufactured_by' => $request->manufactured_by_fr,
                        'gst' => $request->gst_fr,
                        'updated_at' => now(),
                    ]);
            } else {
                // Insert a new record
                DB::table('products')->insert([
                    'pid' => "p".$id,
                    'lang_code' => $langName,
                    'name' => $request->name_fr,
                    'description' => $request->description_fr,
                    'category_id' => $request->category_id_fr,
                    'price' => $request->price_fr,
                    'image' => $request->has('image') ? Helpers::update('product/', null, 'png', $request->file('image')) : null,
                    'total_stock' => $request->total_stock_fr,
                    'product_type_id' => $request->product_type_fr,
                  'unit_weight' => $request->unit_weight_fr,
                    'unit_pieces' => $request->total_pieces_fr,
                    'unit' => $request->unit_fr,
                    'total_stock' => $request->total_stock_fr,
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

    public function delete(Request $request)
    {
        $product = Product::find($request->id);

        if($product->image)
        {
            if (Storage::disk('public')->exists('product/' . $product['image'])) {
                Storage::disk('public')->delete('product/' . $product['image']);
            }
        }

        $product->delete();
        Toastr::success(trans('messages.product_deleted_successfully'));
        return back();
    }

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
   
  
   
    public function get_categories(Request $request)
    {
        $cat = ProductCategory::get();
        $res = '<option value="">---Select---</option>';
        foreach ($cat as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'options' => $res,
        ]);
    }

  

    public function list(Request $request)
    {
      
        $category_id = $request->query('category_id', 'all');
      

        $products = DB::table('products')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, pid FROM products  GROUP BY pid) as sub'), function ($join) {
            $join->on('products.created_at', '=', 'sub.min_created_at')
                 ->on('products.pid', '=', 'sub.pid');
        })->orderBy('created_at','desc')->get();
        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Change this as needed
        $items = collect($products);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

        $products = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        // Set path for pagination links
        $products->withPath($request->url());



        $category =$category_id !='all'? ProductCategory::findOrFail($category_id):null;
        return view('admin-views.product.list', compact('products','category'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $products = Product::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->where('name', 'like', "%{$value}%");
            }
        })->orderBy('created_at','desc')->get();
        return response()->json(['count'=> count($products),
            'view'=>view('admin-views.product.partials._table',compact('products'))->render()
        ]);
    }
}

