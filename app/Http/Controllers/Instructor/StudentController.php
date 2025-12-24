<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    const DIRECTORY = 'instructor.students';

    /**
     * Verify that the course belongs to the authenticated instructor.
     * 
     * @param Course $course
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function verifyCourseOwnership(Course $course)
    {
        if ($course->instructor_id !== Auth::guard('admin')->id()) {
            abort(403, 'You do not have permission to access this course');
        }
    }

    /**
     * Display a listing of students enrolled in instructor's courses.
     * 
     * Shows students with:
     * - Course name
     * - Enrollment date
     * - Progress percentage
     * - Status (enrolled, completed)
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = $this->getData($request->all());
        $instructorId = Auth::guard('admin')->id();
        $courses = Course::where('instructor_id', $instructorId)->get();
        return view(self::DIRECTORY . ".index", \get_defined_vars())
            ->with('directory', self::DIRECTORY);
    }

    public function getData($data)
    {
        $instructorId = Auth::guard('admin')->id();
        $perpage = $data['perpage'] ?? 10;
        $word = $data['word'] ?? null;
        $course_id = $data['course_id'] ?? null;
        $status = $data['status'] ?? null;

        // Get instructor's course IDs
        $courseIds = Course::where('instructor_id', $instructorId)->pluck('id');

        $query = Enrollment::with(['user', 'course'])
            ->whereIn('course_id', $courseIds) // Only enrollments in instructor's courses
            ->when($course_id !== null, function ($q) use ($course_id, $instructorId) {
                // Verify course belongs to instructor
                $course = Course::find($course_id);
                if ($course && $course->instructor_id === $instructorId) {
                    $q->where('course_id', $course_id);
                }
            })
            ->when($status !== null, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($word != null, function ($q) use ($word) {
                $q->whereHas('user', function ($query) use ($word) {
                    $query->where('name', 'like', '%' . $word . '%')
                          ->orWhere('email', 'like', '%' . $word . '%');
                });
            })
            ->orderBy('enrolled_at', 'desc');

        $data = $query->paginate($perpage);

        return \get_defined_vars();
    }

    /**
     * Display student details for a specific course.
     * 
     * Shows:
     * - Student info
     * - Enrollment details
     * - Lessons progress (watched/not watched)
     * - Overall progress
     * 
     * @param User $user
     * @param Course $course
     * @return \Illuminate\View\View
     */
    public function show(User $user, Course $course)
    {
        $this->verifyCourseOwnership($course);

        // Get enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Get course lessons
        $lessons = $course->lessons()
            ->published()
            ->orderBy('lesson_order', 'asc')
            ->get();

        // Get watched lessons
        $watchedLessonIds = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();

        // Mark lessons as watched
        $lessons = $lessons->map(function ($lesson) use ($watchedLessonIds) {
            $lesson->is_watched = in_array($lesson->id, $watchedLessonIds);
            return $lesson;
        });

        return view(self::DIRECTORY . ".show", \get_defined_vars());
    }
}
