<div class="card bg-none card-box">
    <form action="{{ route('expense.update', $expense->id) }}" method="POST">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="account_id" class="form-control-label">{{ __('Account') }}</label>
                    <select name="account_id" id="account_id" class="form-control select2"
                        placeholder="{{ __('Choose Account') }}">
                        @foreach ($accounts as $id => $name)
                            <option value="{{ $id }}" {{ $expense->account_id == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                    <input type="number" name="amount" id="amount" class="form-control"
                        placeholder="{{ __('Amount') }}" value="{{ $expense->amount }}" step="0.01">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="date" class="form-control-label">{{ __('Date') }}</label>
                    <input type="text" name="date" id="date" class="form-control datepicker"
                        value="{{ $expense->date }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expense_category_id" class="form-control-label">{{ __('Category') }}</label>
                    <select name="expense_category_id" id="expense_category_id" class="form-control select2"
                        placeholder="{{ __('Choose A Category') }}">
                        @foreach ($expenseCategory as $id => $name)
                            <option value="{{ $id }}"
                                {{ $expense->expense_category_id == $id ? 'selected' : '' }}>{{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payee_id" class="form-control-label">{{ __('Payee') }}</label>
                    <select name="payee_id" id="payee_id" class="form-control select2">
                        @foreach ($payees as $id => $name)
                            <option value="{{ $id }}" {{ $expense->payee_id == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payment_type_id" class="form-control-label">{{ __('Payment Method') }}</label>
                    <select name="payment_type_id" id="payment_type_id" class="form-control select2"
                        placeholder="{{ __('Choose Payment Method') }}">
                        @foreach ($paymentTypes as $id => $name)
                            <option value="{{ $id }}"
                                {{ $expense->payment_type_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="referal_id" class="form-control-label">{{ __('Ref#') }}</label>
                    <input type="text" name="referal_id" id="referal_id" class="form-control"
                        value="{{ $expense->referal_id }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Description') }}</label>
                    <textarea name="description" id="description" class="form-control" placeholder="{{ __('Description') }}">{{ $expense->description }}</textarea>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
