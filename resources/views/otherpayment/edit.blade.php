<div class="card bg-none card-box">
    <form action="{{ route('otherpayment.update', ['id' => $otherpayment->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body p-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="title">{{ __('Title') }}</label>
                        <input type="text" name="title" id="title" value="{{ $otherpayment->title }}"
                            class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="amount">{{ __('Amount') }}</label>
                        <input type="number" name="amount" id="amount" value="{{ $otherpayment->amount }}"
                            class="form-control" step="0.01" required>
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
