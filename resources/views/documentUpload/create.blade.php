<div class="card bg-none card-box">
    <form action="{{ url('document-upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Name input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="name" class="form-control-label">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control" required
                        value="{{ old('name') }}">
                </div>
            </div>
            <!-- Document file input -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="document" class="form-control-label">{{ __('Document') }}</label>
                    <div class="choose-file form-group">
                        <label for="document" class="form-control-label">
                            <div>{{ __('Choose file here') }}</div>
                            <input type="file" name="document" id="document" class="form-control" required
                                data-filename="document_create">
                        </label>
                        <p class="document_create"></p>
                    </div>
                </div>
            </div>
            <!-- Role select -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="role" class="form-control-label">{{ __('Role') }}</label>
                    <select name="role" id="role" class="form-control select2">
                        @foreach ($roles as $key => $role)
                            <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
                                {{ $role }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Description input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Description') }}</label>
                    <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
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
