<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\AdminPasswordNotification;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;
class Admin extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        $this->notify(new AdminPasswordNotification($token));
    }


    public function password() : Attribute
    {
        return Attribute::make(
           set: fn ($value) => $value != null ? Hash::make($value) : $this->password,
        );
    }

    /////////////////////// RELATIONSHIPS ///////////////////////

    /**
     * Get all courses created by this instructor.
     * 
     * This relationship returns all courses where instructor_id matches this admin's id.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /////////////////////// METHODS ///////////////////////

    /**
     * Check if this admin is an instructor (has at least one course).
     * 
     * An admin is considered an instructor if they have created at least one course.
     * 
     * @return bool True if admin has courses, false otherwise
     */
    public function isInstructor()
    {
        return $this->courses()->exists();
    }

    /**
     * Get the total count of courses created by this instructor.
     * 
     * This method counts all courses (both published and draft) created by this instructor.
     * 
     * @return int Total number of courses
     */
    public function getTotalCourses()
    {
        return $this->courses()->count();
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
        return \App\Models\Enrollment::whereIn('course_id', $this->courses()->pluck('id'))
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
        return \App\Models\Enrollment::whereIn('course_id', $this->courses()->pluck('id'))
            ->count();
    }
}

