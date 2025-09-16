<div class="card bg-none card-box">
    <form action="{{ route('payees.update', $payee->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="payee_name">{{ __('Payee Name') }}</label>
                    <input type="text" name="payee_name" id="payee_name" class="form-control"
                        placeholder="{{ __('Enter Payee Name') }}" value="{{ $payee->payee_name }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="contact_number">{{ __('Contact Number') }}</label>
                    <input type="number" name="contact_number" id="contact_number" class="form-control"
                        placeholder="{{ __('Enter Contact Number') }}" value="{{ $payee->contact_number }}">
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
