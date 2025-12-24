@extends('layouts.instructor.master')
@section('title', __('lang.student_details') ?? 'Student Details')
@section('students_active', 'active bg-light')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2 class="h5 page-title">{{ __('lang.student_details') ?? 'Student Details' }}</h2>
                <div>
                    <a href="{{ route('instructor.students.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> {{ __('lang.back') ?? 'Back' }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Student Info -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('lang.student_information') ?? 'Student Information' }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar avatar-xl mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-label-primary" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                                {{ substr($user->name, 0, 1) }}
                            </span>
                        </div>
                        <h5>{{ $user->name }}</h5>
                        <p class="text-muted">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollment Details -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('lang.enrollment_details') ?? 'Enrollment Details' }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">{{ __('lang.course') ?? 'Course' }}</th>
                            <td>{{ $course->title }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('lang.enrolled_at') ?? 'Enrolled At' }}</th>
                            <td>{{ $enrollment->enrolled_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('lang.progress') ?? 'Progress' }}</th>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $enrollment->progress_percentage }}%"
                                         aria-valuenow="{{ $enrollment->progress_percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $enrollment->progress_percentage }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('lang.status') ?? 'Status' }}</th>
                            <td>
                                @if($enrollment->status == 'enrolled')
                                    <span class="badge bg-label-primary">{{ __('lang.enrolled') ?? 'Enrolled' }}</span>
                                @elseif($enrollment->status == 'completed')
                                    <span class="badge bg-label-success">{{ __('lang.completed') ?? 'Completed' }}</span>
                                @else
                                    <span class="badge bg-label-danger">{{ __('lang.cancelled') ?? 'Cancelled' }}</span>
                                @endif
                            </td>
                        </tr>
                        @if($enrollment->completed_at)
                        <tr>
                            <th>{{ __('lang.completed_at') ?? 'Completed At' }}</th>
                            <td>{{ $enrollment->completed_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Lessons Progress -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('lang.lessons_progress') ?? 'Lessons Progress' }}</h5>
                </div>
                <div class="card-body">
                    @if($lessons->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>{{ __('lang.lesson') ?? 'Lesson' }}</th>
                                        <th>{{ __('lang.status') ?? 'Status' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lessons as $lesson)
                                        <tr>
                                            <td>{{ $lesson->lesson_order }}</td>
                                            <td>
                                                <div>
                                                    <div class="fw-semibold">{{ $lesson->title }}</div>
                                                    @if($lesson->description)
                                                        <small class="text-muted">{{ Str::limit($lesson->description, 100) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($lesson->is_watched)
                                                    <span class="badge bg-label-success">
                                                        <i class="bx bx-check-circle"></i> {{ __('lang.watched') ?? 'Watched' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-secondary">
                                                        <i class="bx bx-time"></i> {{ __('lang.not_watched') ?? 'Not Watched' }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-info-circle display-4 text-muted"></i>
                            <p class="text-muted mt-2">{{ __('lang.no_lessons_available') ?? 'No lessons available for this course.' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

