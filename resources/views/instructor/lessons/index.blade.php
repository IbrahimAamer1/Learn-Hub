@extends('layouts.instructor.master')
@section('title', __('lang.lessons') ?? 'Lessons')
@section('lessons_active', 'active bg-light')
@push('styles')
    @includeIf("$directory.pushStyles")
@endpush

@section('content')
    @include('components.tables.table-header', [
        'title' => __('lang.lessons') ?? 'Lessons',
        'createRoute' => route('instructor.lessons.create'),
        'createTitle' => __('lang.add_new_lesson') ?? 'Add New Lesson',
        'showCreate' => true,
        //'permission' => 'create_lesson'
    ])

    @includeIf("$directory.filter")

    <div class="card" id="mainCont">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-nowrap font-size-14">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-primary" width="5%">#</th>
                            <th class="text-primary">{{ __('lang.course') ?? 'Course' }}</th>
                            <th class="text-primary">{{ __('lang.title') ?? 'Title' }}</th>
                            <th class="text-primary">{{ __('lang.video') ?? 'Video' }}</th>
                            <th class="text-primary">{{ __('lang.lesson_order') ?? 'Order' }}</th>
                            <th class="text-primary">{{ __('lang.is_free') ?? 'Free' }}</th>
                            <th class="text-primary">{{ __('lang.status') ?? 'Status' }}</th>
                            <th class="text-primary" width="11%">{{ __('lang.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($data['data']) > 0)
                            @foreach ($data['data'] as $key => $lesson)
                                <tr>
                                    <td>{{ $data['data']->firstItem()+$loop->index }}</td>
                                    <td>
                                        @if($lesson->course)
                                            <span class="badge bg-label-info">{{ $lesson->course->title }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $lesson->title }}</td>
                                    <td>
                                        @if($lesson->video_file)
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-video"></i> {{ __('lang.has_video') ?? 'Has Video' }}
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ __('lang.no_video') ?? 'No Video' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $lesson->lesson_order }}</td>
                                    <td>
                                        @if($lesson->is_free)
                                            <span class="badge bg-label-success">{{ __('lang.free_lesson') ?? 'Free' }}</span>
                                        @else
                                            <span class="badge bg-label-warning">{{ __('lang.paid_lesson') ?? 'Paid' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lesson->is_published)
                                            <span class="badge bg-label-success">{{ __('lang.published') ?? 'Published' }}</span>
                                        @else
                                            <span class="badge bg-label-warning">{{ __('lang.draft') ?? 'Draft' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @include('components.tables.table-actions', [
                                            'item' => $lesson,
                                            'showRoute' => route('instructor.lessons.show', ['lesson' => $lesson]),
                                            'editRoute' => route('instructor.lessons.edit', ['lesson' => $lesson]),
                                            'deleteRoute' => route('instructor.lessons.destroy', ['lesson' => $lesson]),
                                            'actions' => ['show', 'edit', 'delete'],
                                            //'showPermission' => 'show_lesson',
                                            //'editPermission' => 'edit_lesson',
                                            //'deletePermission' => 'delete_lesson'
                                        ])
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center">
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

