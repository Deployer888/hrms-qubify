<div class="card bg-none card-box">
    <form action="{{ route('complaint.update', $complaint->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            @if (\Auth::user()->type != 'employee')
                <!-- Complaint From select -->
                <div class="form-group col-md-6 col-lg-6">
                    <label for="complaint_from" class="form-control-label">{{ __('Complaint From') }}</label>
                    <select name="complaint_from" id="complaint_from" class="form-control select2" required>
                        @foreach ($employees as $id => $name)
                            <option value="{{ $id }}"
                                {{ $id == $complaint->complaint_from ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <!-- Complaint Against select -->
            <div class="form-group col-md-6 col-lg-6">
                <label for="complaint_against" class="form-control-label">{{ __('Complaint Against') }}</label>
                <select name="complaint_against" id="complaint_against" class="form-control select2">
                    @foreach ($employees as $id => $name)
                        <option value="{{ $id }}"
                            {{ $id == $complaint->complaint_against ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Title input -->
            <div class="form-group col-md-6 col-lg-6">
                <label for="title" class="form-control-label">{{ __('Title') }}</label>
                <input type="text" name="title" id="title" class="form-control"
                    value="{{ old('title', $complaint->title) }}">
            </div>
            <!-- Complaint Date input -->
            <div class="form-group col-md-6 col-lg-6">
                <label for="complaint_date" class="form-control-label">{{ __('Complaint Date') }}</label>
                <input type="text" name="complaint_date" id="complaint_date" class="form-control datepicker"
                    value="{{ old('complaint_date', $complaint->complaint_date) }}">
            </div>
            <!-- Description input -->
            <div class="form-group col-md-12">
                <label for="description" class="form-control-label">{{ __('Description') }}</label>
                <textarea name="description" id="description" class="form-control" placeholder="{{ __('Enter Description') }}">{{ old('description', $complaint->description) }}</textarea>
            </div>
            <!-- Submit and cancel buttons -->
            <div class="col-12">
                <button type="submit" class="btn-create badge-blue">{{ __('Update') }}</button>
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
