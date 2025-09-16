<div class="card bg-none card-box">
    <form action="{{ url('job-stage') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Title') }}</label>
                    <input type="text" id="title" name="title" class="form-control"
                        placeholder="{{ __('Enter stage title') }}">
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
