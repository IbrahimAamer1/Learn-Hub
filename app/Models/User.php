<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\UpdatePasswordNotification;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UpdatePasswordNotification($token));
    }

    // relationships
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Student relationships (courses enrolled in)
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot('status', 'progress_percentage', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Instructor relationships (courses created as instructor)
    public function instructorCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    // helper methods
    public function isEnrolledIn($courseId)
    {
        return $this->enrollments()->where('course_id', $courseId)->exists();
    }

    /**
     * Check if user is a student.
     * 
     * @return bool True if user type is 'student', false otherwise
     */
    public function isStudent()
    {
        return $this->type === 'student';
    }

    /**
     * Check if user is an instructor.
     * 
     * @return bool True if user type is 'instructor', false otherwise
     */
    public function isInstructor()
    {
        return $this->type === 'instructor';
    }

    /////////////////////// INSTRUCTOR METHODS ///////////////////////

    /**
     * Get the total count of courses created by this instructor.
     * 
     * This method counts all courses (both published and draft) created by this instructor.
     * 
     * @return int Total number of courses
     */
    public function getTotalCourses()
    {
        return $this->instructorCourses()->count();
    }

    /**
     * Get the total count of unique students enrolled in instructor's courses.
     * 
     * This method counts distinct users who are enrolled in at least one course
     * created by this instructor.
     * 
     * @return int Total number of unique students
     */
    public function getTotalStudents()
    {
        return \App\Models\Enrollment::whereIn('course_id', $this->instructorCourses()->pluck('id'))
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Get the total count of enrollments in instructor's courses.
     * 
     * This method counts all enrollment records (including duplicates if a student
     * is enrolled in multiple courses) in courses created by this instructor.
     * 
     * @return int Total number of enrollments
     */
    public function getTotalEnrollments()
    {
        return \App\Models\Enrollment::whereIn('course_id', $this->instructorCourses()->pluck('id'))
            ->count();
    }

    /////////////////////// MEDIA LIBRARY METHODS ///////////////////////

    /**
     * Register media collections for the user
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif']);
    }

    /**
     * Register media conversions (resize images automatically)
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(400)
            ->height(400);
    }

    /**
     * Get user avatar URL
     * 
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return $this->getFirstMediaUrl('avatar') 
            ?: 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }
}

