@extends('layouts.instructor.master')
@section('title', __('lang.my_students') ?? 'My Students')
@section('students_active', 'active bg-light')
@push('styles')
    @includeIf("$directory.pushStyles")
@endpush

@section('content')
    @include('components.tables.table-header', [
        'title' => __('lang.my_students') ?? 'My Students',
        'showCreate' => false,
    ])

    @includeIf("$directory.filter")

    <div class="card" id="mainCont">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-nowrap font-size-14">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-primary" width="5%">#</th>
                            <th class="text-primary">{{ __('lang.student') ?? 'Student' }}</th>
                            <th class="text-primary">{{ __('lang.course') ?? 'Course' }}</th>
                            <th class="text-primary">{{ __('lang.enrolled_at') ?? 'Enrolled At' }}</th>
                            <th class="text-primary">{{ __('lang.progress') ?? 'Progress' }}</th>
                            <th class="text-primary">{{ __('lang.status') ?? 'Status' }}</th>
                            <th class="text-primary" width="11%">{{ __('lang.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($data['data']) > 0)
                            @foreach ($data['data'] as $key => $enrollment)
                                <tr>
                                    <td>{{ $data['data']->firstItem()+$loop->index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($enrollment->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $enrollment->user->name }}</div>
                                                <small class="text-muted">{{ $enrollment->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $enrollment->course->title }}</td>
                                    <td>{{ $enrollment->enrolled_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $enrollment->progress_percentage }}%"
                                                 aria-valuenow="{{ $enrollment->progress_percentage }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $enrollment->progress_percentage }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($enrollment->status == 'enrolled')
                                            <span class="badge bg-label-primary">{{ __('lang.enrolled') ?? 'Enrolled' }}</span>
                                        @elseif($enrollment->status == 'completed')
                                            <span class="badge bg-label-success">{{ __('lang.completed') ?? 'Completed' }}</span>
                                        @else
                                            <span class="badge bg-label-danger">{{ __('lang.cancelled') ?? 'Cancelled' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('instructor.students.show', ['user' => $enrollment->user, 'course' => $enrollment->course]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-show"></i> {{ __('lang.view') ?? 'View' }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">
                                    <x-empty-state message="{{ __('lang.no_data_available') ?? 'No data available' }}" />
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        
            @include('components.common.pagination', ['paginator' => $data['data']])
        </div>
    </div>
@endsection

@push('scripts')
    @includeIf("$directory.pushScripts")
@endpush

