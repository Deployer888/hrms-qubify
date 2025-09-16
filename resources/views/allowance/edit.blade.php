<div class="card bg-none card-box">
    <form action="{{ route('allowance.update', $allowance->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body p-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="allowance_option">{{ __('Allowance Options*') }}</label>
                        <select name="allowance_option" id="allowance_option" class="form-control select2" required>
                            @foreach ($allowance_options as $key => $value)
                                <option value="{{ $key }}" {{ $key == $allowance->allowance_option ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="title">{{ __('Title') }}</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ $allowance->title }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="amount">{{ __('Amount') }}</label>
                        <input type="number" name="amount" id="amount" class="form-control" value="{{ $allowance->amount }}" required>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
