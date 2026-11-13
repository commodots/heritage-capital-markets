<?php

namespace Tests\Feature;

use App\Models\AccountApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
        $this->assertSessionHas('account_application_current_individual', $application->id);

        $this->get('/open-account/individual')
            ->assertInertia(fn (Assert $page) => $page
                ->component('AccountOpening/Start')
                ->where('accountApplication.id', $application->id)
                ->where('accountApplication.reference', $application->reference)
                ->where('accountApplication.current_step', 2)
                ->where('accountApplication.form_data.first_name', 'Ada'));

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

    public function test_every_account_type_can_be_submitted_with_its_required_fields(): void
    {
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
            ],
            'corporate' => $common + [
                'company_name' => 'Example Limited',
                'rc_number' => 'RC123456',
                'nature_of_business' => 'Investment services',
            ],
            'joint' => $common + [
                'joint_account_name' => 'Ada and Sam Example',
                'second_applicant_name' => 'Sam Example',
                'surname' => 'Example',
                'first_name' => 'Ada',
            ],
            'minor' => $common + [
                'minor_name' => 'Alex Example',
                'guardian_name' => 'Ada Example',
                'guardian_relationship' => 'Parent',
                'date_of_birth' => '2015-01-01',
            ],
            'estate' => $common + [
                'estate_name' => 'Estate of Pat Example',
                'executor_name' => 'Ada Example',
                'probate_reference' => 'PROBATE-123',
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
    }

    public function test_documents_selected_on_the_final_step_are_saved_with_the_application(): void
    {
        Storage::fake('local');

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
                'passport_photo' => UploadedFile::fake()->image('passport.jpg'),
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $application->refresh();
        $this->assertArrayHasKey('passport_photo', $application->documents);
        Storage::disk('local')->assertExists($application->documents['passport_photo']);
    }
}
