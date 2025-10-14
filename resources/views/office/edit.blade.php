<!-- Modal Header -->
<div class="modal-header-custom">
    <div class="modal-header-content">
        <div class="modal-icon">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
            </svg>
        </div>
        <div class="modal-title-section">
            <h3 class="modal-title">{{ __('Edit Office') }}</h3>
            <p class="modal-subtitle">{{ __('Update office location information and settings') }}</p>
        </div>
    </div>
    <button type="button" class="modal-close-btn" data-dismiss="modal">
        <span>{{ __('Close') }}</span>
        <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
        </svg>
    </button>
</div>

<!-- Modal Body -->
<div class="modal-body-custom">
    <form action="{{ route('office.update', $office->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-row">
            <!-- Name Field -->
            <div class="form-group-custom full-width">
                <label class="form-label-custom">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    {{ __('Name') }} <span class="required">*</span>
                </label>
                <input type="text" name="name" class="form-input-custom" placeholder="{{ __('Enter office name') }}" value="{{ old('name', $office->name) }}" required>
            </div>

            <!-- Location Field -->
            <div class="form-group-custom full-width">
                <label class="form-label-custom">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    {{ __('Location') }} <span class="required">*</span>
                </label>
                <input type="text" name="location" class="form-input-custom" placeholder="{{ __('Enter office location') }}" value="{{ old('location', $office->location) }}" required>
            </div>
        </div>

        <div class="form-row three-columns">
            <!-- Latitude Field -->
            <div class="form-group-custom">
                <label class="form-label-custom">{{ __('Latitude') }}</label>
                <input type="text" name="latitude" class="form-input-custom" placeholder="e.g. 19.0760" value="{{ old('latitude', $office->latitude) }}">
                <small class="form-help-text">{{ __('Decimal format (e.g. 19.0760)') }}</small>
            </div>

            <!-- Longitude Field -->
            <div class="form-group-custom">
                <label class="form-label-custom">{{ __('Longitude') }}</label>
                <input type="text" name="longitude" class="form-input-custom" placeholder="e.g. 72.8777" value="{{ old('longitude', $office->longitude) }}">
                <small class="form-help-text">{{ __('Decimal format (e.g. 72.8777)') }}</small>
            </div>

            <!-- Radius Field -->
            <div class="form-group-custom">
                <label class="form-label-custom">{{ __('Radius (meters)') }}</label>
                <input type="number" name="radius" class="form-input-custom" placeholder="e.g. 100" value="{{ old('radius', $office->radius) }}">
                <small class="form-help-text">{{ __('Used for geofencing attendance') }}</small>
            </div>
        </div>

        <!-- Address Field -->
        <div class="form-row">
            <div class="form-group-custom full-width">
                <label class="form-label-custom">{{ __('Address') }}</label>
                <textarea name="address" rows="3" class="form-input-custom" placeholder="{{ __('Enter full address (optional)') }}">{{ old('address', $office->address) }}</textarea>
            </div>
        </div>

        <div class="form-row">
            <!-- City Field -->
            <div class="form-group-custom half-width">
                <label class="form-label-custom">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15 11V5l-3-3-3 3v2H3v14h18V11h-6zm-8 8H5v-2h2v2zm0-4H5v-2h2v2zm0-4H5V9h2v2zm6 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2zm6 12h-2v-2h2v2zm0-4h-2v-2h2v2z"/>
                    </svg>
                    {{ __('City') }} <span class="required">*</span>
                </label>
                <input type="text" name="city" class="form-input-custom" placeholder="{{ __('Enter city name') }}" value="{{ old('city', $office->city) }}" required>
            </div>

            <!-- State Field -->
            <div class="form-group-custom half-width">
                <label class="form-label-custom">{{ __('State') }}</label>
                <input type="text" name="state" class="form-input-custom" placeholder="{{ __('Enter state name') }}" value="{{ old('state', $office->state) }}">
            </div>
        </div>

        <div class="form-row">
            <!-- Country Field -->
            <div class="form-group-custom half-width">
                <label class="form-label-custom">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    {{ __('Country') }} <span class="required">*</span>
                </label>
                <input type="text" name="country" class="form-input-custom" placeholder="{{ __('Enter country name') }}" value="{{ old('country', $office->country) }}" required>
            </div>

            <!-- Zip Code Field -->
            <div class="form-group-custom half-width">
                <label class="form-label-custom">{{ __('Zip Code') }}</label>
                <input type="text" name="zip_code" class="form-input-custom" placeholder="{{ __('Enter zip code') }}" value="{{ old('zip_code', $office->zip_code) }}">
            </div>
        </div>

        <div class="form-row">
            <!-- Phone Field -->
            <div class="form-group-custom half-width">
                <label class="form-label-custom">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                    {{ __('Phone') }} <span class="required">*</span>
                </label>
                <input type="text" name="phone" class="form-input-custom" placeholder="{{ __('Enter phone number') }}" value="{{ old('phone', $office->phone) }}" required>
            </div>

            <!-- Email Field -->
            <div class="form-group-custom half-width">
                <label class="form-label-custom">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                    {{ __('Email') }}
                </label>
                <input type="email" name="email" class="form-input-custom" placeholder="{{ __('Enter email address') }}" value="{{ old('email', $office->email) }}">
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer-custom">
            <button type="button" class="btn-cancel" data-dismiss="modal">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
                {{ __('Cancel') }}
            </button>
            <button type="submit" class="btn-update-office">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                </svg>
                {{ __('Update Office') }}
            </button>
        </div>
    </form>
</div>