<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    //casts
    protected $casts = [
        'progress_percentage' => 'integer',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonProgresses()
    {
        return $this->hasMany(LessonProgress::class);
    }

    /////////////////////// SCOPES ///////////////////////

    // Get only enrolled status.
    public function scopeEnrolled($query)
    {
        return $query->where('status', 'enrolled');
    }

    // Get only completed status.
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Get only cancelled status (for admin view only - students cannot cancel).
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Get enrollments by user.
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Get enrollments by course.
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /////////////////////// METHODS ///////////////////////

    // Mark enrollment as completed.
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'progress_percentage' => 100,
            'completed_at' => now(),
        ]);
    }

    // Cancel enrollment (for admin only - students cannot cancel).
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Update the progress percentage based on watched lessons.
     * 
     * This method calculates the progress percentage by:
     * 1. Getting the total number of published lessons in the course
     * 2. Getting the number of watched lessons by the user
     * 3. Calculating: (watched lessons / total lessons) * 100
     * 4. Updating the progress_percentage field
     * 5. If progress reaches 100%, automatically mark enrollment as completed
     * 
     * @return void
     */
    public function updateProgressPercentage()
    {
        $course = $this->course;
        $totalLessons = $course->getTotalPublishedLessons();
        
        if ($totalLessons == 0) {
            // No lessons in course, progress is 0
            $this->update(['progress_percentage' => 0]);
            return;
        }

        // Get watched lessons count for this user in this course
        $watchedLessons = $course->getWatchedLessonsCount($this->user_id);
        
        // Calculate progress percentage
        $progressPercentage = round(($watchedLessons / $totalLessons) * 100);
        
        // Update progress
        $this->update(['progress_percentage' => $progressPercentage]);
        
        // If progress reaches 100%, mark as completed
        if ($progressPercentage >= 100) {
            $this->markAsCompleted();
        }
    }
}
