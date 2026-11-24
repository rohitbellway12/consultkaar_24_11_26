<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Domain;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        $searchKey = $request->input('search');
        $query = Domain::query();
        
        if ($searchKey) {
            $query->where('name', 'like', "%{$searchKey}%");
        }
        
        $domains = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        return view('admin-views.domain.index', compact('domains'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:domains,name',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Toastr::error($error);
            }
            return back();
        }

        $domain = new Domain();
        $domain->name = $request->name;
        $domain->save();

        Toastr::success('Domain added successfully!');
        return back();
    }

    public function edit($id)
    {
        $domain = Domain::findOrFail($id);
        return view('admin-views.domain.edit', compact('domain'));
    }

    public function update(Request $request, $id)
    {
        $domain = Domain::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:domains,name,' . $domain->id,
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Toastr::error($error);
            }
            return back();
        }

        $domain->name = $request->name;
        $domain->save();

        Toastr::success('Domain updated successfully!');
        return redirect()->route('admin.domain.list'); // Assuming route name
    }

    public function delete(Request $request)
    {
        $domain = Domain::findOrFail($request->id);
        $domain->delete();
        
        Toastr::success('Domain deleted successfully!');
        return back();
    }
    
    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $domains = Domain::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        
        return response()->json([
            'view'=>view('admin-views.domain.partials._table',compact('domains'))->render(),
            'count'=>$domains->count()
        ]);
    }
}
