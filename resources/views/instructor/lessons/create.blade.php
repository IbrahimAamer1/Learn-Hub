<form action="{{ route('instructor.lessons.store') }}" method="post" id="add_form" enctype="multipart/form-data">
    @csrf

    <div id="add_form_messages"></div>

    <div class="row">
        @include('components.forms.input', [
            'name' => 'title',
            'label' => __('lang.title') ?? 'Title',
            'value' => old('title'),
            'required' => true,
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.select', [
            'name' => 'course_id',
            'label' => __('lang.course') ?? 'Course',
            'options' => $courses->pluck('title', 'id')->toArray(),
            'value' => old('course_id'),
            'required' => true,
            'placeholder' => __('lang.select_course') ?? 'Select Course',
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.textarea', [
            'name' => 'description',
            'label' => __('lang.description') ?? 'Description',
            'value' => old('description'),
            'rows' => 4,
            'class' => 'col-12'
        ])

        @include('components.forms.file-upload', [
            'name' => 'video_file',
            'label' => __('lang.video_file') ?? 'Video File',
            'accept' => 'video/*',
            'help' => 'Max size: 50MB. Supported formats: mp4, webm, ogg, mov, avi',
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.input', [
            'name' => 'lesson_order',
            'type' => 'number',
            'label' => __('lang.lesson_order') ?? 'Lesson Order',
            'value' => old('lesson_order', 0),
            'min' => '0',
            'help' => __('lang.lesson_order_help') ?? 'Order of lesson in the course',
            'class' => 'col-12 col-md-6'
        ])

        <div class="form-group col-12 col-md-6 mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_free" id="is_free" value="1" @checked(old('is_free', false))>
                <label class="form-check-label" for="is_free">
                    {{ __('lang.is_free') ?? 'Is Free' }}
                </label>
            </div>
        </div>

        <div class="form-group col-12 col-md-6 mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" @checked(old('is_published', false))>
                <label class="form-check-label" for="is_published">
                    {{ __('lang.is_published') ?? 'Is Published' }}
                </label>
            </div>
        </div>
    </div>

    <hr class="text-muted">

    <div class="form-group float-end">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('lang.close') ?? 'Close' }}</button>
        <button type="button" class="btn btn-primary" id="submit_add_form">
            {{ __('lang.submit') ?? 'Submit' }}
            @include('partials.shared.modals.spinner')
        </button>
    </div>
</form>

