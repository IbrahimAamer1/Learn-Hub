@extends('back.master')
@section('title', __('lang.show_role'))
@section('roles_active', 'active bg-light')
@includeIf("$directory.pushStyles")

@section('content')
    <!-- page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h2 class="h5 page-title">{{ __('lang.show_role') }}</h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

        {{-- MODIFICATIONS FROM HERE --}}
        <div class="row">
            
            <div class="form-group col-12">
                <label class="form-label">{{ __('lang.name') }}</label>
                <p class="border form-control">{{$role->name ?? '--'}}</p>
            </div>

            <div class="form-group col-12">
                <div class="row">
                    @if (count($groups) > 0)
                        @foreach ($groups as $permission)
                            <div class="col-md-6">
                                <div class="form-check form-check-primary mt-1">
                                    <input class="form-check-input" type="checkbox" 
                                    disabled @checked($role->hasPermissionTo($permission->name))>
                                    <div class="d-inline-block">
                                        <label class="form-check-label">
                                            @php
                                                $transKey = "lang.$permission->name";
                                                $translated = __($transKey);
                                            @endphp
                                            @if ($translated !== $transKey)
                                                {{ $translated }}
                                            @else
                                                {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- CASE PERMISSIONS TABLE --}}
            @if (isset($caseCategories))
                <div class="form-group col-12 mt-4">
                    <label class="form-label">{{ __('lang.case_permissions') }}</label>
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered border-primary mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="20%">{{ __('lang.case') }}</th>
                                    <th class="text-center">{{ __('lang.list_case') }}</th>
                                    <th class="text-center">{{ __('lang.archive_case') }}</th>
                                    <th class="text-center">{{ __('lang.new_case') }}</th>
                                    <th class="text-center">{{ __('lang.edit_case') }}</th>
                                    <th class="text-center">{{ __('lang.delete_case') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($caseCategories as $category)
                                    <tr>
                                        <th class="bg-light">{{ $category->name }}</th>

                                        @if (count($category->permissions) > 0)
                                            @foreach ($category->permissions as $permission)
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox" disabled @checked($role->hasPermissionTo($permission->name))>
                                                </td>
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
    
        </div>
        {{-- MODIFICATIONS TO HERE --}}

        </div>
    </div>

@endsection

@includeIf("$directory.pushScripts")