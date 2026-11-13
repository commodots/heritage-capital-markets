<?php

namespace App\Http\Controllers;

use App\Models\AccountApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

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
        $this->validateFormData($formData);

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
        $app->applicant_name = trim(implode(' ', array_filter([
            $form['surname'] ?? '', $form['first_name'] ?? '', $form['other_names'] ?? '',
        ]))) ?: (
            $form['company_name']
            ?? $form['estate_name']
            ?? $form['joint_account_name']
            ?? $form['minor_name']
            ?? null
        );
        $app->email = $form['email'] ?? null;
        $app->phone = $form['telephone'] ?? $form['phone'] ?? null;
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
        $this->validateFormData($formData);

        $documents = $app->documents ?? [];
        foreach ($request->file('documents', []) as $key => $file) {
            if ($file) {
                $documents[$key] = $file->store('account-applications/'.$app->reference, 'local');
            }
        }

        $app->documents = $documents;
        $app->status = 'submitted';
        $app->submitted_at = now();
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

    /**
     * Required text fields per account type. Document upload requirements are
     * enforced separately at the KYC document-review stage.
     */
    private function requiredFields(string $type): array
    {
        $common = [
            'telephone', 'email', 'residential_address',
            'bank_name', 'bank_account_number', 'bank_account_name',
        ];

        return match ($type) {
            'individual' => array_merge($common, [
                'surname', 'first_name', 'date_of_birth', 'nationality',
                'id_type', 'id_number', 'id_expiry',
            ]),
            'corporate' => array_merge($common, [
                'company_name', 'rc_number', 'nature_of_business',
            ]),
            'joint' => array_merge($common, [
                'joint_account_name', 'second_applicant_name', 'surname', 'first_name',
            ]),
            'minor' => array_merge($common, [
                'minor_name', 'guardian_name', 'guardian_relationship', 'date_of_birth',
            ]),
            'estate' => array_merge($common, [
                'estate_name', 'executor_name', 'probate_reference',
            ]),
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

    private function validateFormData(array $formData): void
    {
        $optionalRules = [
            'email' => ['email', 'max:190'],
            'telephone' => ['string', 'max:50'],
            'phone' => ['string', 'max:50'],
            'date_of_birth' => ['date'],
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

        if (($formData['id_type'] ?? null) === 'NIN' && filled($formData['id_number'] ?? null)) {
            $rules['id_number'] = ['digits:11'];
        }

        if ($rules) {
            validator($formData, $rules)->validate();
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
