<form action="{{ url('office') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label for="location">{{ __('Location') }} <span class="text-danger">*</span></label>
                <input type="text" name="location" class="form-control" required>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label for="latitude">{{ __('Latitude') }}</label>
                <input type="text" name="latitude" class="form-control" placeholder="e.g. 19.0760">
                <small class="form-text text-muted">{{ __('Decimal format (e.g. 19.0760)') }}</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="longitude">{{ __('Longitude') }}</label>
                <input type="text" name="longitude" class="form-control" placeholder="e.g. 72.8777">
                <small class="form-text text-muted">{{ __('Decimal format (e.g. 72.8777)') }}</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="radius">{{ __('Radius (meters)') }}</label>
                <input type="number" name="radius" class="form-control" placeholder="e.g. 100">
                <small class="form-text text-muted">{{ __('Used for geofencing attendance') }}</small>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label for="address">{{ __('Address') }}</label>
                <textarea name="address" rows="3" class="form-control"></textarea>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="city">{{ __('City') }} <span class="text-danger">*</span></label>
                <input type="text" name="city" class="form-control" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="state">{{ __('State') }}</label>
                <input type="text" name="state" class="form-control">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="country">{{ __('Country') }} <span class="text-danger">*</span></label>
                <input type="text" name="country" class="form-control" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="zip_code">{{ __('Zip Code') }}</label>
                <input type="text" name="zip_code" class="form-control">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="phone">{{ __('Phone') }} <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="email">{{ __('Email') }}</label>
                <input type="email" name="email" class="form-control">
            </div>
        </div>

        <div class="col-md-12">
            <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
            <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
</form>