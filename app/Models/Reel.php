<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'Health',
        'Education',
        'Business',
        'Finance',
        'Technology',
        'Marketing',
        'Digital Marketing',
        'Human Resources',
        'Real Estate',
        'Accounting',
        'Nutrition',
        'Yoga & Fitness',
        'Mental Wellness',
        'Pet Care',
        'Astrology',
        'Fashion & Beauty',
        'Home & Interior',
        'Media & Entertainment',
        'Music',
        'Agriculture',
        'Government & Civil Services',
        'Entrepreneurship',
        'Influencer',
        'Other',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'video',
        'views_count',
        'value_count',
        'comments_count',
        'shares_count',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'views_count' => 'integer',
        'value_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'criteria_notification_sent' => 'boolean',
    ];

    public function advisor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function views()
    {
        return $this->hasMany(ReelView::class, 'reel_id');
    }

    public function values()
    {
        return $this->hasMany(ReelValue::class, 'reel_id');
    }

    public function comments()
    {
        return $this->hasMany(ReelComment::class, 'reel_id');
    }

    public function getVideoAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return (env('APP_ENV') == 'local')
            ? asset('storage/reels/' . $value)
            : asset('storage/app/public/reels/' . $value);
    }

    public function checkAndNotifyAdminForEarningCriteria()
    {
        $criteria = \App\Models\ReelEarningCriteria::where('is_active', 1)->first();

        if (!$criteria) {
            return;
        }

        $isCriteriaSatisfied = $this->views_count >= $criteria->minimum_views && $this->value_count >= $criteria->minimum_values;

        $existingNotification = \App\Models\Notification::where('title', 'Reel Achieved Earning Criteria')
            ->where('description', 'like', "%Reel '{$this->title}%")
            ->where('status', 1)
            ->latest()
            ->first();

        if ($isCriteriaSatisfied) {
            if (!$existingNotification) {
                $advisorName = $this->advisor ? ($this->advisor->name ?: 'N/A') : 'N/A';

                \App\Models\Notification::create([
                    'title' => 'Reel Achieved Earning Criteria',
                    'description' => "Reel '{$this->title}' by advisor '{$advisorName}' has achieved the earning criteria ({$criteria->minimum_views} views and {$criteria->minimum_values} values).",
                    'status' => 1,
                ]);

                $this->criteria_notification_sent = true;
                $this->save();
            }
        } else {
            if ($existingNotification && $this->criteria_notification_sent) {
                $existingNotification->update(['status' => 0]);
                $this->criteria_notification_sent = false;
                $this->save();
            }
        }
    }
}
