<div class="card bg-none card-box">
    <form action="{{ route('transferbalance.update', $transferbalance->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="from_account_id" class="form-control-label">{{ __('From Account') }}</label>
                    <select name="from_account_id" id="from_account_id" class="form-control select2"
                        placeholder="{{ __('Choose Account') }}">
                        @foreach ($accounts as $id => $name)
                            <option value="{{ $id }}" @if ($transferbalance->from_account_id == $id) selected @endif>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="to_account_id" class="form-control-label">{{ __('To Account') }}</label>
                    <select name="to_account_id" id="to_account_id" class="form-control select2"
                        placeholder="{{ __('Choose Account') }}">
                        @foreach ($accounts as $id => $name)
                            <option value="{{ $id }}" @if ($transferbalance->to_account_id == $id) selected @endif>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="date" class="form-control-label">{{ __('Date') }}</label>
                    <input type="text" name="date" id="date" class="form-control datepicker"
                        value="{{ $transferbalance->date }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                    <input type="number" name="amount" id="amount" class="form-control" step="0.01"
                        value="{{ $transferbalance->amount }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payment_type_id" class="form-control-label">{{ __('Payment Method') }}</label>
                    <select name="payment_type_id" id="payment_type_id" class="form-control select2"
                        placeholder="{{ __('Choose Payment Method') }}">
                        @foreach ($paymentTypes as $id => $name)
                            <option value="{{ $id }}" @if ($transferbalance->payment_type_id == $id) selected @endif>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="referal_id" class="form-control-label">{{ __('Ref#') }}</label>
                    <input type="text" name="referal_id" id="referal_id" class="form-control"
                        value="{{ $transferbalance->referal_id }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Description') }}</label>
                    <textarea name="description" id="description" class="form-control" placeholder="{{ __('Description') }}">{{ $transferbalance->description }}</textarea>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-bs-dismiss="modal">
            </div>
        </div>
    </form>
</div>
