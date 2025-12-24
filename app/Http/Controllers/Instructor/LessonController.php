<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    const DIRECTORY = 'instructor.lessons';

    /**
     * Verify that the lesson's course belongs to the authenticated instructor.
     * 
     * @param Lesson $lesson
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function verifyOwnership(Lesson $lesson)
    {
        if ($lesson->course->instructor_id !== Auth::guard('admin')->id()) {
            abort(403, 'You do not have permission to access this lesson');
        }
    }

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
        $order = $data['order'] ?? 'lesson_order';
        $sort = $data['sort'] ?? 'asc';
        $perpage = $data['perpage'] ?? 10;
        $start = $data['start'] ?? null;
        $end = $data['end'] ?? null;
        $word = $data['word'] ?? null;
        $is_published = $data['is_published'] ?? null;
        $course_id = $data['course_id'] ?? null;

        // Get instructor's course IDs
        $courseIds = Course::where('instructor_id', $instructorId)->pluck('id');

        $data = Lesson::with(['course'])
            ->whereIn('course_id', $courseIds) // Only lessons from instructor's courses
            ->when($is_published !== null, function ($q) use ($is_published) {
                $q->where('is_published', $is_published);
            })
            ->when($course_id !== null, function ($q) use ($course_id, $instructorId) {
                // Verify course belongs to instructor
                $course = Course::find($course_id);
                if ($course && $course->instructor_id === $instructorId) {
                    $q->where('course_id', $course_id);
                }
            })
            ->when($word != null, function ($q) use ($word) {
                $q->where('title', 'like', '%' . $word . '%')
                  ->orWhere('description', 'like', '%' . $word . '%');
            })
            ->when($start != null, function ($q) use ($start) {
                $q->whereDate('created_at', '>=', $start);
            })
            ->when($end != null, function ($q) use ($end) {
                $q->whereDate('created_at', '<=', $end);
            })
            ->orderby($order, $sort)
            ->paginate($perpage);

        return \get_defined_vars();
    }

    public function create()
    {
        $instructorId = Auth::guard('admin')->id();
        $courses = Course::where('instructor_id', $instructorId)->get();
        return view(self::DIRECTORY . ".create", get_defined_vars());
    }

    public function store(StoreLessonRequest $request)
    {
        // Verify course belongs to instructor
        $course = Course::findOrFail($request->course_id);
        $this->verifyCourseOwnership($course);

        $data = $request->validated();
        if ($request->hasFile('video_file')) {
            $videoName = time() . '.' . $request->video_file->getClientOriginalName();
            $data['video_file'] = $request->file('video_file')->storeAs('lessons', $videoName, 'public');
        }
        Lesson::create($data);
        return redirect()->route(self::DIRECTORY . '.index')->with('success', __('messages.sent') ?? 'Lesson created successfully');
    }

    public function show(Lesson $lesson)
    {
        $this->verifyOwnership($lesson);
        $lesson->load(['course']);
        return view(self::DIRECTORY . ".show", \get_defined_vars());
    }

    public function edit(Lesson $lesson)
    {
        $this->verifyOwnership($lesson);
        $instructorId = Auth::guard('admin')->id();
        $courses = Course::where('instructor_id', $instructorId)->get();
        return view(self::DIRECTORY . ".edit", \get_defined_vars());
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        $this->verifyOwnership($lesson);
        
        // If course_id is being changed, verify new course belongs to instructor
        if ($request->has('course_id') && $request->course_id != $lesson->course_id) {
            $course = Course::findOrFail($request->course_id);
            $this->verifyCourseOwnership($course);
        }

        $data = $request->validated();

        if ($request->hasFile('video_file')) {
            if ($lesson->video_file && Storage::disk('public')->exists($lesson->video_file)) {
                Storage::disk('public')->delete($lesson->video_file);
            }   
            $videoName = time() . '.' . $request->file('video_file')->getClientOriginalName();
            $data['video_file'] = $request->file('video_file')->storeAs('lessons', $videoName, 'public');
        }

        $lesson->update($data);

        return response()->json([
            'success' => __('messages.updated') ?? 'Lesson updated successfully'
        ]);
    }

    public function destroy(Lesson $lesson)
    {
        $this->verifyOwnership($lesson);
        
        if ($lesson->video_file && Storage::disk('public')->exists($lesson->video_file)) {
            Storage::disk('public')->delete($lesson->video_file);
        }

        $lesson->delete();

        return response()->json([
            'success' => __('messages.deleted') ?? 'Lesson deleted successfully'
        ]);
    }
}
