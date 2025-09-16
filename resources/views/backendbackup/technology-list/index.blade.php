@extends('backend.layouts.master')

@section('template_title')
    Technology Lists
@endsection

@section('admin-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Technology Lists') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('admin.technology-lists.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                              </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        
									<th >Name</th>
									<th >Icon</th>
									<th >Description</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($technologyLists as $technologyList)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $technologyList->name }}</td>
										<td ><img src="{{ asset($technologyList->icon) }}" alt="Technology Icon" height="50px" width="50px" ></td>
										<td >{{ $technologyList->description }}</td>

                                            <td>
                                                <form action="{{ route('admin.technology-lists.destroy', $technologyList->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('admin.technology-lists.show', $technologyList->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('admin.technology-lists.edit', $technologyList->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $technologyLists->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
