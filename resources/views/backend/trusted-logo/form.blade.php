<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="image" class="form-label">{{ __('Image') }}</label>
            <input type="file" name="image" class="form-control-file @error('image') is-invalid @enderror" id="image">
            @if(isset($trustedLogo->image))
                <img src="{{ asset($trustedLogo->image) }}" class="bg-primary p-2" alt="image Preview" class="mt-2" style="max-width: 100px;">
            @endif
            {!! $errors->first('image', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div> 

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>