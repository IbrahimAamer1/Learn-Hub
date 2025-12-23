<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Lesson extends Model
{
    use HasFactory, HasSlug;

    protected $guarded = ['id'];

    //casts
    protected $casts = [
        'lesson_order' => 'integer',
        'is_free' => 'boolean',
        'is_published' => 'boolean',
    ];

    // get video url
    public function getVideoUrl()
    {
        if ($this->video_file) {
            // If video_file doesn't start with 'lessons/', assume it's just a filename
            // and prepend 'lessons/' to it
            $videoPath = $this->video_file;
            if (!str_starts_with($videoPath, 'lessons/')) {
                $videoPath = 'lessons/' . $videoPath;
            }
            return asset('storage/' . $videoPath);
        }
        return null;
    }

    // relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonProgresses()
    {
        return $this->hasMany(LessonProgress::class);
    }

    // slug options
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    // get route key name
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /////////////////////// SCOPES ///////////////////////

    // Get only published lessons.
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Get free lessons.
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    // Get lessons by course.
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /////////////////////// METHODS ///////////////////////

    /**
     * Check if lesson is watched by a specific user.
     * 
     * This method checks if there's a LessonProgress record
     * for the given user and this lesson, indicating the lesson was watched.
     * 
     * @param int|null $userId The user ID to check (null if not authenticated)
     * @return bool True if lesson is watched by the user, false otherwise
     */
    public function isWatchedBy($userId)
    {
        if (!$userId) {
            return false;
        }

        return $this->lessonProgresses()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Check if a user can access this lesson.
     * 
     * This method determines access based on:
     * - Free lessons: Available to everyone (return true)
     * - Paid lessons: Only available if user is enrolled (via enrollment parameter)
     * 
     * @param int|null $userId The user ID to check (null if not authenticated)
     * @param Enrollment|null $enrollment The enrollment record (if user is enrolled)
     * @return bool True if user can access the lesson, false otherwise
     */
    public function canAccess($userId, $enrollment = null)
    {
        // Free lessons are available to everyone
        if ($this->is_free) {
            return true;
        }

        // Paid lessons require enrollment
        if (!$userId || !$enrollment) {
            return false;
        }

        // Check if enrollment is active (status = 'enrolled')
        return $enrollment->status === 'enrolled';
    }
}
