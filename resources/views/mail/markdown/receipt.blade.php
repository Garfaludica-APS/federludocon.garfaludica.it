<x-mail::message :message="$message" :logoPath="$logoPath">
# {{ __('Your order has been confirmed!') }}

{{ __('Order ID: :id', ['id' => 'GARFALUDICA-' . str_pad(strval($booking->short_id), 4, '0', STR_PAD_LEFT)]) }}

{{ __('You can find the details of your order in the PDF attached to this email.') }}

{{ __('If you want to add notes to your order, or ask for a refund, please click the button below.') }}

<x-mail::button :url="$booking->getModifyUrl()">
	{{ __('Manage Order') }}
</x-mail::button>

{{ __('If the button does not work, copy and paste the following link in your browser\'s address bar: :link', ['link' => $booking->getModifyUrl()]) }}

{{ __('Do not lose this email!') }}

{{ __('If you have any questions, please do not hesitate to contact us.') }}

{{ __('Thanks') }},

Garfaludica APS
</p>
</x-mail::message>
