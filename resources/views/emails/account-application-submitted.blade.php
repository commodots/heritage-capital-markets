<p>A new {{ ucfirst($application->account_type) }} account application has been submitted.</p>

<p>
    <strong>Reference:</strong> {{ $application->reference }}<br>
    <strong>Applicant:</strong> {{ $application->applicant_name ?? 'Not provided' }}<br>
    <strong>Submitted:</strong> {{ $application->submitted_at?->format('j M Y, H:i') ?? now()->format('j M Y, H:i') }}
</p>

<p>The completed application is attached as a PDF. You can also download it securely from the admin portal:</p>

<p><a href="{{ $pdfUrl }}">Download application PDF</a></p>
