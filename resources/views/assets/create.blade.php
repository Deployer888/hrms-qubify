<div class="card bg-none card-box">
    <form action="{{ url('account-assets') }}" method="POST">
        @csrf
        <div class="row">
            <div class="form-group col-md-6">
                <label for="name" class="form-control-label">{{ __('Name') }}</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                <input type="number" name="amount" id="amount" class="form-control" required step="0.01">
            </div>
            <div class="form-group col-md-6">
                <label for="purchase_date" class="form-control-label">{{ __('Purchase Date') }}</label>
                <input type="text" name="purchase_date" id="purchase_date" class="form-control datepicker">
            </div>
            <div class="form-group col-md-6">
                <label for="supported_date" class="form-control-label">{{ __('Support Until') }}</label>
                <input type="text" name="supported_date" id="supported_date" class="form-control datepicker">
            </div>
            <div class="form-group col-md-12">
                <label for="description" class="form-control-label">{{ __('Description') }}</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
