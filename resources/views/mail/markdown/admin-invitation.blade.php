<x-mail::message :message="$message" :logoPath="$logoPath">
# {{__('Email sent by :username (:email)',['username'=> $invitation->creator->username, 'email' => $invitation->creator->email])}}<br>
<p style="font-family: Times New Roman">
@if ($invitation->is_super_admin)
	{{__('Hello, with this email, we invite you as a super admin of the website')}}
@else
	{{__('Hello, with this email, we invite you to manage the following hotels:')}}
	<ul>
	@foreach ($hotels as $hotel)
		<li><i><b>{{__(':hotel', ['hotel' => $hotel->name]) }}</b></i></li>
	@endforeach
	</ul>
	{{__('for the event GobCon 2024 Garfagnana.')}}
@endif
</p>
<x-mail::button :url="$invitationUrl">
	{{__('Accept Invitation')}}
</x-mail::button>
<p style="font-family: Times New Roman">
<b>{{__('After 3 days the invitation link expires and will no longer work.')}}</b><br>
{{__('We hope that this event will be profitable.')}}<br>
{{__('Thanks')}},<br>
Garfaludica APS
</p>
</x-mail::message>
