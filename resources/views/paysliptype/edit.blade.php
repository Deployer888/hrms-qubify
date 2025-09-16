<div class="card bg-none card-box">
    <form method="POST" action="{{ route('paysliptype.update', $paysliptype->id) }}">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="name" class="form-control-label">{{ __('Name') }}</label>
                    <input id="name" type="text" class="form-control" name="name"
                        placeholder="{{ __('Enter Department Name') }}" value="{{ old('name', $paysliptype->name) }}"
                        required>
                    @error('name')
                        <span class="invalid-name" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
