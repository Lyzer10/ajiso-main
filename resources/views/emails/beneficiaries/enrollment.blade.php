@component('mail::message')
# Usajili wa Taarifa zako umekamilika,

The body of your message. {{ $body }}

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Ahsante,<br>
{{ config('app.name') }}
@endcomponent
