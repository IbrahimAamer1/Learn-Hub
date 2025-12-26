<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\Admin;
use App\Models\User;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    const DIRECTORY = 'back.courses';

    function __construct()
    {
        // $this->middleware('check_permission:manage_courses')->only(['index', 'getData']);
        // $this->middleware('check_permission:create_course')->only(['create', 'store']);
        // $this->middleware('check_permission:show_course')->only(['show']);
        // $this->middleware('check_permission:edit_course')->only(['edit', 'update']);
        // $this->middleware('check_permission:delete_course')->only(['destroy']);
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
        $instructors = User::where('type', 'instructor')->get();
        return view(self::DIRECTORY . ".create", get_defined_vars());
    }

    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $imagename = time() . '.' . $request->image->getClientOriginalName();
            $data['image'] = $request->file('image')->storeAs('courses', $imagename, 'public');
        }
        Course::create($data);
        return redirect()->route(self::DIRECTORY . '.index')->with('success', __('messages.sent') ?? 'Course created successfully');
    }

    public function show(Course $course)
    {
        $course->load(['category', 'instructor']);
        return view(self::DIRECTORY . ".show", \get_defined_vars());
    }

    public function edit(Course $course)
    {
        $categories = Category::active()->get();
        $instructors = User::where('type', 'instructor')->get();
        return view(self::DIRECTORY . ".edit", \get_defined_vars());
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $data = $request->validated();

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
        if ($course->image && Storage::disk('public')->exists($course->image)) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return response()->json([
            'success' => __('messages.deleted') ?? 'Course deleted successfully'
        ]);
    }
}