@extends('backend.layouts.master')

@section('template_title')
    Home Roadmaps
@endsection

@section('admin-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Home Roadmaps') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('admin.home-roadmaps.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Title</th>
                                    <th >Icon</th>
									<th >Description</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($homeRoadmaps as $homeRoadmap)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $homeRoadmap->title }}</td>
                                        <td ><img src="{{ asset($homeRoadmap->icon) }}" class="bg bg-primary p-2" alt="Roadmap Icon" height="50px" width="50px" ></td>
										<td >{{ $homeRoadmap->description }}</td>

                                            <td>
                                                <form action="{{ route('admin.home-roadmaps.destroy', $homeRoadmap->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('admin.home-roadmaps.show', $homeRoadmap->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('admin.home-roadmaps.edit', $homeRoadmap->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $homeRoadmaps->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
