<div class="card bg-none card-box">
    <form method="POST" action="{{ route('job-stage.update', $jobStage->id) }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Title') }}</label>
                    <input type="text" id="title" name="title" class="form-control"
                        placeholder="{{ __('Enter stage title') }}" value="{{ $jobStage->title }}">
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
