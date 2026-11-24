<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdentityProof;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class IdentityProofController extends Controller
{
    function index()
    {
        return view('admin-views.identity-proof.index');
    }

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
                    Toastr::error($validator->errors()->first());
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
                    Toastr::error($validator->errors()->first());
                    return back()->withErrors($validator)->withInput();
                }

                $name = $request->name_fr;
            }

            if (DB::table('identity_proofs')->where('name', $name)->exists()) {
                Toastr::error('Name already exists');
                return back()->withInput();
            }

            $identityProof = new IdentityProof;
            $fileName = Helpers::upload('identity_proof/', 'png', $request->file('image'));
            $identityProof->name = $name;
            $identityProof->lang_code = $langName;
            $identityProof->image = $fileName;
            $identityProof->save();

            $id = $identityProof->id;
            DB::table('identity_proofs')->where(['id' => $id, 'lang_code' => $langName])->update([
                'sid' => "ip" . $id,
            ]);

            Toastr::success('Identity Proof added successfully');
            return back();

        } catch (\Exception $e) {
            Toastr::error('Something went wrong: ' . $e->getMessage());
            return back();
        }
    }

    function edit($id)
    {
        $identityProof = IdentityProof::find($id);
        return view('admin-views.identity-proof.edit', compact('identityProof'));
    }

    public function update(Request $request, $id)
    {
        $langName = $request->lang_code;
        $identityProofData = IdentityProof::find($id);

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
            ? Helpers::update('identity_proof/', $identityProofData->image, 'png', $request->file('image'))
            : $identityProofData->image;

        $exists = DB::table('identity_proofs')->where(['sid' => "ip" . $id, 'lang_code' => $langName])->exists();
        if ($exists) {
            DB::table('identity_proofs')->where(['sid' => "ip" . $id, 'lang_code' => $langName])->update([
                'name' => $name,
                'image' => $fileName,
            ]);
        } else {
            DB::table('identity_proofs')->insert([
                'name' => $name,
                'sid' => "ip" . $id,
                'lang_code' => $langName,
                'image' => $fileName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Toastr::success('Identity Proof updated successfully');
        return back();
    }

    public function delete(Request $request)
    {
        $sid = "ip" . $request->id;
        $identityProofs = IdentityProof::where('sid', $sid)->get();

        foreach ($identityProofs as $proof) {
            $proof->delete();
        }

        Toastr::success('Identity Proof deleted successfully');
        return back();
    }

    function list(Request $request)
    {
        $identityProofs = DB::table('identity_proofs')
            ->select('*')
            ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, sid FROM identity_proofs GROUP BY sid) as sub'), function ($join) {
                $join->on('identity_proofs.created_at', '=', 'sub.min_created_at')
                    ->on('identity_proofs.sid', '=', 'sub.sid');
            })->orderBy('created_at', 'desc')->get();

        return view('admin-views.identity-proof.list', compact('identityProofs'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $identityProofs = IdentityProof::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();

        return response()->json([
            'view' => view('admin-views.identity-proof.partials._table', compact('identityProofs'))->render(),
            'count' => $identityProofs->count()
        ]);
    }
}
