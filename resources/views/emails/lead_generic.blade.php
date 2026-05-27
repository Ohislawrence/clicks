<x-mail::message>
# Hello {{ $lead->name }},

{!! nl2br(e($content)) !!}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
