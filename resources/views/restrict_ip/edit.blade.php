<div class="card bg-none card-box">
    <form action="{{ route('edit.ip', $ip->id) }}" method="POST">
        @csrf
        <div class="row">
            <div class="form-group col-md-12">
                <label for="ip" class="form-control-label">{{ __('IP') }}</label>
                <input type="text" name="ip" id="ip" class="form-control" value="{{ old('ip', $ip->ip) }}">
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
