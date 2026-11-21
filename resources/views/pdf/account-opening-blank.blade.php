<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 30px 34px; }
        body { color: #17283f; font-family: DejaVu Sans, sans-serif; font-size: 9px; line-height: 1.4; }
        header { background: #102b4e; color: #fff; margin: -30px -34px 18px; padding: 18px 34px 14px; }
        header h1 { color: #d6ad55; font-size: 16px; margin: 0 0 4px; }
        header p { margin: 2px 0; }
        h2 { border-bottom: 1px solid #d6ad55; color: #102b4e; font-size: 11px; margin: 16px 0 7px; padding-bottom: 3px; }
        h3 { color: #102b4e; font-size: 9px; margin: 10px 0 4px; }
        table { border-collapse: collapse; width: 100%; }
        td { padding: 5px 5px 7px; vertical-align: bottom; }
        td.label { color: #42536a; width: 22%; }
        td.answer { border-bottom: 1px solid #9aa8b8; width: 28%; }
        .note { color: #627186; font-size: 8px; }
        .declaration { border: 1px solid #dfe5ec; margin-top: 8px; padding: 8px; }
        .line { border-bottom: 1px solid #9aa8b8; display: inline-block; height: 14px; width: 210px; }
        .footer { border-top: 1px solid #dfe5ec; color: #627186; font-size: 8px; margin-top: 18px; padding-top: 6px; }
    </style>
</head>
<body>
    <header>
        <h1>Heritage Capital Markets Limited</h1>
        <p>{{ $schema['accountLabel'] }} — Account Opening Form</p>
        <p>Blank printable form · Fields marked * are required for online submission.</p>
    </header>

    <p class="note">
        This printable form mirrors the current online application fields. Use the online form for validation and to enter additional applicants.
    </p>

    @foreach ($schema['fieldsByStep'] as $stepIndex => $stepFields)
        @php
            $fields = array_values(array_filter(
                $stepFields,
                fn ($field) => $field !== $schema['peopleField'],
            ));
            $requiredFields = $schema['requiredFieldsByStep'][$stepIndex] ?? [];
        @endphp

        @if ($fields)
            <h2>{{ $schema['stepTitles'][$stepIndex] }}</h2>
            <table>
                @foreach (array_chunk($fields, 2) as $row)
                    <tr>
                        @foreach ($row as $field)
                            <td class="label">
                                {{ $schema['fieldLabels'][$field] ?? ucfirst(str_replace('_', ' ', $field)) }}
                                @if (in_array($field, $requiredFields, true))*@endif
                            </td>
                            <td class="answer"></td>
                        @endforeach
                        @if (count($row) === 1)
                            <td></td><td></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @endif

        @if ($schema['peopleField'] !== null && in_array($schema['peopleField'], $stepFields, true))
            <h2>{{ $schema['fieldLabels'][$schema['peopleField']] }}</h2>
            @for ($personIndex = 1; $personIndex <= $schema['peopleMinimum']; $personIndex++)
                <h3>
                    {{ $schema['peopleSingularLabel'] }} {{ $personIndex }}
                </h3>
                <table>
                    @foreach (array_chunk($schema['personFields'], 2) as $row)
                        <tr>
                            @foreach ($row as $field)
                                <td class="label">
                                    {{ $schema['personFieldLabels'][$field] }}
                                    @if (in_array($field, $schema['requiredPersonFields'], true))*@endif
                                </td>
                                <td class="answer"></td>
                            @endforeach
                            @if (count($row) === 1)
                                <td></td><td></td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            @endfor
            <p class="note">Attach an additional sheet if there are more applicants, directors or administrators.</p>
        @endif
    @endforeach

    <h2>Supporting documents (where applicable)</h2>
    <p>[ ] Passport photograph &nbsp;&nbsp; [ ] Means of identification &nbsp;&nbsp; [ ] Recent utility bill</p>
    <p>[ ] Birth certificate (minor account) &nbsp;&nbsp; [ ] Signature / mandate</p>

    <div class="declaration">
        <strong>Declaration</strong>
        <p>I confirm that the information supplied is accurate and consent to Heritage Capital Markets processing it for account-opening review.</p>
        <p>Applicant / authorized signatory: <span class="line"></span> &nbsp; Date: <span class="line"></span></p>
    </div>

    <p class="footer">For additional entries or to submit this application, complete the online form at the Heritage Capital Markets website.</p>
</body>
</html>
