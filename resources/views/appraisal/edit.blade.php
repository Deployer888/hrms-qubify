<div class="card bg-none card-box">
    <form action="{{ route('appraisal.update', $appraisal->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="branch" class="form-control-label">{{ __('Branch') }}</label>
                    <select name="branch" id="branch" class="form-control select2" required>
                        @foreach ($branches as $key => $name)
                            <option value="{{ $key }}" {{ $key == $appraisal->branch ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="employee" class="form-control-label">{{ __('Employee') }}</label>
                    <select name="employee" id="employee" class="form-control select2" required>
                        <!-- Employee options will be populated by JavaScript based on branch selection -->
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="appraisal_date" class="form-control-label">{{ __('Select Month') }}</label>
                    <input type="text" name="appraisal_date" id="appraisal_date"
                        class="form-control custom-datepicker" value="{{ $appraisal->appraisal_date }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="remark" class="form-control-label">{{ __('Remarks') }}</label>
                    <textarea name="remark" id="remark" class="form-control">{{ $appraisal->remark }}</textarea>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach ($performance_types as $performance)
                <div class="col-md-12 mt-3">
                    <h6>{{ $performance->name }}</h6>
                    <hr class="mt-0">
                </div>
                @foreach ($performance->types as $type)
                    <div class="col-6">{{ $type->name }}</div>
                    <div class="col-6">
                        <fieldset class="rating">
                            @for ($i = 5; $i >= 1; $i--)
                                <input class="stars" type="radio"
                                    id="rating-{{ $i }}-{{ $type->id }}"
                                    name="rating[{{ $type->id }}]" value="{{ $i }}"
                                    {{ isset($ratings[$type->id]) && $ratings[$type->id] == $i ? 'checked' : '' }}>
                                <label class="full" for="rating-{{ $i }}-{{ $type->id }}"
                                    title="{{ $i }} stars"></label>
                            @endfor
                        </fieldset>
                    </div>
                @endforeach
            @endforeach
        </div>

        <div class="row">
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    function getEmployee(did) {
        $.ajax({
            url: '{{ route('branch.employee.json') }}',
            type: 'POST',
            data: {
                branch: did,
                _token: "{{ csrf_token() }}",
            },
            success: function(data) {
                const employeeSelect = $('#employee');
                employeeSelect.empty();
                employeeSelect.append('<option value="">{{ __('Select Employee') }}</option>');
                data.forEach((value, key) => {
                    employeeSelect.append(
                        `<option value="${key}" ${key == {{ $appraisal->employee }} ? 'selected' : ''}>${value}</option>`
                        );
                });
            }
        });
    }

    $(document).ready(function() {
        const branchId = $('#branch').val();
        getEmployee(branchId);
        $('#branch').on('change', function() {
            getEmployee($(this).val());
        });
    });
</script>
