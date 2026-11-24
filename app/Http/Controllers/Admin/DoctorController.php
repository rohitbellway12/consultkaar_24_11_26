<?php
namespace App\Http\Controllers\Admin;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Degree;
use App\Models\Order;
use App\Models\Speciality;
use App\Models\Domain;

use App\Models\User;
use App\Models\Zone;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function add_new()
    {
        $specialities = Speciality::where(['status' => 1, 'lang_code' => 'en'])->orderBy('id', 'desc')->get();
        $domains = Domain::all();
        $degrees = Degree::all();
        return view('admin-views.doctor.add-new', compact('specialities', 'domains', 'degrees'));
    }

    // public function store(Request $request)
    // {
    //     $langName = $request->lang_name;

    //     if ($langName == 'en') {
    //         $request->validate([
    //             'name_en' => 'required|string|max:50',
    //             'email_en' => 'required|email',
    //             'mobile_number_en' => 'required|digits_between:7,15|regex:/^[0-9]+$/',
    //             'description_en' => 'nullable|string',
    //             'commission_percentage_en' => 'nullable|numeric|min:0|max:100',
    //             'language_en' => 'nullable|string|max:50',
    //             'gender_en' => 'required|string',
    //             'speciality_id_en' => 'required|integer',
    //             'qualification_en' => 'required|integer',
    //             'experience_en' => 'required|numeric',
    //             'state_en' => 'required|string',
    //             'city_en' => 'required|string',
    //             'date_of_birth_en' => 'required|date',
    //         ]);
    //     } else {
    //         $request->validate([
    //             'name_fr' => 'required|string|max:50',
    //             'email_fr' => 'required|email',
    //             'mobile_number_fr' => 'required|digits_between:7,15|regex:/^[0-9]+$/',
    //             'description_fr' => 'nullable|string',
    //             'commission_percentage_fr' => 'nullable|numeric|min:0|max:100',
    //             'language_fr' => 'nullable|string|max:50',
    //             'gender_fr' => 'required|string',
    //             'speciality_id_fr' => 'required|integer',
    //             'qualification_fr' => 'required|integer',
    //             'experience_fr' => 'required|numeric',
    //             'state_fr' => 'required|string',
    //             'city_fr' => 'required|string',
    //             'date_of_birth_fr' => 'required|date',
    //         ]);
    //     }

    //     $password = Helpers::generatePassword(8);

    //     if ($langName == 'en') {
    //         $mobileExist = DB::table('users')
    //             ->where('mobile_number', $request->mobile_number_en)
    //             ->where('role_id', '3')
    //             ->exists();

    //         $emailExist = DB::table('users')
    //             ->where('email', $request->email_en)
    //             ->where('role_id', '3')
    //             ->exists();
    //     } else {
    //         $mobileExist = DB::table('users')
    //             ->where('mobile_number', $request->mobile_number_fr)
    //             ->where('role_id', '3')
    //             ->exists();

    //         $emailExist = DB::table('users')
    //             ->where('email', $request->email_fr)
    //             ->where('role_id', '3')
    //             ->exists();
    //     }

    //     if ($mobileExist) {
    //         \Brian2694\Toastr\Facades\Toastr::error('Mobile number already exists');
    //         return redirect()->route('admin.doctor.add-new');
    //     }

    //     if ($emailExist) {
    //         \Brian2694\Toastr\Facades\Toastr::error('Email already exists');
    //         return redirect()->route('admin.doctor.add-new');
    //     }

    //     // Handle multiple document uploads
    //     $document_images = [];
    //     if ($request->hasFile('documents')) {
    //         foreach ($request->file('documents') as $doc) {
    //             $fileName = Helpers::upload('doctor_documents/', 'png', $doc);
    //             $document_images[] = $fileName;
    //         }
    //     }

    //     // Upload profile pic
    //     $doctor_image = null;
    //     if ($langName == 'en') {
    //         $doctor_image = $request->hasFile('profile_pic_en')
    //             ? Helpers::upload('users/', 'png', $request->file('profile_pic_en'))
    //             : null;
    //     } else {
    //         $doctor_image = $request->hasFile('profile_pic_fr')
    //             ? Helpers::upload('users/', 'png', $request->file('profile_pic_fr'))
    //             : null;
    //     }

    //     if ($langName == 'en') {
    //         $userId = DB::table('users')->insertGetId([
    //             'name' => $request->name_en,
    //             'mobile_number' => $request->mobile_number_en,
    //             'lang_name' => $langName,
    //             'gender' => $request->gender_en,
    //             'email' => $request->email_en,
    //             'speciality_id' => $request->speciality_id_en,
    //             'qualification' => $request->qualification_en,
    //             'experience' => $request->experience_en,
    //             'state' => $request->state_en,
    //             'city' => $request->city_en,
    //             'address' => $request->address_en,
    //             'date_of_birth' => $request->date_of_birth_en,
    //             'role_id' => 3,
    //             'image' => $doctor_image,
    //             'password' => bcrypt($password),
    //             'decrypted_password' => $password,
    //             'location_id' => $request->location_id_en ?? null,
    //             // 'consultation_fee' => $request->consultation_fee_en ?? 0,
    //             'description' => $request->description_en,
    //             'commission_percentage' => $request->commission_percentage_en ?? 0,
    //             'language' => $request->language_en ?? 'en',
    //             'isregister' => 1,
    //             'is_active' => (int) $request->input('is_active_en', 0),
    //             'enable_chat' => (int) $request->input('enable_chat_en', 0),
    //             'enable_audio' => (int) $request->input('enable_audio_en', 0),
    //             'enable_video' => (int) $request->input('enable_video_en', 0),
    //             'chat_fee' => $request->enable_chat_en ? ($request->chat_fee_en ?? 0) : null,
    //             'audio_fee' => $request->enable_audio_en ? ($request->audio_fee_en ?? 0) : null,
    //             'video_fee' => $request->enable_video_en ? ($request->video_fee_en ?? 0) : null,
    //             'domain_id' => $request->domain_id_en,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     } else {
    //         $userId = DB::table('users')->insertGetId([
    //             'name' => $request->name_fr,
    //             'mobile_number' => $request->mobile_number_fr,
    //             'lang_name' => $langName,
    //             'gender' => $request->gender_fr,
    //             'email' => $request->email_fr,
    //             'speciality_id' => $request->speciality_id_fr,
    //             'qualification' => $request->qualification_fr,
    //             'experience' => $request->experience_fr,
    //             'state' => $request->state_fr,
    //             'city' => $request->city_fr,
    //             'address' => $request->address_fr,
    //             'date_of_birth' => $request->date_of_birth_fr,
    //             'role_id' => 3,
    //             'image' => $doctor_image,
    //             'password' => bcrypt($password),
    //             'decrypted_password' => $password,
    //             'location_id' => $request->location_id_fr ?? null,
    //             'description' => $request->description_fr,
    //             'commission_percentage' => $request->commission_percentage_fr ?? 0,
    //             'language' => $request->language_fr ?? 'fr',
    //             'is_active' => (int) $request->input('is_active_fr', 0),
    //             'enable_chat' => (int) $request->input('enable_chat_fr', 0),
    //             'enable_audio' => (int) $request->input('enable_audio_fr', 0),
    //             'enable_video' => (int) $request->input('enable_video_fr', 0),
    //             'chat_fee' => $request->enable_chat_fr ? ($request->chat_fee_fr ?? 0) : null,
    //             'audio_fee' => $request->enable_audio_fr ? ($request->audio_fee_fr ?? 0) : null,
    //             'video_fee' => $request->enable_video_fr ? ($request->video_fee_fr ?? 0) : null,
    //             'domain_id' => $request->domain_id_fr,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }

    //     DB::table('users')->where(['id' => $userId])->update([
    //         'uid' => 'u' . $userId
    //     ]);

    //     if (!empty($document_images)) {
    //         DB::table('users')->where(['id' => $userId])->update([
    //             'documents' => json_encode($document_images)
    //         ]);
    //     }

    //     \Brian2694\Toastr\Facades\Toastr::success('Advisor profile added successfully');
    //     return redirect()->route('admin.doctor.list');
    // }


