<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Degree;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class DegreeController extends Controller
{
    function index()
    {
        return view('admin-views.degree.index');
    }

    // public function store(Request $request)
    // {
    //     $langName = $request->lang_code;

    //     if ($langName == 'en') {
    //         $validator = Validator::make($request->all(), [
    //             'name_en' => 'required',
    //         ], [
    //             'name_en.required' => "Name is required!",
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json(['errors' => Helpers::error_processor($validator)]);
    //         }

    //         $name = $request->name_en;
    //     } else {
    //         $validator = Validator::make($request->all(), [
    //             'name_fr' => 'required',
    //         ], [
    //             'name_fr.required' => "Name is required!",
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json(['errors' => Helpers::error_processor($validator)]);
    //         }

    //         $name = $request->name_fr;
    //     }

    //     // Check if the name already exists
    //     $nameExist = DB::table('degrees')->where('name', $name)->exists();
    //     if ($nameExist) {
    //         Toastr::error('Name already exists');
    //         return back();
    //     }

    //     $degree = new Degree;
    //     $fileName = Helpers::upload('degree/', 'png', $request->file('image'));
    //     $degree->name = $name;
    //     $degree->lang_code = $langName;
    //     $degree->image = $fileName;
    //     $degree->save();

    //     // Assign unique sid
    //     $id = $degree->id;
    //     DB::table('degrees')->where(['id' => $id, 'lang_code' => $langName])->update([
    //         'sid' => "d" . $id,
    //     ]);

    //     Toastr::success('Degree added successfully');
    //     return back();
    // }

// rohit
public function store(Request $request)
{
    try {
        $langName = $request->lang_code;

        if ($langName == 'en') {
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
            ], [
                'name_en.required' => "Name is required!",
            ]);

            if ($validator->fails()) {
                Toastr::error($validator->errors()->first()); // Display error via Toastr
                return back()->withErrors($validator)->withInput();
            }

            $name = $request->name_en;
        } else {
            $validator = Validator::make($request->all(), [
                'name_fr' => 'required',
            ], [
                'name_fr.required' => "Name is required!",
            ]);

            if ($validator->fails()) {
                Toastr::error($validator->errors()->first()); // Display error via Toastr
                return back()->withErrors($validator)->withInput();
            }

            $name = $request->name_fr;
        }

        // Check if the name already exists
        if (DB::table('degrees')->where('name', $name)->exists()) {
            Toastr::error('Name already exists'); // Display error via Toastr
            return back()->withInput();
        }

        // Upload image and create degree
        $degree = new Degree;
        $fileName = Helpers::upload('degree/', 'png', $request->file('image'));
        $degree->name = $name;
        $degree->lang_code = $langName;
        $degree->image = $fileName;
        $degree->save();

        // Assign unique sid
        $id = $degree->id;
        DB::table('degrees')->where(['id' => $id, 'lang_code' => $langName])->update([
            'sid' => "d" . $id,
        ]);

        Toastr::success('Degree added successfully'); // Success message
        return back();

    } catch (\Exception $e) {
        Toastr::error('Something went wrong: ' . $e->getMessage()); // Catch exceptions
        return back();
    }
}


    function edit($id)
    {
        $degree = Degree::find($id);
        return view('admin-views.degree.edit', compact('degree'));
    }

    public function update(Request $request, $id)
    {
        
        $langName = $request->lang_code;
        $degreeData = Degree::find($id);

        if ($langName == 'en') {
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
            ], [
                'name_en.required' => "Name is required!",
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }

            $name = $request->name_en;
            
        } else {
            $validator = Validator::make($request->all(), [
                'name_fr' => 'required',
            ], [
                'name_fr.required' => "Name is required!",
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }

            $name = $request->name_fr;
        }
        
 $fileName = $request->hasFile('image')
        ? Helpers::update('degree/', $degreeData->image, 'png', $request->file('image'))
        : $degreeData->image;
        
        $exists = DB::table('degrees')->where(['sid' => "d" . $id, 'lang_code' => $langName])->exists();
        if ($exists) {
            DB::table('degrees')->where(['sid' => "d" . $id, 'lang_code' => $langName])->update([
                'name' => $name,
                'image' => $fileName,
            ]);
        } else {
            DB::table('degrees')->insert([
                'name' => $name,
                'sid' => "d" . $id,
                'lang_code' => $langName,
                'image' => $fileName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Toastr::success('Degree updated successfully');
        return back();
    }

    public function delete(Request $request)
    {
        $sid = "d" . $request->id;
        $degrees = Degree::where('sid', $sid)->get();

        foreach ($degrees as $degree) {
            $degree->delete();
        }

        Toastr::success('Degree deleted successfully');
        return back();
    }

    function list(Request $request)
    {
        $degrees = DB::table('degrees')
            ->select('*')
            ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, sid FROM degrees GROUP BY sid) as sub'), function ($join) {
                $join->on('degrees.created_at', '=', 'sub.min_created_at')
                    ->on('degrees.sid', '=', 'sub.sid');
            })->orderBy('created_at', 'desc')->get();

        return view('admin-views.degree.list', compact('degrees'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $degrees = Degree::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();

        return response()->json([
            'view' => view('admin-views.degree.partials._table', compact('degrees'))->render(),
            'count' => $degrees->count()
        ]);
    }
}
