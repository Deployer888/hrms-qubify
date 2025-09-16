<div class="card bg-none card-box">
    <form action="{{ url('custom-question') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Question input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="question" class="form-control-label">{{ __('Question') }}</label>
                    <input type="text" name="question" id="question" class="form-control" placeholder="{{ __('Enter question') }}" value="{{ old('question') }}">
                </div>
            </div>
            <!-- Is Required select -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="is_required" class="form-control-label">{{ __('Is Required') }}</label>
                    <select name="is_required" id="is_required" class="form-control select2" required>
                        @foreach ($is_required as $value => $label)
                            <option value="{{ $value }}" {{ old('is_required') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Submit and cancel buttons -->
            <div class="col-12">
                <button type="submit" class="btn-create badge-blue">{{ __('Create') }}</button>
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
