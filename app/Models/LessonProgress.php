<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    //casts
    protected $casts = [
        'watched_at' => 'datetime',
    ];

    // relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /////////////////////// SCOPES ///////////////////////

    // Get progress by user.
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Get progress by lesson.
    public function scopeByLesson($query, $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }

    // Get progress by enrollment.
    public function scopeByEnrollment($query, $enrollmentId)
    {
        return $query->where('enrollment_id', $enrollmentId);
    }
}
