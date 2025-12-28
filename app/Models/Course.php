<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
class Course extends Model
{
    use HasFactory, HasSlug;

   
    protected $guarded = ['id'];

   //casts
   protected $casts = [
    'price' => 'decimal:2',
    'discount_price' => 'decimal:2',
    'duration' => 'integer',
    'sort_order' => 'integer',
   
];
    
   // get image url
    public function getImageUrl()
    {
        if ($this->image) {
            // If image doesn't start with 'courses/', assume it's just a filename
            // and prepend 'courses/' to it
            $imagePath = $this->image;
            if (!str_starts_with($imagePath, 'courses/')) {
                $imagePath = 'courses/' . $imagePath;
            }
            return asset('storage/' . $imagePath);
        }
        return null;
    }


    // relationships
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('status', 'progress_percentage', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
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


    // get final price

    public function getFinalPrice()
    {
        return $this->discount_price ?? $this->price;
    }

    // check if course has discount
    public function hasDiscount()
    {
        return $this->discount_price !== null 
            && $this->discount_price < $this->price;
    }

    // get discount percentage
    public function getDiscountPercentage()
    {
        if ($this->hasDiscount()) {
            $discount = $this->price - $this->discount_price;
            return round(($discount / $this->price) * 100);
        }
        return 0;
    }
    
 
      /////////////////////// SCOPES ///////////////////////

    // Get only published courses.
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Get courses by category.
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeFree($query)
    {
        return $query->where('price', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('price', '>', 0);
    }   



    // Get formatted duration.
    public function getFormattedDuration()
    {
        if (!$this->duration) {
            return 'N/A';
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} hours {$minutes} minutes";
        } elseif ($hours > 0) {
            return "{$hours} hours";
        } else {
            return "{$minutes} minutes";
        }
    }

    // enrollment helper methods
    public function enrolledStudentsCount()
    {
        return $this->enrollments()->where('id', '!=', null)->count();
    }

    public function isEnrolledBy($userId)
    {
        return $this->enrollments()->where('user_id', $userId)->exists();
    }

    public function canEnroll($userId)
    {
        // Check if user is not already enrolled and course is published
        return !$this->isEnrolledBy($userId) && $this->status === 'published';
    }

    // lesson helper methods
    public function getLessonsCount()
    {
        return $this->lessons()->where('is_published', true)->count();
    }

    public function getPublishedLessons()
    {
        return $this->lessons()->where('is_published', true)->orderBy('lesson_order', 'asc')->get();
    }

    /**
     * Get the total count of published lessons in this course.
     * 
     * This method counts only published lessons (is_published = true).
     * Used for calculating progress percentage.
     * 
     * @return int The total number of published lessons
     */
    public function getTotalPublishedLessons()
    {
        return $this->lessons()->where('is_published', true)->count();
    }

    /**
     * Get the count of watched lessons by a specific user in this course.
     * 
     * This method counts how many lessons in this course have been watched
     * by the given user. It uses the LessonProgress table to check
     * which lessons have been marked as watched.
     * 
     * @param int|null $userId The user ID to check (null if not authenticated)
     * @return int The number of watched lessons by the user
     */
    public function getWatchedLessonsCount($userId)
    {
        if (!$userId) {
            return 0;
        }

        // Get all published lesson IDs in this course
        $lessonIds = $this->lessons()
            ->where('is_published', true)
            ->pluck('id')
            ->toArray();

        if (empty($lessonIds)) {
            return 0;
        }

        // Count how many of these lessons have been watched by the user
        return \App\Models\LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $lessonIds)
            ->count();
    }

    // review helper methods
    /**
     * Get the average rating for this course.
     * 
     * This method calculates the average rating from all approved reviews.
     * Returns a float value between 1 and 5, or 0 if no reviews exist.
     * 
     * @return float The average rating (rounded to 2 decimal places)
     */
    public function getAverageRating()
    {
        $avgRating = $this->reviews()
            ->approved()
            ->avg('rating');
        
        return $avgRating ? round($avgRating, 2) : 0;
    }

    /**
     * Get the total count of approved reviews for this course.
     * 
     * @return int The number of approved reviews
     */
    public function getReviewsCount()
    {
        return $this->reviews()
            ->approved()
            ->count();
    }

    /**
     * Get the rating distribution for this course.
     * 
     * Returns an array with counts for each rating (1-5 stars).
     * Example: [1 => 0, 2 => 2, 3 => 5, 4 => 10, 5 => 8]
     * 
     * @return array Array with rating as key and count as value
     */
    public function getRatingDistribution()
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->reviews()
                ->approved()
                ->where('rating', $i)
                ->count();
        }
        return $distribution;
    }

    /**
     * Check if a user has reviewed this course.
     * 
     * @param int|null $userId The user ID to check (null if not authenticated)
     * @return bool True if user has reviewed, false otherwise
     */
    public function hasUserReviewed($userId)
    {
        if (!$userId) {
            return false;
        }

        return $this->reviews()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Get the review written by a specific user for this course.
     * 
     * @param int|null $userId The user ID to check (null if not authenticated)
     * @return Review|null The review if exists, null otherwise
     */
    public function getUserReview($userId)
    {
        if (!$userId) {
            return null;
        }

        return $this->reviews()
            ->where('user_id', $userId)
            ->first();
    }

}
