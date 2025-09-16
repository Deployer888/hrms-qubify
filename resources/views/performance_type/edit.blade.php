<div class="card bg-none card-box">
    <form method="POST" action="{{ route('performanceType.update', $performance_type->id) }}">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="name" class="form-control-label">{{ __('Name') }}</label>
                    <input id="name" type="text" class="form-control" name="name"
                        value="{{ $performance_type->name }}">
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Updated') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
