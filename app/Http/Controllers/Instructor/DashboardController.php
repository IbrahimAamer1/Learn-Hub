<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    const DIRECTORY = 'instructor.dashboard';

    /**
     * Display the instructor dashboard overview.
     * 
     * This method shows statistics and recent activity for the instructor:
     * - Total courses (published and draft)
     * - Total students (unique)
     * - Total enrollments
     * - Total lessons
     * - Recent enrollments (last 10)
     * - Recent courses (last 5)
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $instructor = Auth::guard('admin')->user();
        
        // Get instructor's course IDs
        $courseIds = $instructor->courses()->pluck('id');

        // Statistics
        $totalCourses = $instructor->getTotalCourses();
        $publishedCourses = $instructor->courses()->where('status', 'published')->count();
        $draftCourses = $instructor->courses()->where('status', 'draft')->count();
        $totalStudents = $instructor->getTotalStudents();
        $totalEnrollments = $instructor->getTotalEnrollments();
        $totalLessons = Lesson::whereIn('course_id', $courseIds)->count();

        // Recent enrollments (last 10)
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->whereIn('course_id', $courseIds)
            ->orderBy('enrolled_at', 'desc')
            ->limit(10)
            ->get();

        // Recent courses (last 5)
        $recentCourses = $instructor->courses()
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view(self::DIRECTORY . ".index", \get_defined_vars())
            ->with('directory', self::DIRECTORY);
    }
}
