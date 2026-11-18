<?php

namespace App\Http\Controllers;

use App\Mail\AccountApplicationSubmitted;
use App\Models\AccountApplication;
use App\Models\User;
use App\Support\AccountOpeningFormSchema;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use RuntimeException;

class AccountApplicationController extends Controller
{
    private const TYPES = AccountOpeningFormSchema::TYPES;

    public function start(string $type)
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        $application = $this->currentDraft($type);

        return Inertia::render('AccountOpening/Start', [
            'accountType' => $type,
            'accountTypes' => self::TYPES,
            'formSchema' => AccountOpeningFormSchema::forType($type),
            'accountApplication' => $application ? [
                'id' => $application->id,
                'reference' => $application->reference,
                'current_step' => $application->current_step,
                'form_data' => $application->form_data ?? [],
            ] : null,
        ]);
    }

    /** Save one step at a time. Documents are stored on Laravel's private local disk. */
    public function saveStep(Request $request, string $type)
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        $step = (int) $request->input('step', 1);
        if ($step < 1 || $step > 4) {
            throw ValidationException::withMessages(['step' => 'Invalid application step.']);
        }

        $request->merge([
            'form_data' => is_string($request->input('form_data'))
                ? json_decode($request->input('form_data'), true)
                : $request->input('form_data'),
        ]);

        $data = $request->validate([
            'application_id' => ['nullable', 'integer', 'exists:account_applications,id'],
            'step' => ['required', 'integer', 'between:1,4'],
            'form_data' => ['required', 'array'],
            'form_data.*' => ['nullable'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $formData = $data['form_data'];
        $this->validateFormData($formData, $type, $step);

        $app = null;
        if (! empty($data['application_id'])) {
            $app = AccountApplication::whereKey($data['application_id'])
                ->where('account_type', $type)
                ->where('status', 'draft')
                ->firstOrFail();
            abort_unless(session()->get('account_application_'.$app->id) === $app->reference, 403);
        }

        if (! $app) {
            $app = new AccountApplication;
            $app->reference = 'HCML-'.strtoupper(Str::random(8));
            $app->account_type = $type;
            $app->status = 'draft';
            $app->form_data = [];
            $app->documents = [];
        }

        $existingData = $app->form_data ?? [];
        $mergedFormData = array_replace_recursive($existingData, $formData);
        foreach (['directors', 'administrators', 'joint_applicants'] as $peopleList) {
            if (array_key_exists($peopleList, $formData)) {
                $mergedFormData[$peopleList] = $formData[$peopleList];
            }
        }
        $app->form_data = $mergedFormData;
        $storedDocuments = $app->documents ?? [];

        foreach ($request->file('documents', []) as $key => $file) {
            if ($file) {
                $storedDocuments[$key] = $file->store('account-applications/'.$app->reference, 'local');
            }
        }
        $app->documents = $storedDocuments;
        $app->current_step = max($app->current_step, min(4, $step + 1));

        $form = $app->form_data;
        $individualName = trim(implode(' ', array_filter([
            $form['surname'] ?? '', $form['first_name'] ?? '', $form['other_names'] ?? '',
        ])));
        $app->applicant_name = match ($type) {
            'minor' => $form['minor_name'] ?? ($individualName ?: null),
            'corporate' => $form['company_name'] ?? null,
            'estate' => $form['estate_name'] ?? null,
            'joint' => $form['joint_account_name'] ?? null,
            default => $individualName ?: null,
        };
        $primaryPerson = $form['joint_applicants'][0] ?? $form['administrators'][0] ?? $form['directors'][0] ?? [];
        $app->email = $form['email'] ?? ($primaryPerson['email'] ?? null);
        $app->phone = $form['telephone'] ?? $form['joint_contact_number'] ?? ($primaryPerson['telephone'] ?? null);
        $app->save();

        session()->put('account_application_'.$app->id, $app->reference);
        session()->put('account_application_current_'.$type, $app->id);

        return back()
            ->with('success', 'Your progress has been saved securely.')
            ->with('accountApplication', [
                'id' => $app->id,
                'reference' => $app->reference,
                'current_step' => $app->current_step,
            ]);
    }

    public function submit(Request $request, string $type)
    {
        abort_unless(in_array($type, self::TYPES, true), 404);
        $data = $request->validate([
            'application_id' => ['required', 'integer', 'exists:account_applications,id'],
            'declaration_accepted' => ['accepted'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $app = AccountApplication::whereKey($data['application_id'])
            ->where('account_type', $type)
            ->where('status', 'draft')
            ->firstOrFail();
        abort_unless(session()->get('account_application_'.$app->id) === $app->reference, 403);

        // Type-specific required-field validation (per HCML-approved KYC requirements).
        $formData = $app->form_data ?? [];
        $missing = [];
        foreach ($this->requiredFields($type) as $field) {
            if (blank($formData[$field] ?? null)) {
                $missing[] = $field;
            }
        }
        if (! empty($missing)) {
            throw ValidationException::withMessages([
                'form_data' => 'Please complete the required fields: '.implode(', ', $missing),
            ]);
        }
        $this->validateFormData($formData, $type);

        $superAdminEmails = User::query()
            ->where('is_admin', true)
            ->where('is_super_admin', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('email')
            ->all();

        if ($superAdminEmails === []) {
            throw ValidationException::withMessages([
                'application_id' => 'This application cannot be submitted until a super administrator email is configured.',
            ]);
        }

        $documents = $app->documents ?? [];
        foreach ($request->file('documents', []) as $key => $file) {
            if ($file) {
                $documents[$key] = $file->store('account-applications/'.$app->reference, 'local');
            }
        }

        $app->documents = $documents;
        $app->status = 'submitted';
        $app->submitted_at = now();

        $pdfContent = Pdf::loadView('pdf.account-application', [
            'application' => $app,
        ])->setPaper('a4')->output();

        $pdfPath = $this->privatePdfPath($app);
        $this->storePrivatePdf($pdfPath, $pdfContent);
        $app->pdf_path = $pdfPath;
        $app->save();

        Mail::to($superAdminEmails)->queue(new AccountApplicationSubmitted(
            $app,
            $pdfContent,
            route('admin.account-applications.pdf', $app),
        ));

        session()->forget([
            'account_application_'.$app->id,
            'account_application_current_'.$type,
        ]);

        return redirect()->route('open-account.success', [
            'type' => $type,
            'reference' => $app->reference,
        ]);
    }

    public function downloadPdf(AccountApplication $application)
    {
        abort_unless(auth()->user()?->hasFeature('account_applications'), 403);
        abort_if($application->status === 'draft', 404);

        $path = $application->pdf_path;
        if (! $path || ! Storage::disk('local')->exists($path)) {
            $pdfContent = Pdf::loadView('pdf.account-application', [
                'application' => $application,
            ])->setPaper('a4')->output();
            $path = $this->privatePdfPath($application);
            $this->storePrivatePdf($path, $pdfContent);
            $application->forceFill(['pdf_path' => $path])->save();
        }

        return Storage::disk('local')->download(
            $path,
            $application->reference.'-account-application.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function downloadBlankForm(string $type)
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        $schema = AccountOpeningFormSchema::forType($type);

        return Pdf::loadView('pdf.account-opening-blank', [
            'schema' => $schema,
        ])->setPaper('a4')->download($type.'-account-opening-form.pdf');
    }

    /**
     * Required text fields per account type. Document upload requirements are
     * enforced separately at the KYC document-review stage.
     */
    private function requiredFields(string $type): array
    {
        return AccountOpeningFormSchema::forType($type)['requiredFields'];
    }

    private function currentDraft(string $type): ?AccountApplication
    {
        $id = session()->get('account_application_current_'.$type);
        if (! $id) {
            return null;
        }

        $application = AccountApplication::query()
            ->whereKey($id)
            ->where('account_type', $type)
            ->where('status', 'draft')
            ->first();

        if (! $application || session()->get('account_application_'.$application->id) !== $application->reference) {
            return null;
        }

        return $application;
    }

    private function validateFormData(array $formData, string $type, int $step = 4): void
    {
        $optionalRules = [
            'email' => ['email', 'max:190'],
            'next_of_kin_email' => ['email', 'max:190'],
            'date_of_birth' => ['date'],
            'deceased_date_of_birth' => ['date'],
            'date_of_registration' => ['date'],
            'account_opened_at' => ['date'],
            'id_expiry' => ['date'],
            'id_type' => ['in:NIN'],
            'bvn' => ['digits:11'],
            'bank_account_number' => ['digits:10'],
            'account_type' => ['in:Savings,Current'],
            'rc_number' => ['regex:/^(?:RC)?\d{1,8}$/i'],
        ];
        $rules = [];

        foreach ($optionalRules as $field => $fieldRules) {
            if (filled($formData[$field] ?? null)) {
                $rules[$field] = $fieldRules;
            }
        }

        if (in_array($type, ['individual', 'minor'], true)
            && ($formData['id_type'] ?? null) === 'NIN'
            && filled($formData['id_number'] ?? null)) {
            $rules['id_number'] = ['digits:11'];
        }

        $schema = AccountOpeningFormSchema::forType($type);
        $personList = $schema['peopleField'];
        $personStep = null;
        if ($personList !== null) {
            foreach ($schema['fieldsByStep'] as $index => $fields) {
                if (in_array($personList, $fields, true)) {
                    $personStep = $index + 1;
                    break;
                }
            }
        }
        $validatePeople = $personList !== null && $step >= $personStep;
        if ($validatePeople) {
            $rules[$personList] = ['required', 'array', 'min:'.$schema['peopleMinimum'], 'max:20'];
            $nestedFieldRules = [
                'name' => ['string', 'max:150'],
                'telephone' => ['string', 'max:40'],
                'email' => ['email', 'max:190'],
                'date_of_birth' => ['date'],
                'nationality' => ['string', 'max:80'],
                'residential_address' => ['string', 'max:1000'],
                'id_type' => ['in:NIN'],
                'id_number' => ['string', 'max:100'],
                'id_expiry' => ['date'],
            ];
            foreach ($schema['requiredPersonFields'] as $field) {
                $rules[$personList.'.*.'.$field] = [
                    'required',
                    ...$nestedFieldRules[$field],
                ];
            }
            $rules[$personList.'.*.next_of_kin_email'] = ['nullable', 'email', 'max:190'];
        }

        if ($rules) {
            validator($formData, $rules)->validate();
        }

        foreach ([
            'telephone', 'next_of_kin_telephone', 'guardian_telephone', 'joint_contact_number',
        ] as $field) {
            if (filled($formData[$field] ?? null) && ! $this->validTelephone($formData[$field])) {
                throw ValidationException::withMessages([
                    $field => 'Enter a valid telephone number, such as 08012345678 or +2348012345678.',
                ]);
            }
        }

        if ($validatePeople) {
            foreach ($formData[$personList] as $index => $person) {
                if (($person['id_type'] ?? null) === 'NIN'
                    && filled($person['id_number'] ?? null)
                    && ! preg_match('/^\d{11}$/', $person['id_number'])) {
                    throw ValidationException::withMessages([
                        "{$personList}.{$index}.id_number" => 'NIN must be exactly 11 digits.',
                    ]);
                }

                foreach (['telephone', 'next_of_kin_telephone'] as $field) {
                    if (filled($person[$field] ?? null) && ! $this->validTelephone($person[$field])) {
                        throw ValidationException::withMessages([
                            "{$personList}.{$index}.{$field}" => 'Enter a valid telephone number.',
                        ]);
                    }
                }
            }
        }
    }

    private function validTelephone(mixed $telephone): bool
    {
        if (! is_string($telephone)) {
            return false;
        }

        $normalized = preg_replace('/[\s()-]/', '', $telephone);

        return is_string($normalized)
            && preg_match('/^(?:0[789]\d{9}|\+234[789]\d{9}|\+[1-9]\d{7,14})$/', $normalized) === 1;
    }

    private function privatePdfPath(AccountApplication $application): string
    {
        return 'account-applications/'.$application->reference.'/completed-application.pdf';
    }

    private function storePrivatePdf(string $path, string $contents): void
    {
        if (! Storage::disk('local')->put($path, $contents)) {
            throw new RuntimeException('Unable to securely store the completed account application PDF.');
        }
    }

    public function success(Request $request, string $type)
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        return Inertia::render('AccountOpening/Submitted', [
            'reference' => $request->query('reference'),
            'accountType' => $type,
        ]);
    }
}
