<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    const DIRECTORY = 'front.lessons';

    public function show(Course $course, Lesson $lesson)
    {
        // Check if lesson is published
        if (!$lesson->is_published) {
            abort(404);
        }

        // Verify that the lesson belongs to the course
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        // Check access control
        $enrollment = null;
        
        if (!$lesson->is_free) {
            // Paid lesson - require enrollment
            if (!auth()->check()) {
                return redirect()->route('login')->with('error', 'Please login to access this lesson');
            }
            
            $enrollment = Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->whereIn('status', ['enrolled', 'completed'])
                ->first();
            
            if (!$enrollment) {
                return redirect()->route('front.courses.show', $course)
                    ->with('error', 'You must enroll in this course to access paid lessons');
            }
        } else {
            // Free lesson - available to all (but still track progress if authenticated)
            if (auth()->check()) {
                $enrollment = Enrollment::where('user_id', auth()->id())
                    ->where('course_id', $course->id)
                    ->whereIn('status', ['enrolled', 'completed'])
                    ->first();
            }
        }

        // Load course with all lessons (ordered)
        $course->load(['lessons' => function ($query) {
            $query->published()->orderBy('lesson_order', 'asc');
        }]);

        // Get all lessons for sidebar
        $lessons = $course->lessons;

        // Check if current lesson is watched by user (if authenticated)
        $isWatched = false;
        if (auth()->check() && $enrollment) {
            $isWatched = $lesson->isWatchedBy(auth()->id());
            
            // Mark lesson as watched automatically when viewing
            if (!$isWatched) {
                $this->markLessonAsWatched($lesson, $enrollment);
                $isWatched = true;
            }
        }

        // Get previous and next lessons
        $currentIndex = $lessons->search(function ($item) use ($lesson) {
            return $item->id === $lesson->id;
        });
        
        $previousLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        // Get course progress (if enrolled)
        $progressPercentage = 0;
        if ($enrollment) {
            $progressPercentage = $enrollment->progress_percentage;
        }

        return view(self::DIRECTORY . ".show", \get_defined_vars())
            ->with('directory', self::DIRECTORY);
    }

    public function markAsWatched(Lesson $lesson)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to track progress'
            ], 401);
        }

        // Get enrollment for this course
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $lesson->course_id)
            ->whereIn('status', ['enrolled', 'completed'])
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You must be enrolled in this course to track progress'
            ], 403);
        }

        // Mark lesson as watched
        $this->markLessonAsWatched($lesson, $enrollment);

        // Refresh enrollment to get updated progress
        $enrollment->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Lesson marked as watched',
            'progress_percentage' => $enrollment->progress_percentage
        ]);
    }

   
    private function markLessonAsWatched(Lesson $lesson, Enrollment $enrollment)
    {
        // Create LessonProgress record if it doesn't exist
        LessonProgress::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'lesson_id' => $lesson->id,
            ],
            [
                'enrollment_id' => $enrollment->id,
                'watched_at' => now(),
            ]
        );

        // Update enrollment progress percentage
        $enrollment->updateProgressPercentage();
    }
}
