<div class="card bg-none card-box">
    <form action="{{ route('leave.changeaction', $leave->id) }}" method="post" id="leaveForm">
        @csrf
        <div class="row">
            <div class="col-12">
                <h5 class="m-4"><b>{{ !empty($leave->reject_reason) ? $leave->reject_reason : '' }}</b></h5>
                
            </div>
        </div>
    </form>
</div>

