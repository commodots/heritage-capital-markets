<!DOCTYPE html>
<html lang="en">
<body>
    @if ($subscriberName)
        <p>Hello {{ $subscriberName }},</p>
    @endif

    <div>{!! nl2br(e($body)) !!}</div>

    <p>
        You are receiving this email because you subscribed to Heritage Capital Markets updates.
        <a href="{{ $unsubscribeUrl }}">Unsubscribe</a>
    </p>
</body>
</html>
