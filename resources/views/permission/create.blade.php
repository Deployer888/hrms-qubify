<form method="POST" action="{{ url('permissions') }}">
    @csrf
    <div class="card-body p-0">
        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" type="text" class="form-control" name="name"
                placeholder="{{ __('Enter Permission Name') }}">
            @error('name')
                <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- New: Module Field -->
        <div class="form-group">
            <label for="module">{{ __('Module') }}</label>
            <input id="module" type="text" class="form-control" name="module"
                placeholder="{{ __('Enter Module Name (e.g., User, Role, Award)') }}">
            @error('module')
                <span class="invalid-module" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- New: Description Field -->
        <div class="form-group">
            <label for="description">{{ __('Description') }}</label>
            <textarea id="description" class="form-control" name="description" rows="3"
                placeholder="{{ __('Enter Description') }}"></textarea>
            @error('description')
                <span class="invalid-description" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            @if (!$roles->isEmpty())
                <h6>{{ __('Assign Permission to Roles') }}</h6>
                @foreach ($roles as $role)
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="role{{ $role->id }}" name="roles[]"
                            value="{{ $role->id }}">
                        <label class="custom-control-label"
                            for="role{{ $role->id }}">{{ ucfirst($role->name) }}</label>
                    </div>
                @endforeach
            @endif
            @error('roles')
                <span class="invalid-roles" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="modal-footer pr-0">
        <button type="button" class="btn dark btn-outline" data-dismiss="modal">{{ __('Cancel') }}</button>
        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
    </div>
</form>
