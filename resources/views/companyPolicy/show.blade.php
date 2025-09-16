<div class="card bg-none card-box">
    <div class="row">
        <!-- Attachment display -->
        <div class="col-md-12">
            <label class="form-control-label font-weight-bold">{{ __('Attachment') }}</label>
            <div class="form-group">
                @if($companyPolicy->attachment)
                    @php
                        $filePath = asset('storage/uploads/companyPolicy/' . $companyPolicy->attachment);
                        $fileExtension = pathinfo($companyPolicy->attachment, PATHINFO_EXTENSION);
                    @endphp
                    
                    <!-- Display Image if it's an image file -->
                    @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ $filePath }}" class="img-fluid" alt="Attachment">
                    @elseif(in_array(strtolower($fileExtension), ['pdf']))
                        <!-- Display PDF without controls and no border using class & frameborder -->
                        <iframe src="{{ $filePath }}" width="100%" height="700px" class="pdf-iframe" frameborder="0"></iframe>
                    @else
                        <!-- For other file types, just show a link to download -->
                        <p>{{ __('Attachment:') }} <a href="{{ $filePath }}" target="_blank">{{ $companyPolicy->attachment }}</a></p>
                    @endif
                @else
                    <p>{{ __('No attachment available') }}</p>
                @endif
            </div>
        </div>

        @if($companyPolicy->attachment)
        <div class="col-12">
           <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $companyPolicy->title }}</h5>
            
                    @if($isAcknowledged)
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle-fill"></i> The policy has already been acknowledged by you.
                        </div>
                    @else
                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-exclamation-circle-fill"></i> You have not yet acknowledged this policy.
                        </div>
                        
                        <!-- Acknowledgment Form -->
                        <form action="{{ route('acknowledge.store') }}" method="POST">
                            @csrf
                            <div class="d-flex" style="justify-content:space-between;">
                                <div class="form-check acknoledge-check">
                                    <input type="hidden" name="emp_id" value="{{ \Auth::user()->employee->id }}">
                                    <input type="hidden" name="company_policy_id" value="{{ $companyPolicy->id }}">
                                    <input class="form-check-input" type="checkbox" name="acknowledgeCheck" id="acknowledgeCheck">
                                    <label class="form-check-label" for="acknowledgeCheck">
                                        I have read and understood the policy.
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-square"></i> Acknowledge Policy
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endif
        <!-- Close button -->
        <div class="col-12">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
        </div>
    </div>
</div>

<!-- Add custom CSS to remove border explicitly -->
<style>
    .pdf-iframe {
        border: none !important;
    }
    .card form input{
        height: auto !important;
    }
    .acknoledge-check{
        align-content: center;
    }
    .modal-dialog {
        max-width: 1640px;
        margin: 1.75rem 30px 1.75rem 270px;
    }
</style>
