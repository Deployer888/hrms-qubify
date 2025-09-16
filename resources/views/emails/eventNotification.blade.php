@component('mail::message')
# {{ $event->title }}

{{ $event->description }}
**Event Details:**
@if($branch)
- **Branch:** {{ $branch->name }}
@endif
- **Start Date:** {{ $event->start_date }}
- **End Date:** {{ $event->end_date }}
@component('mail::button', ['url' => route('event.show', $event->id)])
View Event
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
