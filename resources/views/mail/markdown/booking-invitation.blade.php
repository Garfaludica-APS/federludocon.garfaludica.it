<x-mail::message :message="$message" :logoPath="$logoPath">
# Mancano pochi giorni! Prenota ora!

Ciao **{{ $name }}**,

Ricevi questa email in quanto, in data {{ $date }}, hai avviato la procedura di prenotazione per l'evento GobCon Garfagnana 2024 ma non hai completato il processo.

Se desideri partecipare all'evento, ti invitiamo a completare la prenotazione entro il **13 giugno**!

In caso di domande o problemi con la prenotazione, contattaci a **info@garfaludica.it**.

{{ __('Press the button below to start booking your rooms and meals for the GobCon!') }}

<x-mail::button url="https://gobcon.garfaludica.it/book">
	{{ __('Book Now!') }}
</x-mail::button>

_Messaggio automatico, non rispondere a questa email. La ricezione di questa email non costituisce iscrizione ad alcuna newsletter: questa è l'ultima comunicazione che riceverai in merito a questo evento._

{{ __('Thanks') }},

Garfaludica APS
</x-mail::message>
