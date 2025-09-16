<div class="card bg-none card-box">
    <form action="{{ route('announcement.update', $announcement->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Announcement Title') }}</label>
                    <input type="text" name="title" id="title" class="form-control"
                        placeholder="{{ __('Enter Announcement Title') }}" value="{{ $announcement->title }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="branch_id" class="form-control-label">{{ __('Branch') }}</label>
                    <select name="branch_id" id="branch_id" class="form-control select2">
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ $branch->id == $announcement->branch_id ? 'selected' : '' }}>{{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="department_id" class="form-control-label">{{ __('Department') }}</label>
                    <select name="department_id" id="department_id" class="form-control select2">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ $department->id == $announcement->department_id ? 'selected' : '' }}>
                                {{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_date" class="form-control-label">{{ __('Announcement Start Date') }}</label>
                    <input type="text" name="start_date" id="start_date" class="form-control datepicker"
                        value="{{ $announcement->start_date }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end_date" class="form-control-label">{{ __('Announcement End Date') }}</label>
                    <input type="text" name="end_date" id="end_date" class="form-control datepicker"
                        value="{{ $announcement->end_date }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Announcement Description') }}</label>
                    <textarea name="description" id="description" class="form-control" placeholder="{{ __('Enter Announcement Title') }}">{{ $announcement->description }}</textarea>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
