<div class="card bg-none card-box">
    <form action="{{ route('trainer.update', $trainer->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="branch" class="form-control-label">{{ __('Branch') }}</label>
                    <select name="branch" id="branch" class="form-control select2" required>
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}" @if ($trainer->branch == $id) selected @endif>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="firstname" class="form-control-label">{{ __('First Name') }}</label>
                    <input type="text" name="firstname" id="firstname" class="form-control"
                        value="{{ $trainer->firstname }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lastname" class="form-control-label">{{ __('Last Name') }}</label>
                    <input type="text" name="lastname" id="lastname" class="form-control"
                        value="{{ $trainer->lastname }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="contact" class="form-control-label">{{ __('Contact') }}</label>
                    <input type="text" name="contact" id="contact" class="form-control"
                        value="{{ $trainer->contact }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email" class="form-control-label">{{ __('Email') }}</label>
                    <input type="text" name="email" id="email" class="form-control"
                        value="{{ $trainer->email }}" required>
                </div>
            </div>
            <div class="form-group col-lg-12">
                <label for="expertise" class="form-control-label">{{ __('Expertise') }}</label>
                <textarea name="expertise" id="expertise" class="form-control" placeholder="{{ __('Expertise') }}">{{ $trainer->expertise }}</textarea>
            </div>
            <div class="form-group col-lg-12">
                <label for="address" class="form-control-label">{{ __('Address') }}</label>
                <textarea name="address" id="address" class="form-control" placeholder="{{ __('Address') }}">{{ $trainer->address }}</textarea>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-bs-dismiss="modal">
            </div>
        </div>
    </form>
</div>
