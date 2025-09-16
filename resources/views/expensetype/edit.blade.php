<div class="card bg-none card-box">
    <form action="{{ route('expensetype.update', $expensetype->id) }}" method="POST">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="name" class="form-control-label">Expense Name</label>
                    <input type="text" name="name" id="name" class="form-control"
                        placeholder="Enter Expense Type Name" value="{{ $expensetype->name }}">
                    @error('name')
                        <span class="invalid-name" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="Update" class="btn-create badge-blue">
                <input type="button" value="Cancel" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
