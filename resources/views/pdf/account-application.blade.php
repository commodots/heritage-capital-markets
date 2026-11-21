<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 34px 38px; }
        body { color: #17283f; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.45; }
        header { background: #102b4e; color: #fff; margin: -34px -38px 24px; padding: 22px 38px 18px; }
        header h1 { color: #d6ad55; font-size: 18px; margin: 0 0 5px; }
        header p { color: #fff; margin: 0; }
        h2 { border-bottom: 1px solid #d6ad55; color: #102b4e; font-size: 12px; margin: 18px 0 8px; padding-bottom: 4px; }
        table { border-collapse: collapse; width: 100%; }
        td { border-bottom: 1px solid #e4e9ef; padding: 5px 7px; vertical-align: top; }
        td.label { color: #627186; font-weight: bold; width: 32%; }
        .person { border: 1px solid #dfe5ec; margin: 0 0 10px; padding: 7px; }
        .person h3 { color: #102b4e; font-size: 10px; margin: 0 0 4px; }
        .disclosure { page-break-inside: avoid; }
        .disclosure p, .disclosure li { margin: 5px 0; }
        .disclosure ol { padding-left: 18px; }
        .muted { color: #627186; }
        .footer { border-top: 1px solid #dfe5ec; color: #627186; font-size: 8px; margin-top: 20px; padding-top: 7px; }
    </style>
</head>
<body>
    <header>
        <h1>Heritage Capital Markets Limited</h1>
        <p>Account opening application · {{ strtoupper($application->account_type) }}</p>
        <p>Reference: {{ $application->reference }} · Generated {{ now()->format('j M Y, H:i') }}</p>
    </header>

    <h2>Application summary</h2>
    <table>
        <tr><td class="label">Applicant / account name</td><td>{{ $application->applicant_name ?: '—' }}</td></tr>
        <tr><td class="label">Email</td><td>{{ $application->email ?: '—' }}</td></tr>
        <tr><td class="label">Telephone</td><td>{{ $application->phone ?: '—' }}</td></tr>
        <tr><td class="label">Application type</td><td>{{ ucfirst($application->account_type) }}</td></tr>
        <tr><td class="label">Status</td><td>{{ ucfirst(str_replace('_', ' ', $application->status)) }}</td></tr>
        <tr><td class="label">Submitted</td><td>{{ $application->submitted_at?->format('j M Y, H:i') ?? '—' }}</td></tr>
    </table>

    @php
        $peopleLists = [
            'directors' => 'Directors / signatories',
            'administrators' => 'Estate administrators',
            'joint_applicants' => 'Joint account holders',
            'legal_signatories' => 'Legal disclosure signatories',
        ];
        $personHeadings = [
            'directors' => 'Director / signatory',
            'administrators' => 'Estate administrator',
            'joint_applicants' => 'Joint account holder',
            'legal_signatories' => 'Signatory',
        ];
        $formData = $application->form_data ?? [];
        $formatLabel = fn ($key, $labels) => $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    @endphp

    <h2>Application details</h2>
    <table>
        @foreach ($formData as $key => $value)
            @continue(is_array($value) || $value === null || $value === '')
            <tr>
                <td class="label">{{ $key === 'declaration_accepted' ? 'Conditions Accepted' : $formatLabel($key, $fieldLabels) }}</td>
                <td>{{ $key === 'declaration_accepted' ? ($value ? 'Accepted' : 'Not accepted') : (is_bool($value) ? ($value ? 'Yes' : 'No') : $value) }}</td>
            </tr>
        @endforeach
    </table>

    @foreach ($peopleLists as $key => $title)
        @if (!empty($formData[$key]))
            <h2>{{ $title }}</h2>
            @foreach ($formData[$key] as $index => $person)
                <div class="person">
                    <h3>{{ $personHeadings[$key] }} {{ $index + 1 }}</h3>
                    <table>
                        @foreach ($person as $field => $value)
                            @continue(is_array($value) || $value === null || $value === '')
                            <tr>
                                <td class="label">{{ $formatLabel($field, $personFieldLabels) }}</td>
                                <td>{{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endforeach
        @endif
    @endforeach

    @foreach ($legalDisclosure as $section)
        <section class="disclosure">
            <h2>{{ $section['title'] }}</h2>
            @if (isset($section['clauses']))
                <ol type="a">
                    @foreach ($section['clauses'] as $clause)
                        <li>{{ $clause }}</li>
                    @endforeach
                </ol>
            @endif
            @foreach ($section['paragraphs'] ?? [] as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
            @if (isset($section['acknowledgement']))
                <p><strong>{{ $section['acknowledgement'] }}</strong></p>
            @endif
        </section>
    @endforeach

    <h2>Supporting documents</h2>
    @forelse (array_keys($application->documents ?? []) as $document)
        <p>{{ ucfirst(str_replace('_', ' ', $document)) }}</p>
    @empty
        <p class="muted">No supporting documents were uploaded.</p>
    @endforelse

    <p class="footer">Confidential customer information. For authorized Heritage Capital Markets review only.</p>
</body>
</html>
