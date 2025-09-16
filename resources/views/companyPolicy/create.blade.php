<div class="card bg-none card-box">
    <form action="{{ url('company-policy') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Branch select -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="branch" class="form-control-label">{{ __('Branch') }}</label>
                    <select name="branch" id="branch" class="form-control select2" required>
                        <option value="0" selected>All</option>
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Title input -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
            </div>
            <!-- Description input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Description') }}</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
            </div>
            <!-- Attachment input -->
            <div class="col-md-12">
                <label for="attachment" class="form-control-label">{{ __('Attachment') }}</label>
                <div class="choose-file form-group">
                    <label for="attachment" class="form-control-label">
                        <div>{{ __('Choose file here') }}</div>
                        <input type="file" name="attachment" id="attachment" class="form-control"
                            data-filename="attachment_create">
                    </label>
                    <p class="attachment_create"></p>
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
