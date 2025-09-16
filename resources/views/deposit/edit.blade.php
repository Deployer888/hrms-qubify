<div class="card bg-none card-box">
    <form action="{{ route('deposit.update', $deposit->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Account select -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="account_id" class="form-control-label">{{ __('Account') }}</label>
                    <select name="account_id" id="account_id" class="form-control select2"
                        placeholder="{{ __('Choose Account') }}">
                        @foreach ($accounts as $id => $name)
                            <option value="{{ $id }}"
                                {{ $id == old('account_id', $deposit->account_id) ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Amount input -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                    <input type="number" name="amount" id="amount" class="form-control"
                        placeholder="{{ __('Amount') }}" step="0.01" value="{{ old('amount', $deposit->amount) }}">
                </div>
            </div>
            <!-- Date input -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="date" class="form-control-label">{{ __('Date') }}</label>
                    <input type="text" name="date" id="date" class="form-control datepicker"
                        value="{{ old('date', $deposit->date) }}">
                </div>
            </div>
            <!-- Income category select -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="income_category_id" class="form-control-label">{{ __('Category') }}</label>
                    <select name="income_category_id" id="income_category_id" class="form-control select2"
                        placeholder="{{ __('Choose A Category') }}">
                        @foreach ($incomeCategory as $id => $name)
                            <option value="{{ $id }}"
                                {{ $id == old('income_category_id', $deposit->income_category_id) ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Payer select -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payer_id" class="form-control-label">{{ __('Payer') }}</label>
                    <select name="payer_id" id="payer_id" class="form-control select2">
                        @foreach ($payers as $id => $name)
                            <option value="{{ $id }}"
                                {{ $id == old('payer_id', $deposit->payer_id) ? 'selected' : '' }}>{{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Payment method select -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payment_type_id" class="form-control-label">{{ __('Payment Method') }}</label>
                    <select name="payment_type_id" id="payment_type_id" class="form-control select2"
                        placeholder="{{ __('Choose Payment Method') }}">
                        @foreach ($paymentTypes as $id => $name)
                            <option value="{{ $id }}"
                                {{ $id == old('payment_type_id', $deposit->payment_type_id) ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Referral ID input -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="referal_id" class="form-control-label">{{ __('Ref#') }}</label>
                    <input type="text" name="referal_id" id="referal_id" class="form-control"
                        value="{{ old('referal_id', $deposit->referal_id) }}">
                </div>
            </div>
            <!-- Description input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Description') }}</label>
                    <textarea name="description" id="description" class="form-control" placeholder="{{ __('Description') }}">{{ old('description', $deposit->description) }}</textarea>
                </div>
            </div>
            <!-- Submit and cancel buttons -->
            <div class="col-12">
                <button type="submit" class="btn-create badge-blue">{{ __('Update') }}</button>
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
