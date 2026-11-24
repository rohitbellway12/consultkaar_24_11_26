<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Models\User;      // Import the User model
use Illuminate\Support\Facades\Hash;  // For password hashing
use Illuminate\Support\Facades\Storage; 

class MedicalStoreController extends Controller
{
public function list(Request $request)
{
    $search = $request->input('search');

    $stores = DB::table('users')
        ->where('role_id', 4)
        ->when($search, function ($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('admin-views.medical-store.list', compact('stores'));
}



    public function add_new()
    {
        return view('admin-views.medical-store.add-new');
    }


public function store(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'phone'    => 'required|string|max:15',
        'address'  => 'required|string|max:500',
        'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('medical_stores', 'public');
    }
    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),  
        'mobile_number'    => $request->phone,
        'address'  => $request->address,
        'image'    => $imagePath, 
        'role_id'=> 4,
        'status'=>1,
    ]);

    $userId = $user->id;
    DB::table('users')->where('id', $userId)->update([
        'uid' => "u" . $userId
    ]);

    // ✅ Redirect with success message
    return redirect()->route('admin.medical-store.list')->with('success', 'Medical Store Added Successfully');
}


    public function edit($id)
    {
        $store = DB::table('users')->where('id', $id)->first();
        return view('admin-views.medical-store.edit', compact('store'));
    }

   public function update(Request $request)
{
    $id = $request->id;

    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email,' . $id,  // Ignore current ID for uniqueness
        'phone'    => 'required|string|max:15',
        'address'  => 'required|string|max:500',
        'password' => 'nullable|string|min:6',  // Optional password update
        'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user = User::findOrFail($id);

    if ($request->hasFile('image')) {
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        
        $imagePath = $request->file('image')->store('medical_stores', 'public');
        $user->image = $imagePath;
    }

    $user->name = $request->name;
    $user->email = $request->email;
    $user->mobile_number = $request->phone;
    $user->address = $request->address;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->role_id = 4;
    $user->status = 1;

    $user->save();

    return redirect()->route('admin.medical-store.list')
                     ->with('success', 'Medical Store Updated Successfully');
}

    public function destroy($id)
    {
        // dd($id);
        DB::table('users')->where('id', $id)->delete();
        return redirect()->route('admin.medical-store.list')->with('success', 'Medical Store Deleted Successfully');
    }

    public function search(Request $request)
    {
        // dd($request->all());
        $query = $request->input('query');
        $stores = DB::table('users')
            ->where('name', 'LIKE', "%$query%")
            ->orWhere('owner', 'LIKE', "%$query%")
            ->orWhere('license_number', 'LIKE', "%$query%")
            ->get();

        return view('admin-views.medical-store.list', compact('stores'));
    }
}


