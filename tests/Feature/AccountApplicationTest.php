<?php

namespace Tests\Feature;

use App\Mail\AccountApplicationSubmitted;
use App\Models\AccountApplication;
use App\Models\AdminRole;
use App\Models\User;
use App\Support\AccountOpeningFormSchema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AccountApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_saved_application_is_returned_and_reused_when_resuming(): void
    {
        $this->from('/open-account/individual')
            ->post('/open-account/individual/save-step', [
                'step' => 1,
                'form_data' => json_encode([
                    'surname' => 'Example',
                    'other_names' => 'Ada',
                ]),
            ])
            ->assertRedirect('/open-account/individual')
            ->assertSessionHasNoErrors();

        $application = AccountApplication::query()->firstOrFail();
        $this->assertSame(
            $application->id,
            session('account_application_current_individual'),
        );

        $this->get('/open-account/individual')
            ->assertInertia(fn (Assert $page) => $page
                ->component('AccountOpening/Start', false)
                ->where('accountApplication.id', $application->id)
                ->where('accountApplication.reference', $application->reference)
                ->where('accountApplication.current_step', 2)
                ->where('accountApplication.form_data.other_names', 'Ada')
                ->where('formSchema.requiredFieldsByStep.0.0', 'surname')
                ->where('formSchema.legalDisclosure.termsConditions.title', 'Terms and Conditions')
                ->where('formSchema.legalDisclosure.riskDisclosure.title', 'Risk Disclosure Statement'));

        $this->from('/open-account/individual')
            ->post('/open-account/individual/save-step', [
                'application_id' => $application->id,
                'step' => 2,
                'form_data' => json_encode([
                    'telephone' => '08012345678',
                ]),
            ])
            ->assertRedirect('/open-account/individual')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('account_applications', 1);
        $application->refresh();
        $this->assertSame('Ada', $application->form_data['other_names']);
        $this->assertSame('08012345678', $application->form_data['telephone']);
    }

    public function test_invalid_email_is_rejected_by_the_server(): void
    {
        $this->from('/open-account/individual')
            ->post('/open-account/individual/save-step', [
                'step' => 1,
                'form_data' => json_encode([
                    'email' => 'not-an-email',
                ]),
            ])
            ->assertRedirect('/open-account/individual')
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('account_applications', 0);
    }

    public function test_invalid_telephone_nin_and_bvn_formats_are_rejected(): void
    {
        $this->from('/open-account/individual')
            ->post('/open-account/individual/save-step', [
                'step' => 1,
                'form_data' => json_encode(['telephone' => '12345']),
            ])
            ->assertRedirect('/open-account/individual')
            ->assertSessionHasErrors('telephone');

        $this->from('/open-account/individual')
            ->post('/open-account/individual/save-step', [
                'step' => 1,
                'form_data' => json_encode([
                    'id_type' => 'NIN',
                    'id_number' => '1234',
                    'bvn' => '1234',
                ]),
            ])
            ->assertRedirect('/open-account/individual')
            ->assertSessionHasErrors(['id_number', 'bvn']);

        $this->from('/open-account/individual')
            ->post('/open-account/individual/save-step', [
                'step' => 1,
                'form_data' => json_encode([
                    'id_type' => 'International Passport',
                    'id_number' => 'AB1234567',
                ]),
            ])
            ->assertRedirect('/open-account/individual')
            ->assertSessionHasNoErrors();

        $this->from('/open-account/individual')
            ->post('/open-account/individual/save-step', [
                'step' => 1,
                'form_data' => json_encode(['id_type' => 'Invalid ID']),
            ])
            ->assertRedirect('/open-account/individual')
            ->assertSessionHasErrors('id_type');

        $this->assertDatabaseCount('account_applications', 1);
    }

    public function test_bank_account_and_company_registration_identifiers_are_validated(): void
    {
        $this->from('/open-account/individual')
            ->post('/open-account/individual/save-step', [
                'step' => 3,
                'form_data' => json_encode([
                    'bank_account_number' => '12345',
                    'account_type' => 'Savings',
                    'bvn' => '12345678901',
                ]),
            ])
            ->assertRedirect('/open-account/individual')
            ->assertSessionHasErrors('bank_account_number');

        $this->from('/open-account/corporate')
            ->post('/open-account/corporate/save-step', [
                'step' => 1,
                'form_data' => json_encode(['rc_number' => 'not-an-rc']),
            ])
            ->assertRedirect('/open-account/corporate')
            ->assertSessionHasErrors('rc_number');

        $this->assertDatabaseCount('account_applications', 0);
    }

    public function test_submission_requires_identification_number_and_bvn(): void
    {
        $application = AccountApplication::create([
            'reference' => 'HCML-TEST1234',
            'account_type' => 'individual',
            'status' => 'draft',
            'form_data' => [
                'surname' => 'Example',
                'other_names' => 'Ada',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'Nigerian',
                'id_type' => 'NIN',
                'id_number' => '12345678901',
                'id_expiry' => '2030-01-01',
                'telephone' => '08012345678',
                'email' => 'applicant@example.com',
                'residential_address' => '1 Example Street',
                'bank_name' => 'Example Bank',
                'bank_account_number' => '0123456789',
                'bank_account_name' => 'Ada Example',
                'account_type' => 'Savings',
                'direct_cash_settlement' => 'no',
            ],
        ]);
        session()->put('account_application_'.$application->id, $application->reference);

        $this->post('/open-account/individual/submit', [
            'application_id' => $application->id,
            'declaration_accepted' => '1',
            'legal_signatories' => [$this->legalSignatory('Ada Example')],
            'indemnity_date' => '2025-01-01',
        ])->assertRedirect()->assertSessionHasErrors('form_data');

        $this->assertSame('draft', $application->fresh()->status);

        $formData = $application->fresh()->form_data;
        $formData['bvn'] = '12345678901';
        unset($formData['id_number']);
        $application->update(['form_data' => $formData]);

        $this->post('/open-account/individual/submit', [
            'application_id' => $application->id,
            'declaration_accepted' => '1',
            'legal_signatories' => [$this->legalSignatory('Ada Example')],
            'indemnity_date' => '2025-01-01',
        ])->assertRedirect()->assertSessionHasErrors('form_data');
    }

    public function test_submission_requires_legal_disclosure_signatures_and_dates(): void
    {
        $application = AccountApplication::create([
            'reference' => 'HCML-LEGAL123',
            'account_type' => 'individual',
            'status' => 'draft',
            'form_data' => [],
        ]);
        session()->put('account_application_'.$application->id, $application->reference);

        $this->post('/open-account/individual/submit', [
            'application_id' => $application->id,
            'declaration_accepted' => '1',
            'legal_signatories' => [],
        ])->assertRedirect()->assertSessionHasErrors(['legal_signatories', 'indemnity_date']);

        $this->assertSame('draft', $application->fresh()->status);
    }

    public function test_nested_people_have_telephone_validation_and_match_the_form_fields(): void
    {
        $person = [
            'name' => 'Ada Example',
            'residential_address' => '1 Example Street',
            'telephone' => '12345',
            'email' => 'person@example.com',
            'date_of_birth' => '1990-01-01',
            'nationality' => 'Nigerian',
            'state' => 'Lagos',
        ];

        $this->from('/open-account/corporate')
            ->post('/open-account/corporate/save-step', [
                'step' => 2,
                'form_data' => json_encode(['directors' => [$person]]),
            ])
            ->assertRedirect('/open-account/corporate')
            ->assertSessionHasErrors('directors.0.telephone');

        $person['telephone'] = '08012345678';
        $this->from('/open-account/corporate')
            ->post('/open-account/corporate/save-step', [
                'step' => 2,
                'form_data' => json_encode(['directors' => [$person]]),
            ])
            ->assertRedirect('/open-account/corporate')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('account_applications', 1);
    }

    public function test_submission_is_rejected_when_required_application_details_are_missing(): void
    {
        Mail::fake();
        User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $this->post('/open-account/individual/save-step', [
            'step' => 1,
            'form_data' => json_encode([
                'surname' => 'Example',
                'other_names' => 'Ada',
            ]),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $application = AccountApplication::query()->firstOrFail();

        $this->post('/open-account/individual/submit', [
            'application_id' => $application->id,
            'declaration_accepted' => '1',
            'legal_signatories' => [$this->legalSignatory('Ada Example')],
            'indemnity_date' => '2025-01-01',
        ])->assertRedirect()->assertSessionHasErrors('form_data');

        $this->assertDatabaseHas('account_applications', [
            'id' => $application->id,
            'status' => 'draft',
        ]);
        Mail::assertNothingSent();
    }

    public function test_every_account_type_can_be_submitted_with_its_required_fields(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);
        Storage::fake('local');
        Storage::fake('public');
        Mail::fake();
        $superAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
            'email' => 'superadmin@example.com',
        ]);

        $person = fn (string $name) => [
            'name' => $name,
            'residential_address' => '1 Example Street',
            'telephone' => '08012345678',
            'email' => 'person@example.com',
            'date_of_birth' => '1990-01-01',
            'nationality' => 'Nigerian',
        ];
        $common = [
            'telephone' => '08012345678',
            'email' => 'applicant@example.com',
            'residential_address' => '1 Example Street',
            'bank_name' => 'Example Bank',
            'bank_account_number' => '0123456789',
            'bank_account_name' => 'Example Applicant',
            'account_type' => 'Savings',
            'bvn' => '12345678901',
        ];

        $applications = [
            'individual' => $common + [
                'surname' => 'Example',
                'other_names' => 'Ada',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'Nigerian',
                'id_type' => 'International Passport',
                'id_number' => 'P1234567',
                'id_expiry' => '2030-01-01',
                'direct_cash_settlement' => 'no',
            ],
            'corporate' => $common + [
                'company_name' => 'Example Limited',
                'rc_number' => 'RC123456',
                'business_address' => '2 Example Street',
                'file_number' => 'FILE-001',
                'directors' => [$person('Ada Example')],
            ],
            'joint' => $common + [
                'joint_account_name' => 'Ada and Sam Example',
                'joint_address' => '3 Example Street',
                'joint_contact_number' => '08012345678',
                'direct_cash_settlement' => 'no',
                'joint_applicants' => [$person('Ada Example'), $person('Sam Example')],
            ],
            'minor' => $common + [
                'surname' => 'Example',
                'other_names' => 'Alex',
                'guardian_name' => 'Ada Example',
                'guardian_relationship' => 'Parent',
                'guardian_telephone' => '08012345678',
                'date_of_birth' => '2015-01-01',
                'nationality' => 'Nigerian',
                'residential_address' => '4 Example Street',
                'id_type' => 'NIN',
                'id_number' => '12345678901',
                'id_expiry' => '2030-01-01',
            ],
            'estate' => $common + [
                'estate_name' => 'Estate of Pat Example',
                'deceased_date_of_birth' => '1950-01-01',
                'estate_address' => '5 Example Street',
                'administrators' => [$person('Ada Example'), $person('Sam Example')],
            ],
        ];
        $signersByType = [
            'individual' => ['Ada Example'],
            'corporate' => ['Ada Example'],
            'joint' => ['Ada Example', 'Sam Example'],
            'minor' => ['Ada Example'],
            'estate' => ['Ada Example', 'Sam Example'],
        ];

        foreach ($applications as $type => $formData) {
            $this->post("/open-account/{$type}/save-step", [
                'step' => 3,
                'form_data' => json_encode($formData),
            ])->assertRedirect()->assertSessionHasNoErrors();

            $application = AccountApplication::query()
                ->where('account_type', $type)
                ->latest('id')
                ->firstOrFail();

            $this->post("/open-account/{$type}/submit", [
                'application_id' => $application->id,
                'declaration_accepted' => '1',
                'legal_signatories' => array_map(
                    fn (string $name) => $this->legalSignatory($name),
                    $signersByType[$type],
                ),
                'indemnity_date' => '2025-01-01',
            ])->assertRedirect();

            $this->assertDatabaseHas('account_applications', [
                'id' => $application->id,
                'status' => 'submitted',
            ]);
            $application->refresh();
            $this->assertNotNull($application->pdf_path);
            $this->assertTrue($application->form_data['declaration_accepted']);
            $this->assertCount(count($signersByType[$type]), $application->form_data['legal_signatories']);
            $this->assertSame('Ada Example', $application->form_data['legal_signatories'][0]['terms_name']);
            $this->assertSame('2025-01-01', $application->form_data['indemnity_date']);
            $schema = AccountOpeningFormSchema::forType($type);
            $pdfHtml = view('pdf.account-application', [
                'application' => $application,
                'fieldLabels' => $schema['fieldLabels'],
                'personFieldLabels' => $schema['personFieldLabels'],
                'legalDisclosure' => $schema['legalDisclosure'],
            ])->render();
            $this->assertStringContainsString('Terms and Conditions', $pdfHtml);
            $this->assertStringContainsString('Anti-Money Laundering Act', $pdfHtml);
            $this->assertStringContainsString('Risk Disclosure Statement', $pdfHtml);
            $this->assertStringContainsString('Conditions Accepted', $pdfHtml);
            $this->assertStringContainsString('Terms and Conditions Signature', $pdfHtml);
            Storage::disk('local')->assertExists($application->pdf_path);
            Storage::disk('public')->assertMissing($application->pdf_path);

            if ($type === 'minor') {
                $this->assertSame('Example Alex', $application->applicant_name);
            }
            if ($type === 'estate') {
                $this->assertCount(2, $application->form_data['administrators']);
                $this->assertSame('Sam Example', $application->form_data['administrators'][1]['name']);
            }
        }

        Mail::assertQueued(AccountApplicationSubmitted::class, 5);
        Mail::assertQueued(AccountApplicationSubmitted::class, function ($mail) use ($superAdmin) {
            return $mail->hasTo($superAdmin->email)
                && str_starts_with($mail->pdfContent, '%PDF-');
        });
    }

    public function test_documents_selected_on_the_final_step_are_saved_with_the_application(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Mail::fake();
        User::factory()->create(['is_admin' => true, 'is_super_admin' => true]);

        $formData = [
            'surname' => 'Example',
            'other_names' => 'Ada',
            'date_of_birth' => '1990-01-01',
            'nationality' => 'Nigerian',
            'id_type' => 'NIN',
            'id_number' => '12345678901',
            'id_expiry' => '2030-01-01',
            'telephone' => '08012345678',
            'email' => 'applicant@example.com',
            'residential_address' => '1 Example Street',
            'bank_name' => 'Example Bank',
            'bank_account_number' => '0123456789',
            'bank_account_name' => 'Ada Example',
            'bvn' => '12345678901',
            'account_type' => 'Savings',
            'direct_cash_settlement' => 'no',
        ];

        $this->post('/open-account/individual/save-step', [
            'step' => 3,
            'form_data' => json_encode($formData),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $application = AccountApplication::query()->firstOrFail();

        $this->post('/open-account/individual/submit', [
            'application_id' => $application->id,
            'declaration_accepted' => '1',
            'legal_signatories' => [$this->legalSignatory('Ada Example')],
            'indemnity_date' => '2025-01-01',
            'documents' => [
                'passport_photo' => UploadedFile::fake()->create('passport.jpg', 10, 'image/jpeg'),
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $application->refresh();
        $this->assertTrue($application->form_data['declaration_accepted']);
        $this->assertArrayHasKey('passport_photo', $application->documents);
        Storage::disk('local')->assertExists($application->documents['passport_photo']);
        $this->assertNotNull($application->pdf_path);
        Storage::disk('local')->assertExists($application->pdf_path);
        Storage::disk('public')->assertMissing($application->pdf_path);

        Mail::assertQueued(AccountApplicationSubmitted::class, fn ($mail) => str_starts_with($mail->pdfContent, '%PDF-'));

        $role = AdminRole::create([
            'name' => 'Account Reviewer',
            'permissions' => ['account_applications'],
            'is_active' => true,
        ]);
        $reviewer = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => false,
            'admin_role_id' => $role->id,
        ]);

        $legacyFormData = $application->form_data;
        unset($legacyFormData['declaration_accepted']);
        $application->update(['form_data' => $legacyFormData]);
        Storage::disk('local')->put($application->pdf_path, 'stale PDF');

        $this->actingAs($reviewer)
            ->get("/admin/account-applications/{$application->id}/pdf")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $application->refresh();
        $this->assertTrue($application->form_data['declaration_accepted']);
        $this->assertStringStartsWith('%PDF-', Storage::disk('local')->get($application->pdf_path));

        $unrelatedRole = AdminRole::create([
            'name' => 'Content Reviewer',
            'permissions' => ['pages'],
            'is_active' => true,
        ]);
        $unrelatedAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => false,
            'admin_role_id' => $unrelatedRole->id,
        ]);

        $this->actingAs($unrelatedAdmin)
            ->get("/admin/account-applications/{$application->id}/pdf")
            ->assertForbidden();
    }

    public function test_investor_login_redirects_to_the_approved_integra_portal(): void
    {
        $this->get('/investor-login')
            ->assertRedirect('https://heritagecapital.xcloudintegra.com/LoginInnovationbedai.aspx?ReturnUrl=%2f');
    }

    private function legalSignatory(string $name): array
    {
        return [
            'terms_name' => $name,
            'terms_signature' => $name,
            'terms_date' => '2025-01-01',
            'aml_name' => $name,
            'aml_signature' => $name,
            'aml_date' => '2025-01-01',
            'indemnity_name' => $name,
            'indemnity_signature' => $name,
        ];
    }
}
