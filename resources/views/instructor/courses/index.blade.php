@extends('layouts.instructor.master')
@section('title', __('lang.courses') ?? 'Courses')
@section('courses_active', 'active bg-light')
@push('styles')
    @includeIf("$directory.pushStyles")
@endpush

@section('content')
    @include('components.tables.table-header', [
        'title' => __('lang.courses') ?? 'Courses',
        'createRoute' => route('instructor.courses.create'),
        'createTitle' => __('lang.add_new_course') ?? 'Add New Course',
        'showCreate' => true,
         //'permission' => 'create_course'
    ])

    @includeIf("$directory.filter")

    <div class="card" id="mainCont">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-nowrap font-size-14">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-primary" width="5%">#</th>
                            <th class="text-primary">{{ __('lang.image') ?? 'Image' }}</th>
                            <th class="text-primary">{{ __('lang.title') ?? 'Title' }}</th>
                            <th class="text-primary">{{ __('lang.category') ?? 'Category' }}</th>
                            <th class="text-primary">{{ __('lang.instructor') ?? 'Instructor' }}</th>
                            <th class="text-primary">{{ __('lang.level') ?? 'Level' }}</th>
                            <th class="text-primary">{{ __('lang.price') ?? 'Price' }}</th>
                            <th class="text-primary">{{ __('lang.status') ?? 'Status' }}</th>
                            <th class="text-primary" width="11%">{{ __('lang.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($data['data']) > 0)
                            @foreach ($data['data'] as $key => $course)
                                <tr>
                                    <td>{{ $data['data']->firstItem()+$loop->index }}</td>
                                    <td>
                                        @if($course->image)
                                            <img src="{{ $course->getImageUrl() }}" alt="{{ $course->title }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <span class="badge bg-label-secondary">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $course->title }}</td>
                                    <td>
                                        @if($course->category)
                                            <span class="badge bg-label-info">{{ $course->category->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($course->instructor)
                                            {{ $course->instructor->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($course->level)
                                            <span class="badge bg-label-{{ $course->level == 'beginner' ? 'success' : ($course->level == 'intermediate' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($course->level) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                   
                                    <td>
                                        @if($course->hasDiscount())
                                            <span class="text-decoration-line-through text-muted">{{ $course->price }}</span>
                                            <span class="text-danger fw-bold">{{ $course->getFinalPrice() }}</span>
                                            <span class="badge bg-label-danger">{{ $course->getDiscountPercentage() }}%</span>
                                        @else
                                            <span class="fw-bold">{{ $course->getFinalPrice() }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($course->status == 'published')
                                            <span class="badge bg-label-success">{{ __('lang.published') ?? 'Published' }}</span>
                                        @else
                                            <span class="badge bg-label-warning">{{ __('lang.draft') ?? 'Draft' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @include('components.tables.table-actions', [
                                            'item' => $course,
                                            'showRoute' => route('instructor.courses.show', ['course' => $course]),
                                            'editRoute' => route('instructor.courses.edit', ['course' => $course]),
                                            'deleteRoute' => route('instructor.courses.destroy', ['course' => $course]),
                                            'actions' => ['show', 'edit', 'delete'],
                                            //'showPermission' => 'show_course',
                                            //'editPermission' => 'edit_course',
                                            //'deletePermission' => 'delete_course'
                                        ])
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center">
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