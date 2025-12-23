@extends('front.master')
@section('title', $lesson->title ?? 'Lesson')

@push('styles')
    @includeIf("front.lessons.pushStyles")
@endpush

@section('content')
    <div class="lesson-viewer">
        <div class="row g-0">
            <!-- Left Sidebar: Lessons List (25%) -->
            <div class="col-lg-3 lesson-sidebar">
                <div class="card h-100 border-0 rounded-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bx bx-book"></i> {{ $course->title }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="lessons-list" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                            @if($lessons && $lessons->count() > 0)
                                @foreach($lessons as $index => $lessonItem)
                                    <a href="{{ route('front.lessons.show', [$course, $lessonItem]) }}" 
                                       class="lesson-item d-flex align-items-center p-3 border-bottom text-decoration-none {{ $lesson->id === $lessonItem->id ? 'active bg-light' : '' }}"
                                       style="color: inherit;">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-label-secondary me-2">{{ $lessonItem->lesson_order }}</span>
                                                <h6 class="mb-0">{{ $lessonItem->title }}</h6>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                @if($lessonItem->is_free)
                                                    <span class="badge bg-label-success small">{{ __('lang.free') ?? 'Free' }}</span>
                                                @else
                                                    <span class="badge bg-label-warning small">{{ __('lang.paid') ?? 'Paid' }}</span>
                                                @endif
                                                @if(auth()->check() && $enrollment && $lessonItem->isWatchedBy(auth()->id()))
                                                    <i class="bx bx-check-circle text-success ms-2"></i>
                                                @endif
                                                @if(!$lessonItem->is_free && (!$enrollment || $enrollment->status !== 'enrolled'))
                                                    <i class="bx bx-lock text-muted ms-2"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="p-3 text-center text-muted">
                                    <p>{{ __('lang.no_lessons_available') ?? 'No lessons available yet.' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($enrollment)
                        <div class="card-footer bg-light">
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $progressPercentage }}%"
                                     aria-valuenow="{{ $progressPercentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $progressPercentage }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ __('lang.course_progress') ?? 'Course Progress' }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main Content: Video Player (75%) -->
            <div class="col-lg-9 lesson-content">
                <div class="card border-0 rounded-0 h-100">
                    <!-- Video Player Section -->
                    <div class="card-body p-0">
                        <div class="video-container bg-dark position-relative" style="padding-top: 56.25%;">
                            @if($lesson->video_file)
                                <video id="lessonVideo" 
                                       class="position-absolute top-0 start-0 w-100 h-100" 
                                       controls 
                                       preload="metadata"
                                       style="object-fit: contain;">
                                    <source src="{{ $lesson->getVideoUrl() }}" type="video/mp4">
                                    {{ __('lang.video_not_supported') ?? 'Your browser does not support the video tag.' }}
                                </video>
                            @else
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center text-white">
                                    <div class="text-center">
                                        <i class="bx bx-video-off display-1"></i>
                                        <p class="mt-3">{{ __('lang.no_video_available') ?? 'No video available for this lesson.' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Lesson Info Section -->
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h2 class="mb-2">{{ $lesson->title }}</h2>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        @if($lesson->is_free)
                                            <span class="badge bg-label-success">{{ __('lang.free') ?? 'Free' }}</span>
                                        @else
                                            <span class="badge bg-label-warning">{{ __('lang.paid') ?? 'Paid' }}</span>
                                        @endif
                                        @if(auth()->check() && $enrollment && $isWatched)
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-check-circle"></i> {{ __('lang.watched') ?? 'Watched' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    @if($previousLesson)
                                        <a href="{{ route('front.lessons.show', [$course, $previousLesson]) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bx bx-chevron-left"></i> {{ __('lang.previous') ?? 'Previous' }}
                                        </a>
                                    @endif
                                    @if($nextLesson)
                                        <a href="{{ route('front.lessons.show', [$course, $nextLesson]) }}" 
                                           class="btn btn-primary btn-sm">
                                            {{ __('lang.next') ?? 'Next' }} <i class="bx bx-chevron-right"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Lesson Description -->
                            @if($lesson->description)
                                <div class="lesson-description">
                                    <h5>{{ __('lang.description') ?? 'Description' }}</h5>
                                    <p class="text-muted">{{ $lesson->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @includeIf("front.lessons.pushScripts")
@endpush

