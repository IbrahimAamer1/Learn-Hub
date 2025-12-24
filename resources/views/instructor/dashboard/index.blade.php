@extends('layouts.instructor.master')
@section('title', __('lang.dashboard') ?? 'Dashboard')

@section('content')
    <!-- page title -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2 class="h5 page-title">{{ __('lang.dashboard') ?? 'Dashboard' }}</h2>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <x-cards.stats-card 
                title="{{ __('lang.total_courses') ?? 'Total Courses' }}"
                :value="$totalCourses"
                icon="bx-book"
                color="primary"
                :subtitle="($publishedCourses . ' ' . __('lang.published') ?? 'Published') . ' / ' . ($draftCourses . ' ' . __('lang.draft') ?? 'Draft')"
            />
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <x-cards.stats-card 
                title="{{ __('lang.total_students') ?? 'Total Students' }}"
                :value="$totalStudents"
                icon="bx-group"
                color="success"
            />
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <x-cards.stats-card 
                title="{{ __('lang.total_enrollments') ?? 'Total Enrollments' }}"
                :value="$totalEnrollments"
                icon="bx-user-check"
                color="info"
            />
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <x-cards.stats-card 
                title="{{ __('lang.total_lessons') ?? 'Total Lessons' }}"
                :value="$totalLessons"
                icon="bx-video"
                color="warning"
            />
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('lang.quick_actions') ?? 'Quick Actions' }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus-circle"></i> {{ __('lang.create_new_course') ?? 'Create New Course' }}
                        </a>
                        <a href="{{ route('instructor.courses.index') }}" class="btn btn-outline-primary">
                            <i class="bx bx-book"></i> {{ __('lang.view_all_courses') ?? 'View All Courses' }}
                        </a>
                        <a href="{{ route('instructor.students.index') }}" class="btn btn-outline-success">
                            <i class="bx bx-group"></i> {{ __('lang.view_all_students') ?? 'View All Students' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Enrollments -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('lang.recent_enrollments') ?? 'Recent Enrollments' }}</h5>
                    <a href="{{ route('instructor.students.index') }}" class="btn btn-sm btn-outline-primary">
                        {{ __('lang.view_all') ?? 'View All' }}
                    </a>
                </div>
                <div class="card-body">
                    @if($recentEnrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('lang.student') ?? 'Student' }}</th>
                                        <th>{{ __('lang.course') ?? 'Course' }}</th>
                                        <th>{{ __('lang.enrolled_at') ?? 'Enrolled At' }}</th>
                                        <th>{{ __('lang.progress') ?? 'Progress' }}</th>
                                        <th>{{ __('lang.status') ?? 'Status' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentEnrollments as $enrollment)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            {{ substr($enrollment->user->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $enrollment->user->name }}</div>
                                                        <small class="text-muted">{{ $enrollment->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $enrollment->course->title }}</td>
                                            <td>{{ $enrollment->enrolled_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $enrollment->progress_percentage }}%"
                                                         aria-valuenow="{{ $enrollment->progress_percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ $enrollment->progress_percentage }}%
                                                    </div>
                                                </div>
                                            </td>
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-info-circle display-4 text-muted"></i>
                            <p class="text-muted mt-2">{{ __('lang.no_enrollments_yet') ?? 'No enrollments yet.' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Courses -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('lang.recent_courses') ?? 'Recent Courses' }}</h5>
                    <a href="{{ route('instructor.courses.index') }}" class="btn btn-sm btn-outline-primary">
                        {{ __('lang.view_all') ?? 'View All' }}
                    </a>
                </div>
                <div class="card-body">
                    @if($recentCourses->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentCourses as $course)
                                <a href="{{ route('instructor.courses.show', $course) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $course->title }}</h6>
                                            @if($course->category)
                                                <small class="text-muted">{{ $course->category->name }}</small>
                                            @endif
                                        </div>
                                        <div>
                                            @if($course->status == 'published')
                                                <span class="badge bg-label-success">{{ __('lang.published') ?? 'Published' }}</span>
                                            @else
                                                <span class="badge bg-label-secondary">{{ __('lang.draft') ?? 'Draft' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-book display-4 text-muted"></i>
                            <p class="text-muted mt-2">{{ __('lang.no_courses_yet') ?? 'No courses yet.' }}</p>
                            <a href="{{ route('instructor.courses.create') }}" class="btn btn-sm btn-primary mt-2">
                                {{ __('lang.create_first_course') ?? 'Create First Course' }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

