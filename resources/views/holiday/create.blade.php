<div class="card bg-none card-box">
    <form action="{{ url('holiday') }}" method="post">
        @csrf
        <div class="row">
            <div class="form-group col-md-12">
                <label for="date" class="form-control-label">{{ __('Date') }}</label>
                <input type="text" name="date" id="date" class="form-control datepicker"
                    value="{{ old('date') }}">
            </div>
            <div class="form-group col-md-12">
                <label for="occasion" class="form-control-label">{{ __('Occasion') }}</label>
                <input type="text" name="occasion" id="occasion" class="form-control" value="{{ old('occasion') }}">
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
