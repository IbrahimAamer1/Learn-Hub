@php
    $modelName = App\Models\Course::class;
@endphp

<x-filteration :modelName="$modelName">
    <div class="row">
        <div class="col-md-4">
            <label class="label-filter">{{ __('lang.word') ?? 'Search' }}</label>
            <input type="text" name="word" class="form-control" placeholder="{{ __('lang.please_enter') ?? 'Enter' }} {{ __('lang.word') ?? 'keyword' }}" value="{{ request()->input('word') }}">
        </div>

        <div class="col-md-2">
            <label class="label-filter">{{ __('lang.category') ?? 'Category' }}</label>
            <select name="category_id" class="form-control">
                <option value="">{{ __('lang.all') ?? 'All' }}</option>
                @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id }}" @selected(request()->input('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="label-filter">{{ __('lang.level') ?? 'Level' }}</label>
            <select name="level" class="form-control">
                <option value="">{{ __('lang.all') ?? 'All' }}</option>
                <option value="beginner" @selected(request()->input('level') == 'beginner')>{{ __('lang.beginner') ?? 'Beginner' }}</option>
                <option value="intermediate" @selected(request()->input('level') == 'intermediate')>{{ __('lang.intermediate') ?? 'Intermediate' }}</option>
                <option value="advanced" @selected(request()->input('level') == 'advanced')>{{ __('lang.advanced') ?? 'Advanced' }}</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="label-filter">{{ __('lang.status') ?? 'Status' }}</label>
            <select name="status" class="form-control">
                <option value="">{{ __('lang.all') ?? 'All' }}</option>
                <option value="published" @selected(request()->input('status') == 'published')>{{ __('lang.published') ?? 'Published' }}</option>
                <option value="draft" @selected(request()->input('status') == 'draft')>{{ __('lang.draft') ?? 'Draft' }}</option>
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