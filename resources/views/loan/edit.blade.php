<div class="card bg-none card-box">
    <form action="{{ route('loan.update', $loan->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body p-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="title">{{ __('Title') }}</label>
                        <input type="text" name="title" id="title" class="form-control"
                            value="{{ old('title', $loan->title) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="loan_option">{{ __('Loan Options*') }}</label>
                        <select name="loan_option" id="loan_option" class="form-control select2" required>
                            @foreach ($loan_options as $key => $option)
                                <option value="{{ $key }}"
                                    {{ old('loan_option', $loan->loan_option) == $key ? 'selected' : '' }}>
                                    {{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="amount">{{ __('Loan Amount') }}</label>
                        <input type="number" name="amount" id="amount" class="form-control"
                            value="{{ old('amount', $loan->amount) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="start_date">{{ __('Start Date') }}</label>
                        <input type="text" name="start_date" id="start_date" class="form-control datepicker"
                            value="{{ old('start_date', $loan->start_date) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="end_date">{{ __('End Date') }}</label>
                        <input type="text" name="end_date" id="end_date" class="form-control datepicker"
                            value="{{ old('end_date', $loan->end_date) }}" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="reason">{{ __('Reason') }}</label>
                        <textarea name="reason" id="reason" class="form-control" required>{{ old('reason', $loan->reason) }}</textarea>
                    </div>
                </div>
                <div class="col-12">
                    <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                    <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
                </div>
            </div>
        </div>
    </form>
</div>
