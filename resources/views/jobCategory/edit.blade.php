<div class="card bg-none card-box">
    <form action="{{ route('job-category.update', $jobCategory->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" class="form-control"
                        value="{{ $jobCategory->title }}" placeholder="{{ __('Enter category title') }}">
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
