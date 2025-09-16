<div class="row padding-1 p-1">
    <div class="col-md-12">

        <div class="form-group mb-2 mb20">
            <label for="technology_id" class="form-label">{{ __('Technology') }}</label>
            <select name="technology_id" class="form-control @error('technology_id') is-invalid @enderror"
                id="technology_id">
                <option value="">Select Technology Type</option>
                @foreach($type as $technology)
                    <option value="{{ $technology->id }}"
                        {{ old('technology_id', $industryList->technology_id ?? '') == $technology->id ? 'selected' : '' }}>
                        {{ $technology->name }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('technology_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong>
            </div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $industryList->name ?? '') }}"
                id="name" placeholder="Name">
            {!! $errors->first('name', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="description" class="form-label">{{ __('Description') }}</label>
            <input type="text" name="description" class="form-control @error('description') is-invalid @enderror"
                value="{{ old('description', $industryList->description ?? '') }}"
                id="description" placeholder="Description">
            {!! $errors->first('description', '<div class="invalid-feedback" role="alert"><strong>:message</strong>
            </div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="icon" class="form-label">{{ __('Icon') }}</label>
            <input type="file" name="icon" class="form-control-file @error('icon') is-invalid @enderror" id="icon">
            @if(isset($industryList->icon))
                <img src="{{ asset($industryList->icon) }}" alt="Icon Preview" class="mt-2" style="max-width: 100px;">
            @endif
            {!! $errors->first('icon', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>        

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>
