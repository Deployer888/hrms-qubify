<div class="card bg-none card-box">
    <form action="{{ route('test.send.mail') }}" method="POST">
        @csrf
        <div class="row">
            <div class="form-group col-md-12">
                <label for="email" class="form-control-label">{{ __('Email') }}</label>
                <input type="text" name="email" id="email" class="form-control" required>

                @error('email')
                    <span class="invalid-email" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="modal-footer">
            <input type="submit" value="{{ __('Send') }}" class="btn-create badge-blue">
            <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-bs-dismiss="modal">
        </div>
    </form>
</div>
