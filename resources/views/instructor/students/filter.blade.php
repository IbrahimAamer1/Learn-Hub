<div class="card mb-4">
    <div class="card-body">
        <form id="filterForm" method="GET" action="{{ route('instructor.students.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('lang.course') ?? 'Course' }}</label>
                    <select name="course_id" class="form-select">
                        <option value="">{{ __('lang.all_courses') ?? 'All Courses' }}</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('lang.status') ?? 'Status' }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('lang.all_statuses') ?? 'All Statuses' }}</option>
                        <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>{{ __('lang.enrolled') ?? 'Enrolled' }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('lang.completed') ?? 'Completed' }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('lang.cancelled') ?? 'Cancelled' }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('lang.search') ?? 'Search' }}</label>
                    <input type="text" name="word" class="form-control" placeholder="{{ __('lang.search_by_name_or_email') ?? 'Search by name or email' }}" value="{{ request('word') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> {{ __('lang.filter') ?? 'Filter' }}
                        </button>
                        <a href="{{ route('instructor.students.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-refresh"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

