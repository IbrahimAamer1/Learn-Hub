@push('scripts')
<script>
    $(document).ready(function() {
        const video = document.getElementById('lessonVideo');
        const lessonId = {{ $lesson->id }};
        let hasMarkedAsWatched = false;

        // Mark lesson as watched when page loads (if authenticated and enrolled)
        @if(auth()->check() && $enrollment && !$isWatched)
            markLessonAsWatched();
        @endif

        // Mark lesson as watched when video starts playing
        if (video) {
            video.addEventListener('play', function() {
                if (!hasMarkedAsWatched && {{ auth()->check() ? 'true' : 'false' }}) {
                    markLessonAsWatched();
                }
            });
        }

        /**
         * Mark lesson as watched via AJAX
         * This function sends a POST request to mark the lesson as watched
         * and updates the progress bar if the request is successful.
         */
        function markLessonAsWatched() {
            if (hasMarkedAsWatched) {
                return;
            }

            @if(!auth()->check() || !$enrollment)
                return;
            @endif

            $.ajax({
                url: '{{ route("front.lessons.mark-watched", $lesson) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        hasMarkedAsWatched = true;
                        
                        // Update progress bar
                        if (response.progress_percentage !== undefined) {
                            const progressBar = $('.progress-bar');
                            progressBar.css('width', response.progress_percentage + '%');
                            progressBar.attr('aria-valuenow', response.progress_percentage);
                            progressBar.text(response.progress_percentage + '%');
                        }

                        // Update watched badge
                        const watchedBadge = $('.lesson-content .badge.bg-label-success');
                        if (watchedBadge.length === 0) {
                            $('.lesson-content .d-flex.align-items-center.gap-2').append(
                                '<span class="badge bg-label-success">' +
                                '<i class="bx bx-check-circle"></i> {{ __("lang.watched") ?? "Watched" }}' +
                                '</span>'
                            );
                        }

                        // Update lesson item in sidebar
                        const currentLessonItem = $('.lesson-item.active');
                        if (currentLessonItem.find('.bx-check-circle').length === 0) {
                            currentLessonItem.find('.d-flex.align-items-center').append(
                                '<i class="bx bx-check-circle text-success ms-2"></i>'
                            );
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error marking lesson as watched:', xhr);
                    // Don't show error to user, just log it
                }
            });
        }
    });
</script>
@endpush

