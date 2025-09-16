<div class="card bg-none card-box">
    <form action="{{ route('commission.update', $commission->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body p-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="title">{{ __('Title') }}</label>
                        <input type="text" name="title" id="title" class="form-control" required
                            value="{{ old('title', $commission->title) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="amount">{{ __('Amount') }}</label>
                        <input type="number" name="amount" id="amount" class="form-control" required step="0.01"
                            value="{{ old('amount', $commission->amount) }}">
                    </div>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn-create badge-blue">{{ __('Update') }}</button>
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
