@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Company Policy') }}
@endsection

@section('content')
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="header-text">
                    <h1>{{__('Company Policies')}}</h1>
                </div>
            </div>
            <div class="header-stats">
                @can('Create Company Policy')
                    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                        <a href="#" data-url="{{ route('company-policy.create') }}"
                            class="btn btn-xs btn-white btn-icon-only width-auto" data-ajax-popup="true"
                            data-title="{{ __('Create New Company Policy') }}">
                            <i class="fa fa-plus"></i> {{ __('Create') }}
                        </a>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body py-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 dataTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    @if (Gate::check('Edit Company Policy') || Gate::check('Delete Company Policy'))
                                        <th>{{ __('Attachment') }}</th>
                                        <th width="3%">{{ __('Action') }}</th>
                                    @elseif(\Auth::user()->type == 'employee')
                                    <th class="text-center">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="font-style">
                                @foreach ($companyPolicy as $policy)
                                    @php
                                   
                                         $policyPath = asset('companyPolicy');
                                    @endphp
                                    <tr>
                                        @if(!empty($policy->branches) && $policy->branch != 0)
                                        <td>{{ !empty($policy->branches) ? $policy->branches->name : '' }}</td>
                                        @else
                                        <td>All</td>
                                        @endif
                                        <td>{{ $policy->title }}</td>
                                        <td>{{ $policy->description }}</td>
                                        @if (Gate::check('Edit Company Policy') || Gate::check('Delete Company Policy'))
                                        <td>
                                            @if (!empty($policy->attachment))
                                                <a href="{{ $policyPath . '/' . $policy->attachment }}" target="_blank">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @else
                                                <p>-</p>
                                            @endif
                                        </td>
                                            <td class="text-right action-btns">
                                                @can('Edit Company Policy')
                                                    <a href="#"
                                                        data-url="{{ route('company-policy.acknowledge', $policy->id) }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Acknowledged employees') }}" class="edit-icon"
                                                        data-toggle="tooltip" data-original-title="{{ __('Acknowledged employees') }}"><i
                                                            class="fas fa-pray"></i></a>
                                                    <a href="#"
                                                        data-url="{{ route('company-policy.edit', $policy->id) }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Company Policy') }}" class="edit-icon"
                                                        data-toggle="tooltip" data-original-title="{{ __('Edit') }}"><i
                                                            class="fas fa-pencil-alt"></i></a>
                                                @endcan
                                   

                                    @can('Delete Company Policy')
    <a href="javascript:void(0);" 
       onclick="return confirmDelete({{ $policy->id }});">
        <i class="fas fa-trash"></i>
    </a>

    <form id="delete-form-{{ $policy->id }}" 
          action="{{ route('company-policy.destroy', $policy->id) }}" 
          method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endcan  

                                          
                                            </td>
                                    @elseif(\Auth::user()->type == 'employee')
                                        <td class="text-center action-btns" style="width:100%!important;">
                                            <a href="#"
                                                data-url="{{ route('company-policy.show', $policy->id) }}"
                                                data-size="lg" data-ajax-popup="true"
                                                data-title="{{ __('Show Company Policy') }}" class="screen-icon"
                                                data-toggle="tooltip" data-original-title="{{ __('Show') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
       Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone. Do you want to continue?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@push('script-page')


@endpush
