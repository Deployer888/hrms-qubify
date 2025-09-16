<div class="card bg-none card-box">
    <form method="POST" action="{{ route('promotion.update', $promotion->id) }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="form-group col-lg-6 col-md-6">
                <label for="employee_id" class="form-control-label">Employee</label>
                <select name="employee_id" id="employee_id" class="form-control select2" required>
                    {{-- Option values will be populated dynamically --}}
                </select>
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="designation_id" class="form-control-label">Designation</label>
                <select name="designation_id" id="designation_id" class="form-control select2">
                    {{-- Option values will be populated dynamically --}}
                </select>
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="promotion_title" class="form-control-label">Promotion Title</label>
                <input type="text" name="promotion_title" id="promotion_title" class="form-control" value="">
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="promotion_date" class="form-control-label">Promotion Date</label>
                <input type="text" name="promotion_date" id="promotion_date" class="form-control datepicker"
                    value="">
            </div>
            <div class="form-group col-lg-12">
                <label for="description" class="form-control-label">Description</label>
                <textarea name="description" id="description" class="form-control" placeholder="Enter Description"></textarea>
            </div>
            <div class="col-12">
                <input type="submit" value="Update" class="btn-create badge-blue">
                <input type="button" value="Cancel" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