public function store(Request $request)
{
    $langName = $request->lang_name;
    $suffix = '_' . $langName; // Determine suffix ('_en' or '_fr')

    // 1. Build Dynamic Validation Rules
    $validationRules = [
        'name' . $suffix => 'required|string|max:50',
        'email' . $suffix => 'required|email',
        'mobile_number' . $suffix => 'required|digits_between:7,15|regex:/^[0-9]+$/',
        'description' . $suffix => 'nullable|string',
        'commission_percentage' . $suffix => 'nullable|numeric|min:0|max:100',
        'language' . $suffix => 'nullable|string|max:50',
        'gender' . $suffix => 'required|string',
        'speciality_id' . $suffix => 'required',
        'qualification' . $suffix => 'required|integer',
        'experience' . $suffix => 'required|numeric',
        'state' . $suffix => 'required|string',
        'city' . $suffix => 'required|string',
        'date_of_birth' . $suffix => 'required|date',
        'domain_id' . $suffix => 'required',
    ];

    $request->validate($validationRules);

    // 2. Retrieve Dynamic Inputs
    $domainId = $request->input('domain_id' . $suffix);
    $specialityId = $request->input('speciality_id' . $suffix);
    $otherDomain = $request->input('other_domain' . $suffix);
    $otherSpecialization = $request->input('other_specialization' . $suffix);

    // Custom validation for "other" selections
    if ($domainId == 'other' && empty($otherDomain)) {
        Toastr::error('Please enter the domain name');
        return redirect()->route('admin.doctor.add-new');
    }

    if ($specialityId == 'other' && empty($otherSpecialization)) {
        Toastr::error('Please enter the specialization name');
        return redirect()->route('admin.doctor.add-new');
    }

    $password = Helpers::generatePassword(8);
    $mobile = $request->input('mobile_number' . $suffix);
    $email = $request->input('email' . $suffix);

    // Check Existence
    $mobileExist = DB::table('users')
        ->where('mobile_number', $mobile)
        ->where('role_id', '3')
        ->exists();

    if ($mobileExist) {
        Toastr::error('Mobile number already exists');
        return redirect()->route('admin.doctor.add-new');
    }

    $emailExist = DB::table('users')
        ->where('email', $email)
        ->where('role_id', '3')
        ->exists();

    if ($emailExist) {
        Toastr::error('Email already exists');
        return redirect()->route('admin.doctor.add-new');
    }

    // Handle multiple document uploads
    $document_images = [];
    if ($request->hasFile('documents')) {
        foreach ($request->file('documents') as $doc) {
            $fileName = Helpers::upload('doctor_documents/', 'png', $doc);
            $document_images[] = $fileName;
        }
    }

    // Upload profile pic dynamically
    $doctor_image = null;
    if ($request->hasFile('profile_pic' . $suffix)) {
        $doctor_image = Helpers::upload('users/', 'png', $request->file('profile_pic' . $suffix));
    }

    // Process Domain ID
    if ($domainId == 'other' && !empty($otherDomain)) {
        $domainId = DB::table('domains')->insertGetId([
            'name' => $otherDomain,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Process Specialization ID
    if ($specialityId == 'other' && !empty($otherSpecialization)) {
        $specialityId = DB::table('specialities')->insertGetId([
            'name' => $otherSpecialization,
            'domain_id' => $domainId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Handle Fee toggles
    $enableChat = (int) $request->input('enable_chat' . $suffix, 0);
    $enableAudio = (int) $request->input('enable_audio' . $suffix, 0);
    $enableVideo = (int) $request->input('enable_video' . $suffix, 0);

    // Insert Data
    $userId = DB::table('users')->insertGetId([
        'name' => $request->input('name' . $suffix),
        'mobile_number' => $mobile,
        'lang_name' => $langName,
        'gender' => $request->input('gender' . $suffix),
        'email' => $email,
        'speciality_id' => $specialityId,
        'qualification' => $request->input('qualification' . $suffix),
        'experience' => $request->input('experience' . $suffix),
        'state' => $request->input('state' . $suffix),
        'city' => $request->input('city' . $suffix),
        'address' => $request->input('address' . $suffix),
        'date_of_birth' => $request->input('date_of_birth' . $suffix),
        'role_id' => 3,
        'image' => $doctor_image,
        'password' => bcrypt($password),
        'decrypted_password' => $password,
        'location_id' => $request->input('location_id' . $suffix),
        'description' => $request->input('description' . $suffix),
        'commission_percentage' => $request->input('commission_percentage' . $suffix) ?? 0,
        'language' => $request->input('language' . $suffix) ?? $langName,
        'isregister' => 1,
        'is_active' => (int) $request->input('is_active' . $suffix, 0),
        'enable_chat' => $enableChat,
        'enable_audio' => $enableAudio,
        'enable_video' => $enableVideo,
        'chat_fee' => $enableChat ? ($request->input('chat_fee' . $suffix) ?? 0) : null,
        'audio_fee' => $enableAudio ? ($request->input('audio_fee' . $suffix) ?? 0) : null,
        'video_fee' => $enableVideo ? ($request->input('video_fee' . $suffix) ?? 0) : null,
        'domain_id' => $domainId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('users')->where(['id' => $userId])->update([
        'uid' => 'u' . $userId
    ]);

    if (!empty($document_images)) {
        DB::table('users')->where(['id' => $userId])->update([
            'documents' => json_encode($document_images)
        ]);
    }

    Toastr::success('Advisor profile added successfully');
    return redirect()->route('admin.doctor.list');
}

    public function list(Request $request)
    {
        $searchKey = $request->input('search');
        $dateFilter = $request->input('date_filter');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = DB::table('users')
            ->select(
                'users.*',
                'doctor_banks.account_number',
                'doctor_banks.bank_name',
                'doctor_banks.ifsc_code',
                'doctor_banks.account_holder_name'
            )
            ->leftJoin('doctor_banks', 'users.id', '=', 'doctor_banks.doctor_id')
            ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, uid FROM users WHERE role_id = 3 GROUP BY uid) as sub'), function ($join) {
                $join
                    ->on('users.created_at', '=', 'sub.min_created_at')
                    ->on('users.uid', '=', 'sub.uid');
            })
            ->where('users.isregister', 1)
            ->where('users.status', 1); // ONLY APPROVED DOCTORS

        // Apply search filter
        if ($searchKey) {
            $query->where(function ($q) use ($searchKey) {
                $q
                    ->where('users.name', 'like', "%{$searchKey}%")
                    ->orWhere('users.mobile_number', 'like', "%{$searchKey}%")
                    ->orWhere('users.email', 'like', "%{$searchKey}%");
            });
        }

        // Apply date filters
        if ($dateFilter) {
            $today = now()->format('Y-m-d');
            $thisWeekStart = now()->startOfWeek()->format('Y-m-d');
            $thisWeekEnd = now()->endOfWeek()->format('Y-m-d');
            $thisMonthStart = now()->startOfMonth()->format('Y-m-d');
            $thisMonthEnd = now()->endOfMonth()->format('Y-m-d');

            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('users.created_at', $today);
                    break;
                case 'this_week':
                    $query->whereBetween('users.created_at', [$thisWeekStart, $thisWeekEnd]);
                    break;
                case 'this_month':
                    $query->whereBetween('users.created_at', [$thisMonthStart, $thisMonthEnd]);
                    break;
            }
        }

        // Apply custom date range filter
        if ($fromDate && $toDate) {
            $query->whereBetween('users.created_at', [$fromDate, $toDate]);
        }

        $doctors = $query->orderBy('users.created_at', 'desc')->paginate(10);

        return view('admin-views.doctor.list', compact('doctors'));
    }

    public function viewbankdetails($id, Request $request)
    {
        $doctor = User::with(['bankDetails', 'country', 'state', 'city', 'domain'])
            ->where('id', $id)
            ->first();

        if (!$doctor) {
            Toastr::error(__('Doctor not found'));
            return back();
        }

        $specialization_name = null;
        if ($doctor->speciality_id) {
            $specialization = Speciality::where('id', $doctor->speciality_id)->value('name');
            $specialization_name = $specialization ?? 'N/A';
        }

        $degree_name = null;
        if ($doctor->qualification) {
            $degree = Degree::where('id', $doctor->qualification)->value('name');
            $degree_name = $degree ?? 'N/A';
        }

        // Wallet Transactions logic for Doctor
        $from = $request->from_date;
        $to = $request->to_date;

        $query = DB::table('wallet_transactions as doctor_tx')
            ->where('doctor_tx.user_id', $id)
            ->leftJoin('wallet_transactions as customer_tx', function($join) {
                $join->on('doctor_tx.order_id', '=', 'customer_tx.order_id')
                     ->where('customer_tx.is_credit', '=', '0');
            })
            ->leftJoin('users as patient', 'customer_tx.user_id', '=', 'patient.id')
            ->select('doctor_tx.*', 'patient.name as patient_name', 'patient.mobile_number as patient_mobile');

        if ($from && $to) {
            $query->whereBetween('doctor_tx.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        $wallet_transactions_paginated = $query->orderBy('doctor_tx.created_at', 'desc')
            ->paginate(10, ['*'], 'wallet_page')
            ->withQueryString();

        return view('admin-views.doctor.view', compact('doctor', 'specialization_name', 'degree_name', 'wallet_transactions_paginated'));
    }

    public function edit($id)
    {
        $user_record_en = User::where(['uid' => 'u' . $id, 'lang_name' => 'en'])->first();
        $user_record_fr = User::where(['uid' => 'u' . $id, 'lang_name' => 'fr'])->first();
        $user_record = User::where(['id' => $id])->first();

        $specialities = Speciality::where(['status' => 1, 'lang_code' => 'en'])->orderBy('id', 'desc')->get();
        $french_specialities = Speciality::where(['status' => 1, 'lang_code' => 'fr'])->orderBy('id', 'desc')->get();
        $locations = Zone::where(['status' => 1])->orderBy('id', 'desc')->get();
        $domains = Domain::all();
        $degrees = Degree::all();

        if (isset($user_record_en) && !empty($user_record_en)) {
            return view('admin-views.doctor.edit', compact('user_record_en', 'user_record_fr', 'specialities', 'locations', 'user_record', 'french_specialities', 'degrees', 'domains'));
        } else {
            Toastr::error('Invalid user id');
            return redirect()->route('admin.doctor.list');
        }
    }

    public function update(Request $request, $id)
    {
        $langName = $request->lang_name;

        if ($langName == 'en') {
            $request->validate([
                'name_en' => 'required|string',
                'mobile_number_en' => 'required',
                'profile_pic_en' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
                'description_en' => 'nullable|string',
                'commission_percentage_en' => 'nullable|numeric|min:0|max:100',
                'language_en' => 'nullable|string|max:50',
                'gender_en' => 'nullable|string',
                'speciality_id_en' => 'nullable|integer',
                'qualification_en' => 'nullable|integer',
                'experience_en' => 'nullable|numeric',
                'state_en' => 'nullable|string',
                'city_en' => 'nullable|string',
                'dob_en' => 'nullable|date',
            ]);
        } else {
            $request->validate([
                'name_fr' => 'required|string',
                'mobile_number_fr' => 'required',
                'profile_pic_fr' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
                'description_fr' => 'nullable|string',
                'commission_percentage_fr' => 'nullable|numeric|min:0|max:100',
                'language_fr' => 'nullable|string|max:50',
                'gender_fr' => 'nullable|string',
                'speciality_id_fr' => 'nullable|integer',
                'qualification_fr' => 'nullable|integer',
                'experience_fr' => 'nullable|numeric',
                'state_fr' => 'nullable|string',
                'city_fr' => 'nullable|string',
                'date_of_birth_fr' => 'nullable|date',
            ]);
        }

        $user_record = User::find($id);
        $record_uid = $user_record->uid;

        if ($langName == 'en') {
            $mobileExist = DB::table('users')
                ->where('mobile_number', $request->mobile_number_en)
                ->where('role_id', '3')
                ->where('uid', '!=', $record_uid)
                ->exists();

            $emailExist = DB::table('users')
                ->where('email', $request->email_en)
                ->where('role_id', '3')
                ->where('uid', '!=', $record_uid)
                ->exists();
        } else {
            $mobileExist = DB::table('users')
                ->where('mobile_number', $request->mobile_number_fr)
                ->where('role_id', '3')
                ->where('uid', '!=', $record_uid)
                ->exists();

            $emailExist = DB::table('users')
                ->where('email', $request->email_fr)
                ->where('role_id', '3')
                ->where('uid', '!=', $record_uid)
                ->exists();
        }

        if ($mobileExist) {
            Toastr::error('Mobile number already exist');
            return redirect()->route('admin.doctor.edit', ['id' => $id]);
        }

        if ($emailExist) {
            Toastr::error('Email already exist');
            return redirect()->route('admin.doctor.edit', ['id' => $id]);
        }

        $document_images = [];
        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            if (is_array($files)) {
                foreach ($files as $doc) {
                    if ($doc && $doc->isValid()) {
                        $fileName = Helpers::upload('doctor_documents/', 'png', $doc);
                        $document_images[] = $fileName;
                    }
                }
            }
        }

        $exists = DB::table('users')
            ->where(['uid' => $record_uid, 'lang_name' => $langName])
            ->exists();

        if ($exists) {
            if ($langName == 'en') {
                $image = $user_record->image;
                if ($request->hasFile('profile_pic_en')) {
                    $image = Helpers::update('users/', $user_record->image, 'png', $request->file('profile_pic_en'));
                }

                $updateData = [
                    'name' => $request->name_en,
                    'mobile_number' => $request->mobile_number_en,
                    'gender' => $request->gender_en,
                    'email' => $request->email_en,
                    'speciality_id' => $request->speciality_id_en,
                    'qualification' => $request->qualification_en,
                    'experience' => $request->experience_en,
                    'state' => $request->state_en,
                    'city' => $request->city_en,
                    'address' => $request->address_en,
                    'date_of_birth' => $request->date_of_birth_en,
                    'role_id' => 3,
                    'lang_name' => $langName,
                    'image' => $image,
                    'location_id' => $request->location_id_en ?? null,
                    'description' => $request->description_en,
                    'commission_percentage' => $request->commission_percentage_en ?? 0,
                    'language' => $request->language_en ?? 'en',
                    'is_active' => (int) $request->input('is_active_en', 0),
                    'enable_chat' => (int) $request->input('enable_chat_en', 0),
                    'enable_audio' => (int) $request->input('enable_audio_en', 0),
                    'enable_video' => (int) $request->input('enable_video_en', 0),
                    'chat_fee' => $request->enable_chat_en ? ($request->chat_fee_en ?? 0) : null,
                    'audio_fee' => $request->enable_audio_en ? ($request->audio_fee_en ?? 0) : null,
                    'video_fee' => $request->enable_video_en ? ($request->video_fee_en ?? 0) : null,
                    'domain_id' => $request->domain_id_en,
                    'updated_at' => now(),
                ];

                DB::table('users')->where(['uid' => $record_uid, 'lang_name' => $langName])->update($updateData);

                // Sync common fields to other language records
                DB::table('users')->where('uid', $record_uid)->update([
                    'mobile_number' => $request->mobile_number_en,
                    'email' => $request->email_en,
                    'gender' => $request->gender_en,
                    'date_of_birth' => $request->date_of_birth_en,
                    'state' => $request->state_en,
                    'city' => $request->city_en,
                ]);

                if (!empty($document_images)) {
                    $existingUser = DB::table('users')->where(['uid' => $record_uid, 'lang_name' => $langName])->first();
                    $existingDocs = [];
                    if ($existingUser && $existingUser->documents) {
                        $decoded = json_decode($existingUser->documents, true);
                        $existingDocs = is_array($decoded) ? $decoded : [];
                    }
                    $allDocuments = array_merge($existingDocs, $document_images);
                    DB::table('users')->where('uid', $record_uid)->update([
                        'documents' => json_encode($allDocuments)
                    ]);
                }
            } else {
                $image = $user_record->image;
                if ($request->hasFile('profile_pic_fr')) {
                    $image = Helpers::update('users/', $user_record->image, 'png', $request->file('profile_pic_fr'));
                }

                $updateDataFr = [
                    'name' => $request->name_fr,
                    'mobile_number' => $request->mobile_number_fr,
                    'gender' => $request->gender_fr,
                    'email' => $request->email_fr,
                    'speciality_id' => $request->speciality_id_fr,
                    'qualification' => $request->qualification_fr,
                    'experience' => $request->experience_fr,
                    'state' => $request->state_fr,
                    'city' => $request->city_fr,
                    'address' => $request->address_fr,
                    'date_of_birth' => $request->date_of_birth_fr,
                    'role_id' => 3,
                    'lang_name' => $langName,
                    'image' => $image,
                    'location_id' => $request->location_id_fr ?? null,
                    'description' => $request->description_fr,
                    'commission_percentage' => $request->commission_percentage_fr ?? 0,
                    'language' => $request->language_fr ?? 'fr',
                    'is_active' => (int) $request->input('is_active_fr', 0),
                    'enable_chat' => (int) $request->input('enable_chat_fr', 0),
                    'enable_audio' => (int) $request->input('enable_audio_fr', 0),
                    'enable_video' => (int) $request->input('enable_video_fr', 0),
                    'chat_fee' => $request->enable_chat_fr ? ($request->chat_fee_fr ?? 0) : null,
                    'audio_fee' => $request->enable_audio_fr ? ($request->audio_fee_fr ?? 0) : null,
                    'video_fee' => $request->enable_video_fr ? ($request->video_fee_fr ?? 0) : null,
                    'domain_id' => $request->domain_id_fr,
                    'updated_at' => now(),
                ];

                DB::table('users')->where(['uid' => $record_uid, 'lang_name' => $langName])->update($updateDataFr);

                // Sync common fields to other language records
                DB::table('users')->where('uid', $record_uid)->update([
                    'mobile_number' => $request->mobile_number_fr,
                    'email' => $request->email_fr,
                    'gender' => $request->gender_fr,
                    'date_of_birth' => $request->date_of_birth_fr,
                    'state' => $request->state_fr,
                    'city' => $request->city_fr,
                ]);

                if (!empty($document_images)) {
                    $existingUser = DB::table('users')->where(['uid' => $record_uid, 'lang_name' => $langName])->first();
                    $existingDocs = [];
                    if ($existingUser && $existingUser->documents) {
                        $decoded = json_decode($existingUser->documents, true);
                        $existingDocs = is_array($decoded) ? $decoded : [];
                    }
                    $allDocuments = array_merge($existingDocs, $document_images);
                    DB::table('users')->where('uid', $record_uid)->update([
                        'documents' => json_encode($allDocuments)
                    ]);
                }
            }
        } else {
            // Insert new language version
            $password = Helpers::generatePassword(8);
            $doctor_image = null;
            if ($langName == 'en') {
                $doctor_image = $request->hasFile('profile_pic_en')
                    ? Helpers::upload('users/', 'png', $request->file('profile_pic_en'))
                    : null;

                $newUserId = DB::table('users')->insertGetId([
                    'name' => $request->name_en,
                    'mobile_number' => $request->mobile_number_en,
                    'lang_name' => $langName,
                    'gender' => $request->gender_en,
                    'email' => $request->email_en,
                    'speciality_id' => $request->speciality_id_en,
                    'qualification' => $request->qualification_en,
                    'experience' => $request->experience_en,
                    'state' => $request->state_en,
                    'city' => $request->city_en,
                    'address' => $request->address_en,
                    'date_of_birth' => $request->date_of_birth_en,
                    'role_id' => 3,
                    'uid' => 'u' . $id,
                    'image' => $doctor_image,
                    'password' => bcrypt($password),
                    'decrypted_password' => $password,
                    'location_id' => $request->location_id_en ?? null,
                    // 'consultation_fee' => $request->consultation_fee_en ?? 0,
                    'description' => $request->description_en,
                    'commission_percentage' => $request->commission_percentage_en ?? 0,
                    'language' => $request->language_en ?? 'en',
                    'is_active' => (int) $request->input('is_active_en', 0),
                    'enable_chat' => (int) $request->input('enable_chat_en', 0),
                    'enable_audio' => (int) $request->input('enable_audio_en', 0),
                    'enable_video' => (int) $request->input('enable_video_en', 0),
                    'chat_fee' => $request->enable_chat_en ? ($request->chat_fee_en ?? 0) : null,
                    'audio_fee' => $request->enable_audio_en ? ($request->audio_fee_en ?? 0) : null,
                    'video_fee' => $request->enable_video_en ? ($request->video_fee_en ?? 0) : null,
                    'domain_id' => $request->domain_id_en,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $doctor_image = $request->hasFile('profile_pic_fr')
                    ? Helpers::upload('users/', 'png', $request->file('profile_pic_fr'))
                    : null;

                $newUserId = DB::table('users')->insertGetId([
                    'name' => $request->name_fr,
                    'mobile_number' => $request->mobile_number_fr,
                    'lang_name' => $langName,
                    'gender' => $request->gender_fr,
                    'email' => $request->email_fr,
                    'speciality_id' => $request->speciality_id_fr,
                    'qualification' => $request->qualification_fr,
                    'experience' => $request->experience_fr,
                    'state' => $request->state_fr,
                    'city' => $request->city_fr,
                    'address' => $request->address_fr,
                    'date_of_birth' => $request->date_of_birth_fr,
                    'role_id' => 3,
                    'uid' => 'u' . $id,
                    'image' => $doctor_image,
                    'password' => bcrypt($password),
                    'decrypted_password' => $password,
                    'location_id' => $request->location_id_fr ?? null,
                    // 'consultation_fee' => $request->consultation_fee_fr ?? 0,
                    'description' => $request->description_fr,
                    'commission_percentage' => $request->commission_percentage_fr ?? 0,
                    'language' => $request->language_fr ?? 'fr',
                    'is_active' => (int) $request->input('is_active_fr', 0),
                    'enable_chat' => (int) $request->input('enable_chat_fr', 0),
                    'enable_audio' => (int) $request->input('enable_audio_fr', 0),
                    'enable_video' => (int) $request->input('enable_video_fr', 0),
                    'chat_fee' => $request->enable_chat_fr ? ($request->chat_fee_fr ?? 0) : null,
                    'audio_fee' => $request->enable_audio_fr ? ($request->audio_fee_fr ?? 0) : null,
                    'video_fee' => $request->enable_video_fr ? ($request->video_fee_fr ?? 0) : null,
                    'domain_id' => $request->domain_id_fr,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (!empty($document_images)) {
                DB::table('users')->where(['id' => $newUserId])->update([
                    'documents' => json_encode($document_images)
                ]);
            }
        }

        Toastr::success('Advisor updated successfully');
        return redirect()->route('admin.doctor.list');
    }

    public function distroy($id)
    {
        User::where(['id' => $id])->delete();
        \Brian2694\Toastr\Facades\Toastr::success('Doctor deleted successfully');
        return back();
    }

    // ... rest of your methods (search, view, filter, filter_reset) remain unchanged
    // You can keep them as they are below...





    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $doctors = DB::table('users')
            ->select('*')
            ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, uid FROM users WHERE role_id = 3 GROUP BY uid) as sub'), function ($join) {
                $join
                    ->on('users.created_at', '=', 'sub.min_created_at')
                    ->on('users.uid', '=', 'sub.uid');
            })
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                    $q->orWhere('mobile_number', 'like', "%{$value}%");
                    $q->orWhere('email', 'like', "%{$value}%");
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $items = collect($doctors);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();
        $doctors = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        $doctors->withPath($request->url());

        return response()->json([
            'view' => view('admin-views.doctor.partials._table', compact('doctors'))->render(),
            'count' => $doctors->count()
        ]);
    }

    public function view($id)
    {
        $customer = User::find($id);
        if (isset($customer)) {
            if (session()->has('customer_order_filter')) {
                $request = json_decode(session('customer_order_filter'));
                $status = 'all';

                $orders = Order::latest()
                    ->when(isset($request->zone), function ($query) use ($request) {
                        return $query->whereHas('vendor', function ($q) use ($request) {
                            return $q->whereIn('zone_id', $request->zone);
                        });
                    })
                    ->when($status == 'ongoing', function ($query) {
                        return $query->ServiceOngoing();
                    })
                    ->when($status == 'completed', function ($query) {
                        return $query->Completed();
                    })
                    ->when($status == 'cancelled', function ($query) {
                        return $query->Cancelled();
                    })
                    ->when($status == 'failed', function ($query) {
                        return $query->failed();
                    })
                    ->when($status == 'refunded', function ($query) {
                        return $query->Refunded();
                    })
                    ->when($status == 'all', function ($query) {
                        return $query->All();
                    })
                    ->when(isset($request->vendor), function ($query) use ($request) {
                        return $query->whereHas('vendor', function ($query) use ($request) {
                            return $query->whereIn('id', $request->vendor);
                        });
                    })
                    ->when(isset($request->orderStatus) && $status == 'all', function ($query) use ($request) {
                        return $query->whereIn('status', $request->orderStatus);
                    })
                    ->when(isset($request->scheduled) && $status == 'all', function ($query) {
                        return $query->scheduled();
                    })
                    ->when(isset($request->from_date) && isset($request->to_date) && $request->from_date != null && $request->to_date != null, function ($query) use ($request) {
                        return $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
                    })
                    ->where(['user_id' => $id])
                    ->orderBy('id', 'desc')
                    ->paginate(config('default_pagination'));

                $orderstatus = isset($request->orderStatus) ? $request->orderStatus : [];
                $from_date = isset($request->from_date) ? $request->from_date : null;
                $to_date = isset($request->to_date) ? $request->to_date : null;

                return view('admin-views.doctor.doctor-view', compact('customer', 'orders', 'status', 'orderstatus', 'from_date', 'to_date'));
            } else {
                $orders = Order::latest()->where(['user_id' => $id])->paginate(config('default_pagination'));
                $status = 'all';
                $orderstatus = [];
                $from_date = null;
                $to_date = null;
                return view('admin-views.doctor.doctor-view', compact('customer', 'orders', 'status', 'orderstatus', 'from_date', 'to_date'));
            }
        }
        Toastr::error(__('messages.doctor_not_found'));
        return back();
    }

    public function filter(Request $request)
    {
        $request->validate([
            'from_date' => 'required_if:to_date,true',
            'to_date' => 'required_if:from_date,true',
        ]);

        session()->put('customer_order_filter', json_encode($request->all()));
        return back();
    }

    public function filter_reset(Request $request)
    {
        session()->forget('customer_order_filter');
        return back();
    }

    public function request_list(Request $request)
    {
        $searchKey = $request->input('search');

        $query = DB::table('users')
            ->select(
                'users.*',
                'doctor_banks.account_number',
                'doctor_banks.bank_name',
                'doctor_banks.ifsc_code',
                'doctor_banks.account_holder_name'
            )
            ->leftJoin('doctor_banks', 'users.id', '=', 'doctor_banks.doctor_id')
            ->where('users.role_id', 3)          // Ensure only doctors
            ->where('users.isregister', 1)       // Profile submitted
            ->where('users.status', 0);          // Pending approval

        if ($searchKey) {
            $query->where(function ($q) use ($searchKey) {
                $q->where('users.name', 'like', "%{$searchKey}%")
                    ->orWhere('users.mobile_number', 'like', "%{$searchKey}%")
                    ->orWhere('users.email', 'like', "%{$searchKey}%");
            });
        }

        $doctors = $query->orderBy('users.created_at', 'desc')->paginate(10);

        return view('admin-views.doctor.request-list', compact('doctors'));
    }

    public function approve_form($id)
    {
        $doctor = User::find($id);
        if (!$doctor) {
            Toastr::error('Advisor not found');
            return back();
        }
        return view('admin-views.doctor.approve-form', compact('doctor'));
    }

    public function approve_submit(Request $request, $id)
    {
        $request->validate([
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'chat_fee' => 'required|numeric|min:0',
            'audio_fee' => 'required|numeric|min:0',
            'video_fee' => 'required|numeric|min:0',
        ]);

        $user = User::find($id);
        if ($user) {
            $user->status = 1;
            $user->is_active = 1;
            $user->commission_percentage = $request->commission_percentage;
            $user->chat_fee = $request->chat_fee;
            $user->audio_fee = $request->audio_fee;
            $user->video_fee = $request->video_fee;
            $user->enable_chat = 1;
            $user->enable_audio = 1;
            $user->enable_video = 1;
            $user->save();
            Toastr::success('Advisor approved successfully with configured fees');
        } else {
            Toastr::error('Advisor not found');
        }
        return redirect()->route('admin.doctor.list');
    }

    public function reject_submit(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $user = User::find($id);
        if ($user) {
            $user->status = 2;
            $user->is_active = 0;
            $user->rejection_reason = $request->rejection_reason;
            $user->save();
            Toastr::success('Advisor rejected successfully');
        } else {
            Toastr::error('Advisor not found');
        }
        return redirect()->route('admin.doctor.request-list');
    }
}