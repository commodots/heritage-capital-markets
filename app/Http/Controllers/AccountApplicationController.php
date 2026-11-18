<?php

namespace App\Http\Controllers;

use App\Models\AccountApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\AccountApplicationSubmitted;

class AccountApplicationController extends Controller
{
    private const TYPES = ['individual', 'corporate', 'joint', 'minor', 'estate'];

    public function start(string $type)
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        $application = $this->currentDraft($type);

        return Inertia::render('AccountOpening/Start', [
            'accountType' => $type,
            'accountTypes' => self::TYPES,
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
        if (!empty($data['application_id'])) {
            $app = AccountApplication::whereKey($data['application_id'])
                ->where('account_type', $type)
                ->where('status', 'draft')
                ->firstOrFail();
            abort_unless(session()->get('account_application_'.$app->id) === $app->reference, 403);
        }

        if (!$app) {
            $app = new AccountApplication();
            $app->reference = 'HCML-'.strtoupper(Str::random(8));
            $app->account_type = $type;
            $app->status = 'draft';
            $app->form_data = [];
            $app->documents = [];
        }

        $existingData = $app->form_data ?? [];
        $app->form_data = array_replace_recursive($existingData, $formData);
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
        if (!empty($missing)) {
            throw ValidationException::withMessages([
                'form_data' => 'Please complete the required fields: ' . implode(', ', $missing),
            ]);
        }
        $this->validateFormData($formData, $type);

        $superAdminEmails = User::query()
            ->where('is_super_admin', true)
            ->whereNotNull('email')
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
        $app->save();
        $app->status = 'submitted';
        $app->submitted_at = now();

        $pdfContent = Pdf::loadView('pdf.account-application', [
            'application' => $app,
        ])->setPaper('a4')->output();

        Mail::to($superAdminEmails)->send(new AccountApplicationSubmitted(
            $app,
            $pdfContent,
            route('admin.account-applications.pdf', $app),
        ));

        $app->save();

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
        abort_unless(auth()->user()?->is_super_admin, 403);

        return Pdf::loadView('pdf.account-application', [
            'application' => $application,
        ])->setPaper('a4')->download($application->reference.'-account-application.pdf');
    }

    /**
     * Required text fields per account type. Document upload requirements are
     * enforced separately at the KYC document-review stage.
     */
    private function requiredFields(string $type): array
    {
        return match ($type) {
            'individual' => [
                'surname', 'first_name', 'date_of_birth', 'nationality', 'id_type',
                'id_number', 'id_expiry', 'telephone', 'email', 'residential_address',
                'bank_name', 'bank_account_number', 'bank_account_name', 'bvn',
                'direct_cash_settlement',
            ],
            'corporate' => [
                'company_name', 'rc_number', 'nature_of_business', 'business_address',
                'directors', 'telephone', 'email', 'contact_address',
                'bank_name', 'bank_account_number', 'bank_account_name', 'bvn',
            ],
            'joint' => [
                'joint_account_name', 'joint_address', 'joint_contact_number',
                'direct_cash_settlement', 'joint_applicants', 'bank_name',
                'bank_account_number', 'bank_account_name',
            ],
            'minor' => [
                'surname', 'date_of_birth', 'nationality', 'id_type', 'id_number', 'id_expiry',
                'telephone', 'email',
                'residential_address', 'guardian_name', 'guardian_relationship',
                'guardian_telephone', 'bank_name', 'bank_account_number', 'bank_account_name',
            ],
            'estate' => [
                'estate_name', 'deceased_date_of_birth', 'estate_address', 'administrators',
                'bank_name', 'bank_account_number', 'bank_account_name',
            ],
        };
    }

    private function currentDraft(string $type): ?AccountApplication
    {
        $id = session()->get('account_application_current_'.$type);
        if (!$id) {
            return null;
        }

        $application = AccountApplication::query()
            ->whereKey($id)
            ->where('account_type', $type)
            ->where('status', 'draft')
            ->first();

        if (!$application || session()->get('account_application_'.$application->id) !== $application->reference) {
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
            'bvn' => ['digits:11'],
            'bank_account_number' => ['digits:10'],
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

        $personList = match ($type) {
            'corporate' => 'directors',
            'estate' => 'administrators',
            'joint' => 'joint_applicants',
            default => null,
        };
        $personStep = in_array($type, ['joint', 'estate'], true) ? 2 : 1;
        $validatePeople = $personList !== null && $step >= $personStep;
        if ($validatePeople) {
            $rules[$personList] = ['required', 'array', 'min:'.($type === 'joint' ? 2 : 1), 'max:20'];
            $rules[$personList.'.*.name'] = ['required', 'string', 'max:150'];
            $rules[$personList.'.*.telephone'] = ['required', 'string', 'max:40'];
            $rules[$personList.'.*.email'] = ['required', 'email', 'max:190'];
            $rules[$personList.'.*.date_of_birth'] = ['required', 'date'];
            $rules[$personList.'.*.nationality'] = ['required', 'string', 'max:80'];
            $rules[$personList.'.*.residential_address'] = ['required', 'string', 'max:1000'];
            $rules[$personList.'.*.id_type'] = ['required', 'string', 'max:60'];
            $rules[$personList.'.*.id_number'] = ['required', 'string', 'max:100'];
            $rules[$personList.'.*.id_expiry'] = ['required', 'date'];
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

    public function success(Request $request, string $type)
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        return Inertia::render('AccountOpening/Submitted', [
            'reference' => $request->query('reference'),
            'accountType' => $type,
        ]);
    }
}
