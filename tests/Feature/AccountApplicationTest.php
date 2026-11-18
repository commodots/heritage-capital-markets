<?php

namespace Tests\Feature;

use App\Mail\AccountApplicationSubmitted;
use App\Models\AccountApplication;
use App\Models\User;
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
                    'first_name' => 'Ada',
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
                ->where('accountApplication.form_data.first_name', 'Ada')
                ->where('formSchema.requiredFieldsByStep.0.0', 'surname'));

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
        $this->assertSame('Ada', $application->form_data['first_name']);
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

        $this->assertDatabaseCount('account_applications', 0);
    }

    public function test_nested_people_have_telephone_and_nin_format_validation(): void
    {
        $person = [
            'name' => 'Ada Example',
            'residential_address' => '1 Example Street',
            'telephone' => '12345',
            'email' => 'person@example.com',
            'date_of_birth' => '1990-01-01',
            'nationality' => 'Nigerian',
            'id_type' => 'NIN',
            'id_number' => '12345678901',
            'id_expiry' => '2030-01-01',
        ];

        $this->from('/open-account/corporate')
            ->post('/open-account/corporate/save-step', [
                'step' => 1,
                'form_data' => json_encode(['directors' => [$person]]),
            ])
            ->assertRedirect('/open-account/corporate')
            ->assertSessionHasErrors('directors.0.telephone');

        $person['telephone'] = '08012345678';
        $person['id_number'] = '1234';
        $this->from('/open-account/corporate')
            ->post('/open-account/corporate/save-step', [
                'step' => 1,
                'form_data' => json_encode(['directors' => [$person]]),
            ])
            ->assertRedirect('/open-account/corporate')
            ->assertSessionHasErrors('directors.0.id_number');

        $this->assertDatabaseCount('account_applications', 0);
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
                'first_name' => 'Ada',
            ]),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $application = AccountApplication::query()->firstOrFail();

        $this->post('/open-account/individual/submit', [
            'application_id' => $application->id,
            'declaration_accepted' => '1',
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
            'id_type' => 'NIN',
            'id_number' => '12345678901',
            'id_expiry' => '2030-01-01',
        ];
        $common = [
            'telephone' => '08012345678',
            'email' => 'applicant@example.com',
            'residential_address' => '1 Example Street',
            'bank_name' => 'Example Bank',
            'bank_account_number' => '0123456789',
            'bank_account_name' => 'Example Applicant',
        ];

        $applications = [
            'individual' => $common + [
                'surname' => 'Example',
                'first_name' => 'Ada',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'Nigerian',
                'id_type' => 'NIN',
                'id_number' => '12345678901',
                'id_expiry' => '2030-01-01',
                'bvn' => '12345678901',
                'direct_cash_settlement' => 'no',
            ],
            'corporate' => $common + [
                'company_name' => 'Example Limited',
                'rc_number' => 'RC123456',
                'nature_of_business' => 'Investment services',
                'business_address' => '2 Example Street',
                'contact_address' => '2 Example Street',
                'directors' => [$person('Ada Example')],
                'bvn' => '12345678901',
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
                'minor_name' => 'Alex Example',
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
                'administrators' => [$person('Ada Example')],
            ],
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
            ])->assertRedirect();

            $this->assertDatabaseHas('account_applications', [
                'id' => $application->id,
                'status' => 'submitted',
            ]);

            if ($type === 'minor') {
                $this->assertSame('Alex Example', $application->fresh()->applicant_name);
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
        Mail::fake();
        $superAdmin = User::factory()->create(['is_admin' => true, 'is_super_admin' => true]);

        $formData = [
            'surname' => 'Example',
            'first_name' => 'Ada',
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
            'documents' => [
                'passport_photo' => UploadedFile::fake()->create('passport.jpg', 10, 'image/jpeg'),
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $application->refresh();
        $this->assertArrayHasKey('passport_photo', $application->documents);
        Storage::disk('local')->assertExists($application->documents['passport_photo']);

        Mail::assertQueued(AccountApplicationSubmitted::class, fn ($mail) => str_starts_with($mail->pdfContent, '%PDF-'));
        $this->actingAs($superAdmin)
            ->get("/admin/account-applications/{$application->id}/pdf")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
