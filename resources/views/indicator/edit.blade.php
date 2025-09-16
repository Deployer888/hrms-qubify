<div class="card bg-none card-box">
    <form action="{{ route('indicator.update', $indicator->id) }}" method="POST">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="branch" class="form-control-label">{{ __('Branch') }}</label>
                    <select name="branch" id="branch" class="form-control select2" required>
                        @foreach ($brances as $value => $label)
                            <option value="{{ $value }}" {{ $indicator->branch == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="department" class="form-control-label">{{ __('Department') }}</label>
                    <select name="department" id="department_id" class="form-control select2" required>
                        @foreach ($departments as $value => $label)
                            <option value="{{ $value }}" {{ $indicator->department == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="designation" class="form-control-label">{{ __('Designation') }}</label>
                    <select name="designation" id="designation_id" class="form-control select2-multiple" required data-placeholder="{{ __('Select Designation ...') }}">
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach ($performance_types as $performances)
                <div class="col-md-12 mt-3">
                    <h6>{{ $performances->name }}</h6>
                    <hr class="mt-0">
                </div>
                @foreach($performances->types as $types)
                    <div class="col-6">
                        {{ $types->name }}
                    </div>
                    <div class="col-6">
                        <fieldset id="demo1" class="rating">
                            @for ($i = 5; $i >= 1; $i--)
                                <input class="stars" type="radio" id="technical-{{ $i }}-{{ $types->id }}" name="rating[{{ $types->id }}]" value="{{ $i }}" {{ isset($ratings[$types->id]) && $ratings[$types->id] == $i ? 'checked' : '' }}>
                                <label class="full" for="technical-{{ $i }}-{{ $types->id }}" title="{{ ['Awesome', 'Pretty good', 'Meh', 'Kinda bad', 'Sucks big time'][$i - 1] }} - {{ $i }} stars"></label>
                            @endfor
                        </fieldset>
                    </div>
                @endforeach
            @endforeach
        </div>
        <div class="row">
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    function getDesignation(did) {
        $.ajax({
            url: '{{ route('employee.json') }}',
            type: 'POST',
            data: {
                "department_id": did,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                console.log(data);
                $('#designation_id').empty();
                $('#designation_id').append('<option value="">Select Designation</option>');
                $.each(data, function(key, value) {
                    var select = key == '{{ $indicator->designation }}' ? 'selected' : '';
                    $('#designation_id').append('<option value="' + key + '" ' + select + '>' + value + '</option>');
                });
            }
        });
    }

    $(document).ready(function() {
        var d_id = $('#department_id').val();
        getDesignation(d_id);
    });
</script>
