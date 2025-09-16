@extends('layouts.admin')
@section('page-title')
    {{ __('Training Details') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mt-3">
                            <tbody>
                                <tr>
                                    <td>{{ __('Training Type') }}</td>
                                    <td class="text-right">{{ !empty($training->types) ? $training->types->name : '' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Trainer') }}</td>
                                    <td class="text-right">
                                        {{ !empty($training->trainers) ? $training->trainers->firstname : '--' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Training Cost') }}</td>
                                    <td class="text-right">{{ \Auth::user()->priceFormat($training->training_cost) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Start Date') }}</td>
                                    <td class="text-right">{{ \Auth::user()->dateFormat($training->start_date) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('End Date') }}</td>
                                    <td class="text-right">{{ \Auth::user()->dateFormat($training->end_date) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Date') }}</td>
                                    <td class="text-right">{{ \Auth::user()->dateFormat($training->created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="text-sm mt-4 p-2"> {{ $training->description }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('Training Employee') }}</h6>
                            <div class="media-list" id="all_employees_list">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item" style="border:0px;">
                                        <div class="media align-items-center">
                                            <img src="{{ !empty($training->employees) ? (!empty($training->employees->user->avatar) ? asset(Storage::url('uploads/avatar')) . '/' . $training->employees->user->avatar : asset(Storage::url('uploads/avatar')) . '/avatar.png') : asset(Storage::url('uploads/avatar')) . '/avatar.png' }}"
                                                class="user-image-hr-prj ui-w-30 rounded-circle" width="50px"
                                                height="50px">
                                            <div class="media-body px-2 text-sm">
                                                <a href="{{ route('employee.show', !empty($training->employees) ? \Illuminate\Support\Facades\Crypt::encrypt($training->employees->id) : 0) }}"
                                                    class="text-dark">
                                                    {{ !empty($training->employees) ? $training->employees->name : '' }}
                                                </a>
                                                <br>
                                                {{ !empty($training->employees) ? (!empty($training->employees->designation) ? $training->employees->designation->name : '') : '' }}
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('training.status', $training->id) }}" method="post">
                                @csrf
                                <h6>{{ __('Update Status') }}</h6>
                                <div class="col-md-12 mt-4">
                                    <input type="hidden" value="{{ $training->id }}" name="id">
                                    <div class="form-group">
                                        <label for="performance"
                                            class="form-control-label text-dark">{{ __('Performance') }}</label>
                                        <select name="performance" id="performance" class="form-control select2">
                                            @foreach ($performance as $key => $value)
                                                <option value="{{ $key }}"
                                                    @if ($training->performance == $key) selected @endif>{{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="status"
                                            class="form-control-label text-dark">{{ __('Status') }}</label>
                                        <select name="status" id="status" class="form-control select2">
                                            @foreach ($status as $key => $value)
                                                <option value="{{ $key }}"
                                                    @if ($training->status == $key) selected @endif>{{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="remarks"
                                            class="form-control-label text-dark">{{ __('Remarks') }}</label>
                                        <textarea name="remarks" id="remarks" class="form-control" placeholder="{{ __('Remarks') }}">{{ $training->remarks }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 text-right">
                                    <input type="submit" value="{{ __('Save') }}" class="btn-create badge-blue">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
