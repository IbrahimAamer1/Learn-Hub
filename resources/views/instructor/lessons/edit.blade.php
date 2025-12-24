<form action="{{ route('instructor.lessons.update', ['lesson' => $lesson]) }}" method="post" id="edit_form" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div id="edit_form_messages"></div>

    <div class="row">
        @include('components.forms.input', [
            'name' => 'title',
            'label' => __('lang.title') ?? 'Title',
            'value' => old('title', $lesson->title),
            'required' => true,
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.select', [
            'name' => 'course_id',
            'label' => __('lang.course') ?? 'Course',
            'options' => $courses->pluck('title', 'id')->toArray(),
            'value' => old('course_id', $lesson->course_id),
            'required' => true,
            'placeholder' => __('lang.select_course') ?? 'Select Course',
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.textarea', [
            'name' => 'description',
            'label' => __('lang.description') ?? 'Description',
            'value' => old('description', $lesson->description),
            'rows' => 4,
            'class' => 'col-12'
        ])

        @include('components.forms.file-upload', [
            'name' => 'video_file',
            'label' => __('lang.video_file') ?? 'Video File',
            'value' => $lesson->video_file,
            'accept' => 'video/*',
            'preview' => true,
            'help' => 'Max size: 50MB. Leave empty to keep current video.',
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.input', [
            'name' => 'lesson_order',
            'type' => 'number',
            'label' => __('lang.lesson_order') ?? 'Lesson Order',
            'value' => old('lesson_order', $lesson->lesson_order),
            'min' => '0',
            'help' => __('lang.lesson_order_help') ?? 'Order of lesson in the course',
            'class' => 'col-12 col-md-6'
        ])

        <div class="form-group col-12 col-md-6 mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_free" id="is_free_edit" value="1" @checked(old('is_free', $lesson->is_free))>
                <label class="form-check-label" for="is_free_edit">
                    {{ __('lang.is_free') ?? 'Is Free' }}
                </label>
            </div>
        </div>

        <div class="form-group col-12 col-md-6 mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_published" id="is_published_edit" value="1" @checked(old('is_published', $lesson->is_published))>
                <label class="form-check-label" for="is_published_edit">
                    {{ __('lang.is_published') ?? 'Is Published' }}
                </label>
            </div>
        </div>
    </div>

    <hr class="text-muted">

    <div class="form-group float-end">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('lang.close') ?? 'Close' }}</button>
        <button type="button" class="btn btn-primary" id="submit_edit_form">
            {{ __('lang.submit') ?? 'Submit' }}
            @include('partials.shared.modals.spinner')
        </button>
    </div>
</form>

