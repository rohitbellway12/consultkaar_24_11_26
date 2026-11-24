<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{


    function index()
    {
        return view('admin-views.category.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
        ]);
        $category = new Category;


        $category->name = $request->name;
        $category->image = Helpers::upload('category/', 'png', $request->file('image'));
        $category->save();

        
        Toastr::success('Category added successfully');

        return back();
    }

    function edit($id)
    {
        $category = Category::find($id);
        return view('admin-views.category.edit',compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,'.$id,
        ]);
        $category = Category::find($id);


        $category->name = $request->name;
        $category->image = $request->has('image') ? Helpers::update('category/', $category->image, 'png', $request->file('image')) : $category->image;
        $category->save();

        
        Toastr::success('Category updated successfully');

        return back();
    }

    public function delete(Request $request)
    {
        $category = Category::find($request->id);
        $category->delete();
        Toastr::success('Category deleted successfully');
        return back();
    }

    function list()
    {
        $categories=Category::latest()->paginate(config('default_pagination'));
        return view('admin-views.category.list',compact('categories'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $categories = Category::where(function ($q) use ($key) {           
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            })->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.category.partials._table',compact('categories'))->render(),
            'count'=>$categories->count()
        ]);
        
        
    }  

    public function status(Request $request)
    {
        $category = Category::find($request->id);
        $category->status = $request->status;
        $category->save();
        Toastr::success('Category status updated!');
        return back();
    }
}
