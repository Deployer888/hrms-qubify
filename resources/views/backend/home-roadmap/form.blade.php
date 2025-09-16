<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="title" class="form-label">{{ __('Title') }}</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $homeRoadmap?->title) }}" id="title" placeholder="Title">
            {!! $errors->first('title', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="description" class="form-label">{{ __('Description') }}</label>
            <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description', $homeRoadmap?->description) }}" id="description" placeholder="Description">
            {!! $errors->first('description', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="icon" class="form-label">{{ __('Icon') }}</label>
            <input type="file" name="icon" class="form-control-file @error('icon') is-invalid @enderror" id="icon">
            @if(isset($homeRoadmap->icon))
                <img src="{{ asset($homeRoadmap->icon) }}" class="bg bg-primary p-2" alt="Icon Preview" class="mt-2" style="max-width: 100px;">
            @endif
            {!! $errors->first('icon', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>  
    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>