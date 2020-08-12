
@component('mail::message')
    # Introduction

    Hello {{$user->username}}
    Thank you for creating an account. Please verify your email using this link:

    @component('mail::button', ['url' => route('verify', $user->verification_token)])
        Click here
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
