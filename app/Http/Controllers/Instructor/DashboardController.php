<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Review;
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
        $instructor = Auth::user();
        
        // Get instructor's course IDs
        $courseIds = $instructor->instructorCourses()->pluck('id');

        // Statistics
        $totalCourses = $instructor->getTotalCourses();
        $publishedCourses = $instructor->instructorCourses()->where('status', 'published')->count();
        $draftCourses = $instructor->instructorCourses()->where('status', 'draft')->count();
        $totalStudents = $instructor->getTotalStudents();
        $totalEnrollments = $instructor->getTotalEnrollments();
        $totalLessons = Lesson::whereIn('course_id', $courseIds)->count();

        // Rating statistics
        $totalReviews = Review::whereIn('course_id', $courseIds)
            ->approved()
            ->count();
        
        $averageRating = 0;
        if ($totalReviews > 0) {
            $averageRating = Review::whereIn('course_id', $courseIds)
                ->approved()
                ->avg('rating');
            $averageRating = round($averageRating, 2);
        }

        // Rating distribution (1-5 stars)
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = Review::whereIn('course_id', $courseIds)
                ->approved()
                ->where('rating', $i)
                ->count();
        }

        // Recent enrollments (last 10)
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->whereIn('course_id', $courseIds)
            ->orderBy('enrolled_at', 'desc')
            ->limit(10)
            ->get();

        // Recent courses (last 5)
        $recentCourses = $instructor->instructorCourses()
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view(self::DIRECTORY . ".index", \get_defined_vars())
            ->with('directory', self::DIRECTORY);
    }
}
