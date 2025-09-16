<style>
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default .select2-selection--multiple {
        height: auto !important;
        min-height: 40px !important;
    }
</style>
<div class="card bg-none card-box">
    <form method="POST" action="{{ url('zoom-meeting') }}" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label class="form-control-label">{{ __('Title') }}</label>
                    <input type="text" name="title" class="form-control"
                        placeholder="{{ __('Enter Meeting Title') }}" required="required">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group select2_option">
                    <label class="form-control-label">{{ __('User') }}</label>
                    <select name="user_id[]" multiple="multiple" class="form-control select2">
                        @foreach ($employee_option as $optionId => $optionValue)
                            <option value="{{ $optionId }}">{{ $optionValue }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label class="form-control-label">{{ __('Start Date') }}</label>
                    <input type="text" name="start_date" class="form-control datepicker datetime_class_start_date">
                    <input type="hidden" name="start_date" class="start_date" value="">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label class="form-control-label">{{ __('Duration') }}</label>
                    <input type="number" name="duration" class="form-control" required="required" min="0">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label class="form-control-label">{{ __('Password') }}</label>
                    <input type="password" name="password" class="form-control"
                        placeholder="{{ __('Enter Password') }}">
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-bs-dismiss="modal">
            </div>
        </div>
    </form>
</div>
