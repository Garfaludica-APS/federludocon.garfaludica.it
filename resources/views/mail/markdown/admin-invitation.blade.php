<x-mail::message>
# Introduction

Invited by {{ $invitation->creator->username }}.

<x-mail::button :url="$invitationUrl">
Accept Invitation
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
