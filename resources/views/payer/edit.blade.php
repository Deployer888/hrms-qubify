<div class="card bg-none card-box">
    <form action="{{ route('payer.update', $payer->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="payer_name" class="form-control-label">{{ __('Payer Name') }}</label>
                    <input type="text" name="payer_name" id="payer_name" class="form-control"
                        placeholder="{{ __('Enter Payer Name') }}" value="{{ $payer->payer_name }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="contact_number" class="form-control-label">{{ __('Contact Number') }}</label>
                    <input type="number" name="contact_number" id="contact_number" class="form-control"
                        placeholder="{{ __('Enter Contact Number') }}" value="{{ $payer->contact_number }}">
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
