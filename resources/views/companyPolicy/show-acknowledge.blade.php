<div class="card bg-none card-box">
    <div class="card-header">
        <h5 class="card-title">Employee Acknowledgment Status</h5>
    </div>

    <div class="card-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="acknowledgeTabs" style="border: none!important;" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="acknowledged-tab" data-bs-toggle="tab" href="#acknowledged" role="tab" aria-controls="acknowledged" aria-selected="true">Acknowledged</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-black" id="not-acknowledged-tab" data-bs-toggle="tab" href="#not-acknowledged" role="tab" aria-controls="not-acknowledged" aria-selected="false">Not Acknowledged</a>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content mt-3" id="acknowledgeTabsContent">
            <!-- Acknowledged Tab -->
            <div class="tab-pane fade show active" id="acknowledged" role="tabpanel" aria-labelledby="acknowledged-tab">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Employee Name</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                @if(in_array($employee->id, $acknowledges))
                                    <tr>
                                        <td>{{ $employee->name }}</td>
                                        <td>
                                            <span class="badge badge-success">Acknowledged</span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Not Acknowledged Tab -->
            <div class="tab-pane fade" id="not-acknowledged" role="tabpanel" aria-labelledby="not-acknowledged-tab">
                <div class="table-responsive" style="max-height: 400px!important; overflow-y: auto;">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Employee Name</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                @if(!in_array($employee->id, $acknowledges))
                                    <tr>
                                        <td>{{ $employee->name }}</td>
                                        <td>
                                            <span class="badge badge-danger">Not Acknowledged</span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
