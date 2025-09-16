<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="heading" class="form-label">{{ __('Heading') }}</label>
            <input type="text" name="heading" class="form-control @error('heading') is-invalid @enderror" value="{{ old('heading', $homeSlider?->heading) }}" id="heading" placeholder="Heading">
            {!! $errors->first('heading', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="sub_heading" class="form-label">{{ __('Sub Heading') }}</label>
            <input type="text" name="sub_heading" class="form-control @error('sub_heading') is-invalid @enderror" value="{{ old('sub_heading', $homeSlider?->sub_heading) }}" id="sub_heading" placeholder="Sub Heading">
            {!! $errors->first('sub_heading', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="image" class="form-label">{{ __('Image') }}</label>
            <input type="file" name="image" class="form-control-file @error('image') is-invalid @enderror" id="image" accept="image/*">
            @if ($homeSlider && $homeSlider->image)
                <img src="{{ asset($homeSlider->image) }}" alt="Slider Image" class="mt-2" style="max-width: 200px;">
            @endif
            {!! $errors->first('image', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>