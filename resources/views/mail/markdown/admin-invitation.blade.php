<x-mail::message>
# Introduction

Invited by {{ $invite->admin->username }}.

<x-mail::button :url="$invitationUrl">
Accept Invitation
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
