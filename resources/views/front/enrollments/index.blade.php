@extends('front.master')
@section('title', __('lang.my_enrollments') ?? 'My Enrollments')

@section('content')
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0">{{ __('lang.my_enrollments') ?? 'My Enrollments' }}</h2>
                <p class="text-muted">{{ __('lang.my_enrollments_description') ?? 'View all courses you are enrolled in' }}</p>
            </div>
        </div>

        @if($enrollments->count() > 0)
            <div class="row">
                @foreach($enrollments as $enrollment)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            @if($enrollment->course && $enrollment->course->image)
                                <img src="{{ $enrollment->course->getImageUrl() }}" class="card-img-top" alt="{{ $enrollment->course->title }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bx bx-book display-4 text-muted"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $enrollment->course->title ?? 'N/A' }}</h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        {{ __('lang.enrolled_on') ?? 'Enrolled on' }}: {{ $enrollment->enrolled_at->format('M d, Y') }}
                                    </small>
                                </p>
                                <div class="mb-3">
                                    <label class="small text-muted">{{ __('lang.progress') ?? 'Progress' }}</label>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $enrollment->progress_percentage }}%" aria-valuenow="{{ $enrollment->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $enrollment->progress_percentage }}%
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    @if($enrollment->status == 'enrolled')
                                        <span class="badge bg-primary">{{ __('lang.enrolled') ?? 'Enrolled' }}</span>
                                    @elseif($enrollment->status == 'completed')
                                        <span class="badge bg-success">{{ __('lang.completed') ?? 'Completed' }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('lang.cancelled') ?? 'Cancelled' }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                @php
                                    $course = $enrollment->course;
                                    $firstLesson = $course ? $course->getPublishedLessons()->first() : null;
                                @endphp
                                @if($firstLesson)
                                    <a href="{{ route('front.lessons.show', [$course, $firstLesson]) }}" class="btn btn-primary btn-sm w-100">
                                        <i class="bx bx-play-circle"></i> {{ __('lang.continue_learning') ?? 'Continue Learning' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    {{ $enrollments->links() }}
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bx bx-info-circle display-4"></i>
                        <h4 class="mt-3">{{ __('lang.no_enrollments') ?? 'No Enrollments' }}</h4>
                        <p>{{ __('lang.no_enrollments_message') ?? 'You are not enrolled in any courses yet.' }}</p>
                        <a href="{{ route('front.courses.index') }}" class="btn btn-primary">{{ __('lang.browse_courses') ?? 'Browse Courses' }}</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

