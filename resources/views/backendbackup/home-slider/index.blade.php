@extends('backend.layouts.master')

@section('template_title')
    Home Sliders
@endsection

@section('admin-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Home Sliders') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('admin.home-sliders.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Heading</th>
									<th >Sub Heading</th>
									<th >Image</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($homeSliders as $homeSlider)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $homeSlider->heading }}</td>
										<td >{{ $homeSlider->sub_heading }}</td>
										<td ><img src="{{ asset($homeSlider->image) }}" alt="Slider Image" height="50px" width="50px" ></td>

                                            <td>
                                                <form action="{{ route('admin.home-sliders.destroy', $homeSlider->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('admin.home-sliders.show', $homeSlider->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('admin.home-sliders.edit', $homeSlider->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $homeSliders->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
