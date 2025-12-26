<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    const DIRECTORY = 'instructor.courses';

    /**
     * Verify that the course belongs to the authenticated instructor.
     * 
     * @param Course $course
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function verifyOwnership(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'You do not have permission to access this course');
        }
    }

    public function index(Request $request)
    {
        $data = $this->getData($request->all());
        $categories = Category::active()->get();
        return view(self::DIRECTORY . ".index", \get_defined_vars())
            ->with('directory', self::DIRECTORY);
    }

    public function getData($data)
    {
        $instructorId = Auth::id();
        $order = $data['order'] ?? 'sort_order';
        $sort = $data['sort'] ?? 'asc';
        $perpage = $data['perpage'] ?? 10;
        $start = $data['start'] ?? null;
        $end = $data['end'] ?? null;
        $word = $data['word'] ?? null;
        $status = $data['status'] ?? null;
        $category_id = $data['category_id'] ?? null;
        $level = $data['level'] ?? null;

        $data = Course::with(['category', 'instructor'])
            ->withCount('lessons') // Count lessons for each course
            ->where('instructor_id', $instructorId) // Only instructor's courses
            ->when($status !== null, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($category_id !== null, function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            })
            ->when($level !== null, function ($q) use ($level) {
                $q->where('level', $level);
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
        $categories = Category::active()->get();
        return view(self::DIRECTORY . ".create", get_defined_vars());
    }

    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();
        
        // Auto-set instructor_id to current user
        $data['instructor_id'] = Auth::id();
        
        if ($request->hasFile('image')) {
            $imagename = time() . '.' . $request->image->getClientOriginalName();
            $data['image'] = $request->file('image')->storeAs('courses', $imagename, 'public');
        }
        
        Course::create($data);
        return redirect()->route(self::DIRECTORY . '.index')->with('success', __('messages.sent') ?? 'Course created successfully');
    }

    public function show(Course $course)
    {
        $this->verifyOwnership($course);
        $course->load(['category', 'instructor', 'lessons' => function ($query) {
            $query->orderBy('lesson_order', 'asc');
        }, 'enrollments.user']);
        return view(self::DIRECTORY . ".show", \get_defined_vars());
    }

    public function edit(Course $course)
    {
        $this->verifyOwnership($course);
        $categories = Category::active()->get();
        return view(self::DIRECTORY . ".edit", \get_defined_vars());
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $this->verifyOwnership($course);
        
        $data = $request->validated();
        
        // Prevent changing instructor_id
        unset($data['instructor_id']);

        if ($request->hasFile('image')) {
            if ($course->image && Storage::disk('public')->exists($course->image)) {
                Storage::disk('public')->delete($course->image);
            }   
            $imageName = time() . '.' . $request->file('image')->getClientOriginalName();
            $data['image'] = $request->file('image')->storeAs('courses', $imageName, 'public');
        }

        $course->update($data);

        return response()->json([
            'success' => __('messages.updated') ?? 'Course updated successfully'
        ]);
    }

    public function destroy(Course $course)
    {
        $this->verifyOwnership($course);
        
        if ($course->image && Storage::disk('public')->exists($course->image)) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return response()->json([
            'success' => __('messages.deleted') ?? 'Course deleted successfully'
        ]);
    }
}
