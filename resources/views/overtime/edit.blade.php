<div class="card bg-none card-box">
    <form action="{{ route('overtime.update', $overtime->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body p-0">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title" class="form-control-label">{{ __('Title') }}</label>
                        <input type="text" name="title" id="title" class="form-control" required
                            value="{{ old('title', $overtime->title) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="number_of_days" class="form-control-label">{{ __('Number Of Days') }}</label>
                        <input type="text" name="number_of_days" id="number_of_days" class="form-control" required
                            value="{{ old('number_of_days', $overtime->number_of_days) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="hours" class="form-control-label">{{ __('Hours') }}</label>
                        <input type="text" name="hours" id="hours" class="form-control" required
                            value="{{ old('hours', $overtime->hours) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="rate" class="form-control-label">{{ __('Rate') }}</label>
                        <input type="number" name="rate" id="rate" class="form-control" required
                            value="{{ old('rate', $overtime->rate) }}">
                    </div>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
