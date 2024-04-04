<x-mail::message :message="$message" :logoPath="$logoPath">

# {{__('Email sent by :username (:email)',['username'=> $invitation->creator->username, 'email' => $invitation->creator->email])}}<br>
{{__('Hello, with this email, we invite you to manage the following hotels:')}}
<ul>
    @foreach ($hotels as $hotel)
	<li><i><b>{{__(':hotel', ['hotel' => $hotel->name]) }}</b></i></li>
    @endforeach
</ul>
{{__('for the event GobCon 2024 Garfagnana.')}}
<x-mail::button :url="$invitationUrl">
Accept Invitation
</x-mail::button>
<b>{{__('After 3 days the invitation link expires and will no longer work.')}}</b>
<br>
{{__('We hope that this event will be profitable.')}}<br>
Thanks,<br>
Garfaludica APS
</x-mail::message>
