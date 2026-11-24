<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;

class ReelController extends Controller
{
    public function index(Request $request)
    {
        $query = Reel::with('advisor');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhereHas('advisor', function ($q2) use ($search) {
                      $q2->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $reels = $query->latest()->paginate(10);
        $reels->appends($request->all());

        return view('admin-views.reel.list', compact('reels'));
    }

    public function view($id)
    {
        $reel = Reel::with(['advisor', 'comments.user'])->findOrFail($id);
        return view('admin-views.reel.view', compact('reel'));
    }

    public function delete($id)
    {
        $reel = Reel::findOrFail($id);

        if ($reel->video && \Storage::disk('public')->exists('reels/' . $reel->video)) {
            \Storage::disk('public')->delete('reels/' . $reel->video);
        }

        $reel->delete();

        Toastr::success('Reel deleted successfully.');
        return redirect()->route('admin.reel.list');
    }

    public function status($id, $status)
    {
        $reel = Reel::findOrFail($id);
        $reel->status = $status;
        $reel->save();

        $statusText = $status ? 'activated' : 'deactivated';
        Toastr::success('Reel ' . $statusText . ' successfully.');
        return redirect()->back();
    }

    public function earningSettings()
    {
        $criteria = \App\Models\ReelEarningCriteria::first();
        if (!$criteria) {
            $criteria = \App\Models\ReelEarningCriteria::create([
                'minimum_views' => 1000,
                'minimum_values' => 100,
                'is_active' => true,
            ]);
        }
        return view('admin-views.reel.earning-settings', compact('criteria'));
    }

    public function updateEarningSettings(Request $request)
    {
        $request->validate([
            'minimum_views' => 'required|integer|min:0',
            'minimum_values' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $criteria = \App\Models\ReelEarningCriteria::firstOrNew();
        $criteria->minimum_views = $request->minimum_views;
        $criteria->minimum_values = $request->minimum_values;
        $criteria->is_active = $request->has('is_active') ? 1 : 0;
        $criteria->save();

        Toastr::success('Knowledge earning criteria updated successfully.');
        return redirect()->route('admin.reel.earning-settings');
    }

    public function reelNotifications()
    {
        $notifications = \App\Models\Notification::where('title', 'Reel Achieved Earning Criteria')
            ->latest()
            ->paginate(20);

        return view('admin-views.reel.notifications', compact('notifications'));
    }

    public function report(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $doctor_id = $request->input('doctor_id');

        $query = DB::table('users as u')
            ->where('u.role_id', 3)
            ->where('u.isregister', 1)
            ->select(
                'u.id',
                'u.name',
                'u.mobile_number',
                'u.wallet_amount',
                'u.created_at',
                DB::raw('(SELECT COUNT(*) FROM reels WHERE reels.user_id = u.id AND reels.status = 1' . ($from && $to ? " AND reels.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'" : "") . ') as total_reels'),
                DB::raw('(SELECT SUM(views_count) FROM reels WHERE reels.user_id = u.id AND reels.status = 1' . ($from && $to ? " AND reels.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'" : "") . ') as total_views'),
                DB::raw('(SELECT SUM(value_count) FROM reels WHERE reels.user_id = u.id AND reels.status = 1' . ($from && $to ? " AND reels.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'" : "") . ') as total_likes'),
                DB::raw('(SELECT SUM(comments_count) FROM reels WHERE reels.user_id = u.id AND reels.status = 1' . ($from && $to ? " AND reels.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'" : "") . ') as total_comments'),
                DB::raw('(SELECT SUM(shares_count) FROM reels WHERE reels.user_id = u.id AND reels.status = 1' . ($from && $to ? " AND reels.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'" : "") . ') as total_shares'),
                DB::raw('(SELECT SUM(amount) FROM wallet_transactions WHERE wallet_transactions.user_id = u.id AND is_credit = "1" AND transaction_for = "reel_credit"' . ($from && $to ? " AND wallet_transactions.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'" : "") . ') as total_reel_credits'),
                DB::raw('(SELECT SUM(amount) FROM wallet_transactions WHERE wallet_transactions.user_id = u.id AND is_credit = "0" AND transaction_for = "reel_debit"' . ($from && $to ? " AND wallet_transactions.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'" : "") . ') as total_reel_debits')
            );

        if ($doctor_id) {
            $query->where('u.id', $doctor_id);
        }

        $advisors = $query->orderBy('total_reels', 'desc')->paginate(25)->appends($request->all());

        $summary = [
            'total_reels' => \App\Models\Reel::where('status', 1)->when($from && $to, function($q) use ($from, $to) { $q->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']); })->count(),
            'total_views' => \App\Models\Reel::where('status', 1)->when($from && $to, function($q) use ($from, $to) { $q->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']); })->sum('views_count'),
            'total_likes' => \App\Models\Reel::where('status', 1)->when($from && $to, function($q) use ($from, $to) { $q->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']); })->sum('value_count'),
            'total_reel_credits' => DB::table('wallet_transactions')->where('is_credit', '1')->where('transaction_for', 'reel_credit')->when($from && $to, function($q) use ($from, $to) { $q->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']); })->sum('amount'),
            'total_reel_debits' => DB::table('wallet_transactions')->where('is_credit', '0')->where('transaction_for', 'reel_debit')->when($from && $to, function($q) use ($from, $to) { $q->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']); })->sum('amount'),
        ];

        $all_advisors = \App\Models\User::where('role_id', 3)->where('isregister', 1)->get();

        return view('admin-views.reel.report', compact('advisors', 'summary', 'from', 'to', 'all_advisors', 'doctor_id'));
    }
}
