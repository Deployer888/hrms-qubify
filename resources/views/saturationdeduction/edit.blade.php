<div class="card bg-none card-box">
    <form action="{{ route('saturationdeduction.update', $saturationdeduction->id) }}" method="post">
        @method('PUT')
        <div class="card-body p-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="deduction_option">{{ __('Deduction Options*') }}</label>
                        <select name="deduction_option" id="deduction_option" class="form-control select2" required>
                            @foreach ($deduction_options as $option)
                                <option value="{{ $option }}"
                                    {{ $saturationdeduction->deduction_option == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">{{ __('Title') }}</label>
                        <input type="text" name="title" id="title" class="form-control" required
                            value="{{ $saturationdeduction->title }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="amount">{{ __('Amount') }}</label>
                        <input type="number" name="amount" id="amount" class="form-control" required step="0.01"
                            value="{{ $saturationdeduction->amount }}">
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
