<x-mail::message :message="$message" :logoPath="$logoPath">
# {{ __('Book your rooms for the GobCon!') }}

{{ __('Hello,') }}

{{ __('Press the button below to start booking your rooms and meals for the GobCon!') }}

<x-mail::button :url="$booking->getSignedUrl()">
	{{ __('Book Now!') }}
</x-mail::button>

{{ __('Thanks') }},

Garfaludica APS
</p>
</x-mail::message>
