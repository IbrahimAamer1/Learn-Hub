<div class="row">
    <div class="col-md-4">
        @if($lesson->video_file)
            <video src="{{ $lesson->getVideoUrl() }}" controls class="img-fluid rounded mb-3" style="max-width: 100%;">
                Your browser does not support the video tag.
            </video>
        @else
            <div class="bg-light rounded p-5 text-center mb-3">
                <i class="bx bx-video display-4 text-muted"></i>
                <p class="text-muted mt-2">No Video</p>
            </div>
        @endif
    </div>
    <div class="col-md-8">
        <table class="table table-bordered">
            <tr>
                <th width="30%">{{ __('lang.title') ?? 'Title' }}</th>
                <td>{{ $lesson->title }}</td>
            </tr>
            <tr>
                <th>{{ __('lang.slug') ?? 'Slug' }}</th>
                <td>{{ $lesson->slug }}</td>
            </tr>
            <tr>
                <th>{{ __('lang.course') ?? 'Course' }}</th>
                <td>
                    @if($lesson->course)
                        <span class="badge bg-label-info">{{ $lesson->course->title }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{ __('lang.lesson_order') ?? 'Lesson Order' }}</th>
                <td>{{ $lesson->lesson_order }}</td>
            </tr>
            <tr>
                <th>{{ __('lang.is_free') ?? 'Is Free' }}</th>
                <td>
                    @if($lesson->is_free)
                        <span class="badge bg-label-success">{{ __('lang.free_lesson') ?? 'Free' }}</span>
                    @else
                        <span class="badge bg-label-warning">{{ __('lang.paid_lesson') ?? 'Paid' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{ __('lang.status') ?? 'Status' }}</th>
                <td>
                    @if($lesson->is_published)
                        <span class="badge bg-label-success">{{ __('lang.published') ?? 'Published' }}</span>
                    @else
                        <span class="badge bg-label-warning">{{ __('lang.draft') ?? 'Draft' }}</span>
                    @endif
                </td>
            </tr>
            @if($lesson->description)
            <tr>
                <th>{{ __('lang.description') ?? 'Description' }}</th>
                <td>{{ $lesson->description }}</td>
            </tr>
            @endif
            @if($lesson->video_file)
            <tr>
                <th>{{ __('lang.video_file') ?? 'Video File' }}</th>
                <td>
                    <a href="{{ $lesson->getVideoUrl() }}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="bx bx-download"></i> {{ __('lang.download_video') ?? 'Download Video' }}
                    </a>
                </td>
            </tr>
            @endif
            <tr>
                <th>{{ __('lang.created_at') ?? 'Created At' }}</th>
                <td>{{ $lesson->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <th>{{ __('lang.updated_at') ?? 'Updated At' }}</th>
                <td>{{ $lesson->updated_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        </table>
    </div>
</div>

