<div class="card bg-none card-box">
    <form method="POST" action="{{ route('accountlist.update', $accountlist->id) }}" accept-charset="UTF-8"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="account_name" class="form-control-label">{{ __('Account Name') }}</label>
                    <input type="text" name="account_name" id="account_name" class="form-control"
                        placeholder="{{ __('Enter Account Name') }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="initial_balance" class="form-control-label">{{ __('Initial Balance') }}</label>
                    <input type="number" name="initial_balance" id="initial_balance" class="form-control"
                        placeholder="{{ __('Enter Initial Balance') }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="account_number" class="form-control-label">{{ __('Account Number') }}</label>
                    <input type="text" name="account_number" id="account_number" class="form-control"
                        placeholder="{{ __('Enter Account Number') }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="branch_code" class="form-control-label">{{ __('Branch Code') }}</label>
                    <input type="text" name="branch_code" id="branch_code" class="form-control"
                        placeholder="{{ __('Enter Branch Code') }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="bank_branch" class="form-control-label">{{ __('Bank Branch') }}</label>
                    <input type="text" name="bank_branch" id="bank_branch" class="form-control"
                        placeholder="{{ __('Enter Bank Branch') }}">
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
