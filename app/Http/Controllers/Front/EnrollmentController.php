<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Http\Requests\StoreEnrollmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    const DIRECTORY = 'front.enrollments';

    public function store(StoreEnrollmentRequest $request)
    {
        $enrollment = Enrollment::create([
            'user_id' => Auth ::user()->id,
            'course_id' => $request->course_id,
            'status' => 'enrolled',
            'progress_percentage' => 0,
            'enrolled_at' => now(),
        ]);

        return response()->json([
            'success' => __('messages.enrolled_successfully') ?? 'Successfully enrolled in course',
            'enrollment' => $enrollment
        ]);
    }

    public function index()
    {
        $enrollments = Enrollment::with(['course' => function ($query) {
            $query->with(['lessons' => function ($q) {
                $q->published()->orderBy('lesson_order', 'asc');
            }]);
        }])
            ->where('user_id', Auth ::user()->id)
            ->orderBy('enrolled_at', 'desc')
            ->paginate(12);

        return view(self::DIRECTORY . ".index", \get_defined_vars());
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        // Ensure enrollment belongs to authenticated user
        if ($enrollment->user_id !== Auth ::user()->id) {
            return response()->json([
                'error' => __('messages.unauthorized') ?? 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'progress_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        $data = [];
        
        if ($request->has('progress_percentage')) {
            $data['progress_percentage'] = $request->progress_percentage;
            
            // Auto-complete if progress reaches 100%
            if ($request->progress_percentage >= 100 && $enrollment->status !== 'completed') {
                $enrollment->markAsCompleted();
                return response()->json([
                    'success' => __('messages.progress_updated') ?? 'Progress updated successfully',
                    'completed' => true
                ]);
            }
        }

        $enrollment->update($data);

        return response()->json([
            'success' => __('messages.progress_updated') ?? 'Progress updated successfully'
        ]);
    }
}
