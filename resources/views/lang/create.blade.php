<div class="card bg-none card-box">
    <form action="{{ route('store.language') }}" method="POST">
        @csrf
        <div class="row">
            <div class="form-group col-md-12">
                <label for="code">{{ __('Language Code') }}</label>
                <input type="text" id="code" name="code" class="form-control" required>
                @error('code')
                    <span class="invalid-code" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
            <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </form>
</div>
