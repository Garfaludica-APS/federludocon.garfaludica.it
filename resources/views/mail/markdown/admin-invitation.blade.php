<x-mail::message :message="$message" :logoPath="$logoPath">

{{__('You have been invited by :username (:email) to manage the following hotels:',['username'=> $invitation->creator->username, 'email' => $invitation->creator->email])}}
@foreach ($hotels as $hotel)
	<ul>{{ $hotel->name }}</ul>
@endforeach

For the event GobCon 2024 Garfagnana.
<x-mail::button :url="$invitationUrl">
Accept Invitation
</x-mail::button>

Thanks,<br>
Garfaludica APS
</x-mail::message>
