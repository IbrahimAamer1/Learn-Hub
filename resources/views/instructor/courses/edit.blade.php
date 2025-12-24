<form action="{{ route('instructor.courses.update', ['course' => $course]) }}" method="post" id="edit_form" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div id="edit_form_messages"></div>

    <div class="row">
        @include('components.forms.input', [
            'name' => 'title',
            'label' => __('lang.title') ?? 'Title',
            'value' => old('title', $course->title),
            'required' => true,
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.select', [
            'name' => 'category_id',
            'label' => __('lang.category') ?? 'Category',
            'options' => $categories->pluck('name', 'id')->toArray(),
            'value' => old('category_id', $course->category_id),
            'required' => true,
            'placeholder' => __('lang.select_category') ?? 'Select Category',
            'class' => 'col-12 col-md-6'
        ])

        <!-- Instructor ID cannot be changed -->

        @include('components.forms.select', [
            'name' => 'level',
            'label' => __('lang.level') ?? 'Level',
            'options' => [
                'beginner' => __('lang.beginner') ?? 'Beginner',
                'intermediate' => __('lang.intermediate') ?? 'Intermediate',
                'advanced' => __('lang.advanced') ?? 'Advanced'
            ],
            'value' => old('level', $course->level),
            'required' => true,
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.textarea', [
            'name' => 'description',
            'label' => __('lang.description') ?? 'Description',
            'value' => old('description', $course->description),
            'rows' => 4,
            'class' => 'col-12'
        ])

        @include('components.forms.file-upload', [
            'name' => 'image',
            'label' => __('lang.image') ?? 'Image',
            'value' => $course->image,
            'accept' => 'image/*',
            'preview' => true,
            'help' => 'Max size: 2MB. Leave empty to keep current image.',
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.input', [
            'name' => 'price',
            'type' => 'number',
            'label' => __('lang.price') ?? 'Price',
            'value' => old('price', $course->price),
            'required' => true,
            'step' => '0.01',
            'min' => '0',
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.input', [
            'name' => 'discount_price',
            'type' => 'number',
            'label' => __('lang.discount_price') ?? 'Discount Price',
            'value' => old('discount_price', $course->discount_price),
            'step' => '0.01',
            'min' => '0',
            'help' => __('lang.discount_price_help') ?? 'Leave empty if no discount',
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.input', [
            'name' => 'duration',
            'type' => 'number',
            'label' => __('lang.duration') ?? 'Duration (minutes)',
            'value' => old('duration', $course->duration),
            'min' => '1',
            'help' => __('lang.duration_help') ?? 'Course duration in minutes',
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.input', [
            'name' => 'language',
            'label' => __('lang.language') ?? 'Language',
            'value' => old('language', $course->language),
            'help' => 'Language code (e.g., en, ar)',
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.select', [
            'name' => 'status',
            'label' => __('lang.status') ?? 'Status',
            'options' => [
                'draft' => __('lang.draft') ?? 'Draft',
                'published' => __('lang.published') ?? 'Published'
            ],
            'value' => old('status', $course->status),
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.input', [
            'name' => 'sort_order',
            'type' => 'number',
            'label' => __('lang.sort_order') ?? 'Sort Order',
            'value' => old('sort_order', $course->sort_order),
            'min' => '0',
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.input', [
            'name' => 'meta_title',
            'label' => __('lang.meta_title') ?? 'Meta Title (SEO)',
            'value' => old('meta_title', $course->meta_title),
            'help' => __('lang.meta_title_help') ?? 'SEO title for search engines',
            'class' => 'col-12 col-md-6'
        ])

        @include('components.forms.textarea', [
            'name' => 'meta_description',
            'label' => __('lang.meta_description') ?? 'Meta Description (SEO)',
            'value' => old('meta_description', $course->meta_description),
            'rows' => 2,
            'help' => __('lang.meta_description_help') ?? 'SEO description for search engines',
            'class' => 'col-12 col-md-6'
        ])
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