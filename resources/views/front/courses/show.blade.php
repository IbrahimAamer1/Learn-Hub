
@extends('front.master')
@section('title', $course->title ?? 'Course Details')

@push('styles')
    @includeIf("front.courses.pushStyles")
@endpush

@section('content')
    <div class="container-xxl py-4">
        <!-- Course Hero Section -->
        <div class="row mb-4">
            <div class="col-lg-8">
                @if($course->image)
                    <img src="{{ $course->getImageUrl() }}" class="img-fluid rounded mb-3" alt="{{ $course->title }}">
                @else
                    <div class="bg-light rounded p-5 text-center mb-3" style="height: 400px;">
                        <i class="bx bx-book display-1 text-muted"></i>
                    </div>
                @endif
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">{{ $course->title }}</h2>
                        
                        <div class="mb-3">
                            @if($course->category)
                                <span class="badge bg-label-info">{{ $course->category->name }}</span>
                            @endif
                            <span class="badge bg-label-{{ $course->level == 'beginner' ? 'success' : ($course->level == 'intermediate' ? 'warning' : 'danger') }}">
                                {{ ucfirst($course->level) }}
                            </span>
                        </div>

                        <div class="mb-3">
                            @if($course->instructor)
                                <p class="mb-1">
                                    <strong>{{ __('lang.instructor') ?? 'Instructor' }}:</strong>
                                    {{ $course->instructor->name }}
                                </p>
                            @endif
                        </div>

                        <div class="mb-3">
                            <p class="mb-1">
                                <i class="bx bx-time"></i> <strong>{{ __('lang.duration') ?? 'Duration' }}:</strong>
                                {{ $course->getFormattedDuration() }}
                            </p>
                            <p class="mb-1">
                                <i class="bx bx-group"></i> <strong>{{ __('lang.students') ?? 'Students' }}:</strong>
                                {{ $course->enrolledStudentsCount() }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <h4>
                                @if($course->hasDiscount())
                                    <span class="text-decoration-line-through text-muted small">{{ $course->price }}</span>
                                    <span class="text-danger fw-bold">{{ $course->getFinalPrice() }}</span>
                                    <span class="badge bg-label-danger ms-2">{{ $course->getDiscountPercentage() }}% {{ __('lang.off') ?? 'Off' }}</span>
                                @else
                                    <span class="fw-bold">{{ $course->getFinalPrice() == 0 ? __('lang.free') : $course->getFinalPrice() }}</span>
                                @endif
                            </h4>
                        </div>

                        <!-- Enroll Button -->
                        @auth
                            @if($isEnrolled)
                                <div class="alert alert-success">
                                    <i class="bx bx-check-circle"></i> {{ __('lang.already_enrolled') ?? 'You are already enrolled in this course' }}
                                </div>
                                @php
                                    $firstLesson = $course->lessons->where('is_published', true)->sortBy('lesson_order')->first();
                                @endphp
                                @if($firstLesson)
                                    <a href="{{ route('front.lessons.show', [$course, $firstLesson]) }}" class="btn btn-success w-100 mb-2">
                                        <i class="bx bx-play-circle"></i> {{ __('lang.start_learning') ?? 'Start Learning' }}
                                    </a>
                                @endif
                                <a href="{{ route('front.enrollments.index') }}" class="btn btn-outline-success w-100">
                                    {{ __('lang.my_enrollments') ?? 'My Enrollments' }}
                                </a>
                            @else
                                <button type="button" class="btn btn-primary w-100" id="enrollBtn" data-course-id="{{ $course->id }}">
                                    <i class="bx bx-plus-circle"></i> {{ __('lang.enroll_now') ?? 'Enroll Now' }}
                                </button>
                            @endif
                        @else
                            <a href="{{ route('front.login') }}" class="btn btn-primary w-100">
                                {{ __('lang.login_to_enroll') ?? 'Login to Enroll' }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Description -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ __('lang.description') ?? 'Description' }}</h4>
                    </div>
                    <div class="card-body">
                        <p>{{ $course->description ?? __('lang.no_description') ?? 'No description available.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Curriculum (Lessons) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ __('lang.course_curriculum') ?? 'Course Curriculum' }}</h4>
                    </div>
                    <div class="card-body">
                        @if($course->lessons && $course->lessons->count() > 0)
                            <div class="list-group">
                                @foreach($course->lessons as $lesson)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <span class="badge bg-label-secondary me-3">{{ $lesson->lesson_order }}</span>
                                            <div class="flex-grow-1">
                                                @if($isEnrolled)
                                                    <a href="{{ route('front.lessons.show', [$course, $lesson]) }}" class="text-decoration-none">
                                                        <h6 class="mb-0 text-primary">{{ $lesson->title }}</h6>
                                                    </a>
                                                @else
                                                    <h6 class="mb-0">{{ $lesson->title }}</h6>
                                                @endif
                                                @if($lesson->description)
                                                    <small class="text-muted">{{ Str::limit($lesson->description, 100) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            @if($lesson->is_free)
                                                <span class="badge bg-label-success">{{ __('lang.free') ?? 'Free' }}</span>
                                            @else
                                                <span class="badge bg-label-warning">{{ __('lang.paid') ?? 'Paid' }}</span>
                                            @endif
                                            @if(!$isEnrolled && !$lesson->is_free)
                                                <i class="bx bx-lock text-muted ms-2"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">{{ __('lang.no_lessons_available') ?? 'No lessons available yet.' }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructor Section -->
        @if($course->instructor)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ __('lang.instructor') ?? 'Instructor' }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                    {{ substr($course->instructor->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $course->instructor->name }}</h5>
                                <p class="text-muted mb-0">{{ $course->instructor->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Related Courses -->
        @if($relatedCourses && $relatedCourses->count() > 0)
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3">{{ __('lang.related_courses') ?? 'Related Courses' }}</h4>
                <div class="row">
                    @foreach($relatedCourses as $relatedCourse)
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100">
                                @if($relatedCourse->image)
                                    <img src="{{ $relatedCourse->getImageUrl() }}" class="card-img-top" alt="{{ $relatedCourse->title }}" style="height: 150px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="bx bx-book display-6 text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">{{ Str::limit($relatedCourse->title, 40) }}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            @if($relatedCourse->hasDiscount())
                                                <span class="text-decoration-line-through">{{ $relatedCourse->price }}</span>
                                                <span class="fw-bold text-danger">{{ $relatedCourse->getFinalPrice() }}</span>
                                            @else
                                                <span class="fw-bold">{{ $relatedCourse->getFinalPrice() == 0 ? __('lang.free') : $relatedCourse->getFinalPrice() }}</span>
                                            @endif
                                        </small>
                                    </p>
                                    <a href="{{ route('front.courses.show', $relatedCourse) }}" class="btn btn-sm btn-outline-primary">
                                        {{ __('lang.view_details') ?? 'View Details' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
    @includeIf("front.courses.pushScripts")
@endpush

