<form method="POST" action="{{ route('permissions.update', $permission->id) }}">
    @method('PUT')
    @csrf
    <div class="card-body p-0">
        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" type="text" class="form-control" name="name" value="{{ $permission->name }}"
                placeholder="{{ __('Enter Permission Name') }}">
            @error('name')
                <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="modal-footer pr-0">
        <button type="button" class="btn dark btn-outline" data-dismiss="modal">{{ __('Cancel') }}</button>
        <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
    </div>
</form>
