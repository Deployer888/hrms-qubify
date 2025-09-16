<div class="card bg-none card-box">
    <form action="{{ url('document') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Name input -->
            <div class="form-group col-12">
                <label for="name" class="form-control-label">{{ __('Name') }}</label>
                <input type="text" name="name" id="name" class="form-control"
                    placeholder="{{ __('Enter Document Name') }}" value="{{ old('name') }}">
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <!-- Required Field select -->
            <div class="form-group col-12">
                <label for="is_required" class="form-control-label">{{ __('Required Field') }}</label>
                <select class="form-control select2" name="is_required" id="is_required" required>
                    <option value="0" {{ old('is_required') == '0' ? 'selected' : '' }}>{{ __('Not Required') }}
                    </option>
                    <option value="1" {{ old('is_required') == '1' ? 'selected' : '' }}>{{ __('Is Required') }}
                    </option>
                </select>
            </div>
            <!-- Submit and cancel buttons -->
            <div class="col-12">
                <button type="submit" class="btn-create badge-blue">{{ __('Create') }}</button>
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
