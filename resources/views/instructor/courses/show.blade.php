@extends('layouts.instructor.master')
@section('title', $course->title ?? 'Course Details')
@section('courses_active', 'active bg-light')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2 class="h5 page-title">{{ $course->title ?? 'Course Details' }}</h2>
                <div>
                    <a href="{{ route('instructor.courses.edit', $course) }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-edit"></i> {{ __('lang.edit') ?? 'Edit' }}
                    </a>
                    <a href="{{ route('instructor.courses.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> {{ __('lang.back') ?? 'Back' }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            @if($course->image)
                <img src="{{ $course->getImageUrl() }}" alt="{{ $course->title }}" class="img-fluid rounded mb-3">
            @else
                <div class="bg-light rounded p-5 text-center mb-3">
                    <i class="bx bx-image display-4 text-muted"></i>
                    <p class="text-muted mt-2">No Image</p>
                </div>
            @endif
        </div>
        <div class="col-md-8">
            <table class="table table-bordered">
                <tr>
                    <th width="30%">{{ __('lang.title') ?? 'Title' }}</th>
                    <td>{{ $course->title }}</td>
                </tr>
                <tr>
                    <th>{{ __('lang.slug') ?? 'Slug' }}</th>
                    <td>{{ $course->slug }}</td>
                </tr>
                <tr>
                    <th>{{ __('lang.category') ?? 'Category' }}</th>
                    <td>
                        @if($course->category)
                            <span class="badge bg-label-info">{{ $course->category->name }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>{{ __('lang.instructor') ?? 'Instructor' }}</th>
                    <td>
                        @if($course->instructor)
                            {{ $course->instructor->name }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>{{ __('lang.level') ?? 'Level' }}</th>
                    <td>
                        <span class="badge bg-label-{{ $course->level == 'beginner' ? 'success' : ($course->level == 'intermediate' ? 'warning' : 'danger') }}">
                            {{ ucfirst($course->level) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>{{ __('lang.price') ?? 'Price' }}</th>
                    <td>
                        @if($course->hasDiscount())
                            <span class="text-decoration-line-through text-muted">{{ $course->price }}</span>
                            <span class="text-danger fw-bold ms-2">{{ $course->getFinalPrice() }}</span>
                            <span class="badge bg-label-danger ms-2">{{ $course->getDiscountPercentage() }}% {{ __('lang.off') ?? 'Off' }}</span>
                        @else
                            <span class="fw-bold">{{ $course->getFinalPrice() }}</span>
                        @endif
                    </td>
                </tr>
                @if($course->duration)
                <tr>
                    <th>{{ __('lang.duration') ?? 'Duration' }}</th>
                    <td>{{ $course->getFormattedDuration() }}</td>
                </tr>
                @endif
                @if($course->language)
                <tr>
                    <th>{{ __('lang.language') ?? 'Language' }}</th>
                    <td>{{ strtoupper($course->language) }}</td>
                </tr>
                @endif
                <tr>
                    <th>{{ __('lang.status') ?? 'Status' }}</th>
                    <td>
                        @if($course->status == 'published')
                            <span class="badge bg-label-success">{{ __('lang.published') ?? 'Published' }}</span>
                        @else
                            <span class="badge bg-label-warning">{{ __('lang.draft') ?? 'Draft' }}</span>
                        @endif
                    </td>
                </tr>
                @if($course->description)
                <tr>
                    <th>{{ __('lang.description') ?? 'Description' }}</th>
                    <td>{{ $course->description }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Students Enrolled -->
    @if($course->enrollments && $course->enrollments->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('lang.enrolled_students') ?? 'Enrolled Students' }} ({{ $course->enrollments->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('lang.student') ?? 'Student' }}</th>
                                    <th>{{ __('lang.enrolled_at') ?? 'Enrolled At' }}</th>
                                    <th>{{ __('lang.progress') ?? 'Progress' }}</th>
                                    <th>{{ __('lang.status') ?? 'Status' }}</th>
                                    <th>{{ __('lang.actions') ?? 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->enrollments as $enrollment)
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
                                        <td>
                                            <a href="{{ route('instructor.students.show', ['user' => $enrollment->user, 'course' => $course]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-show"></i> {{ __('lang.view') ?? 'View' }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
