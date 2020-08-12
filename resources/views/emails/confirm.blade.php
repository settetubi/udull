@component('mail::message')
    # Introduction

    Hello {{$user->username}}
    Your email has changed , you have to confirm it clicking this link:

    @component('mail::button', ['url' => route('verify', $user->verification_token)])
        Click here
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
