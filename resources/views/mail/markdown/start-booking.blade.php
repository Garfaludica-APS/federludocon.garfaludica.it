<x-mail::message :message="$message" :logoPath="$logoPath">
# {{ __('Book your rooms for the GobCon!') }}

{{ __('Hello') }},

{{ __('Press the button below to start booking your rooms and meals for the GobCon!') }}

<x-mail::button :url="$booking->getSignedUrl()">
	{{ __('Book Now!') }}
</x-mail::button>

{{ __('If the button does not work, copy and paste the following link in your browser\'s address bar: :link', ['link' => $booking->getSignedUrl()]) }}

{{ __('Link will expire in 2 hours.') }}

{{ __('If you have any questions, please do not hesitate to contact us.') }}

{{ __('Thanks') }},

Garfaludica APS
</p>
</x-mail::message>
