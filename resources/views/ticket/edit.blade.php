<div class="card bg-none card-box">
    <form action="{{ route('ticket.update', $ticket->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Subject') }}</label>
                    <input type="text" name="title" class="form-control"
                        placeholder="{{ __('Enter Ticket Subject') }}" value="{{ $ticket->title }}">
                </div>
            </div>
        </div>
        @if (\Auth::user()->type != 'employee')
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="employee_id" class="form-control-label">{{ __('Ticket for Employee') }}</label>
                        <select name="employee_id" class="form-control select2">
                            @foreach ($employees as $id => $name)
                                <option value="{{ $id }}" @if ($ticket->employee_id == $id) selected @endif>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="priority" class="form-control-label">{{ __('Priority') }}</label>
                    <select name="priority" class="form-control select2">
                        <option value="low" @if ($ticket->priority == 'low') selected @endif>{{ __('Low') }}
                        </option>
                        <option value="medium" @if ($ticket->priority == 'medium') selected @endif>{{ __('Medium') }}
                        </option>
                        <option value="high" @if ($ticket->priority == 'high') selected @endif>{{ __('High') }}
                        </option>
                        <option value="critical" @if ($ticket->priority == 'critical') selected @endif>{{ __('Critical') }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="end_date" class="form-control-label">{{ __('End Date') }}</label>
                    <input type="text" name="end_date" class="form-control datepicker"
                        value="{{ $ticket->end_date }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Description') }}</label>
                    <textarea name="description" class="form-control" placeholder="{{ __('Ticket Description') }}">{{ $ticket->description }}</textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="status" class="form-control-label">{{ __('Status') }}</label>
                    <select name="status" class="form-control select2">
                        <option value="close" @if ($ticket->status == 'close') selected @endif>{{ __('Close') }}
                        </option>
                        <option value="open" @if ($ticket->status == 'open') selected @endif>{{ __('Open') }}
                        </option>
                        <option value="onhold" @if ($ticket->status == 'onhold') selected @endif>{{ __('On Hold') }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12">
            <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
            <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-bs-dismiss="modal">
        </div>
    </form>
</div>
