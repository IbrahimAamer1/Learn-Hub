@php
    $modelName = App\Models\Lesson::class;
@endphp

<x-filteration :modelName="$modelName">
    <div class="row">
        <div class="col-md-4">
            <label class="label-filter">{{ __('lang.word') ?? 'Search' }}</label>
            <input type="text" name="word" class="form-control" placeholder="{{ __('lang.please_enter') ?? 'Enter' }} {{ __('lang.word') ?? 'keyword' }}" value="{{ request()->input('word') }}">
        </div>

        <div class="col-md-2">
            <label class="label-filter">{{ __('lang.course') ?? 'Course' }}</label>
            <select name="course_id" class="form-control">
                <option value="">{{ __('lang.all') ?? 'All' }}</option>
                @foreach($courses ?? [] as $course)
                    <option value="{{ $course->id }}" @selected(request()->input('course_id') == $course->id)>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="label-filter">{{ __('lang.status') ?? 'Status' }}</label>
            <select name="is_published" class="form-control">
                <option value="">{{ __('lang.all') ?? 'All' }}</option>
                <option value="1" @selected(request()->input('is_published') == '1')>{{ __('lang.published') ?? 'Published' }}</option>
                <option value="0" @selected(request()->input('is_published') == '0')>{{ __('lang.draft') ?? 'Draft' }}</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="label-filter">{{ __('lang.is_free') ?? 'Free' }}</label>
            <select name="is_free" class="form-control">
                <option value="">{{ __('lang.all') ?? 'All' }}</option>
                <option value="1" @selected(request()->input('is_free') == '1')>{{ __('lang.free_lesson') ?? 'Free' }}</option>
                <option value="0" @selected(request()->input('is_free') == '0')>{{ __('lang.paid_lesson') ?? 'Paid' }}</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="label-filter">{{ __('lang.date') ?? 'Date' }}</label>
            <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker6'>
                <input type="date" class="form-control" name="start" placeholder="{{ __('lang.date_from') ?? 'From' }}" value="{{ request()->input('start') }}"/>
                <input type="date" class="form-control" name="end" placeholder="{{ __('lang.date_to') ?? 'To' }}" value="{{ request()->input('end') }}"/>
            </div>
        </div>
    </div>
</x-filteration>

