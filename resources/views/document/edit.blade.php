<div class="card bg-none card-box">
    <form action="{{ route('document.update', $document->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Name input -->
            <div class="col-12">
                <div class="form-group">
                    <label for="name" class="form-control-label">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control"
                        placeholder="{{ __('Enter Department Name') }}" value="{{ old('name', $document->name) }}">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <!-- Required Field select -->
            <div class="col-12">
                <div class="form-group">
                    <label for="is_required" class="form-control-label">{{ __('Required Field') }}</label>
                    <select name="is_required" id="is_required" class="form-control select2" required>
                        <option value="0" {{ old('is_required', $document->is_required) == 0 ? 'selected' : '' }}>
                            {{ __('Not Required') }}</option>
                        <option value="1" {{ old('is_required', $document->is_required) == 1 ? 'selected' : '' }}>
                            {{ __('Is Required') }}</option>
                    </select>
                </div>
            </div>
            <!-- Submit and cancel buttons -->
            <div class="col-12">
                <button type="submit" class="btn-create badge-blue">{{ __('Update') }}</button>
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
