<?php

namespace Tests\Feature;

use App\Models\AdminRole;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicDownloadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_downloads_page_lists_published_documents_and_account_forms(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->create('individual-account-form.pdf', 20, 'application/pdf')
            ->store('downloads', 'public');
        $document = Media::create([
            'name' => 'Individual Account Opening Form',
            'caption' => 'Complete and submit this form.',
            'file_path' => $path,
            'disk' => 'public',
            'mime_type' => 'application/pdf',
            'file_size' => 20,
        ]);

        $this->get('/resources/downloads')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Resources/Downloads', false)
                ->has('documents', 1)
                ->where('documents.0.id', $document->id)
                ->where('documents.0.name', 'Individual Account Opening Form'));

        $this->get("/resources/downloads/{$document->id}/file")
            ->assertDownload('individual-account-opening-form.pdf');

        $this->get('/open-account/individual')->assertOk();
        $this->get('/open-account/corporate')->assertOk();
        $this->get('/open-account/joint')->assertOk();
        $this->get('/open-account/minor')->assertOk();
        $this->get('/open-account/estate')->assertOk();

        $formFiles = [
            'individual' => 'HCML KYC Individual.pdf',
            'corporate' => 'HCML kyc corporate.pdf',
            'joint' => 'HCML kyc Joint account.pdf',
            'minor' => 'HCML kyc Minor Account.pdf',
            'estate' => 'HCML kyc ESTATE ACCOUNT.pdf',
        ];

        foreach ($formFiles as $type => $fileName) {
            $response = $this->get("/resources/downloads/account-opening/{$type}.pdf")
                ->assertDownload("{$type}-account-opening-form.pdf");

            $this->assertSame(
                realpath(public_path($fileName)),
                $response->baseResponse->getFile()->getPathname(),
            );
        }
    }

    public function test_only_superadmins_can_publish_download_documents(): void
    {
        Storage::fake('public');
        $role = AdminRole::create([
            'name' => 'Media Manager',
            'permissions' => ['dashboard', 'media'],
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_role_id' => $role->id,
        ]);

        $this->actingAs($admin)
            ->post('/admin/media/documents', [
                'name' => 'Account form',
                'file' => UploadedFile::fake()->create('account-form.pdf', 20, 'application/pdf'),
            ])
            ->assertForbidden();

        $superAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $this->actingAs($superAdmin)
            ->post('/admin/media/documents', [
                'name' => 'Account form',
                'file' => UploadedFile::fake()->create('account-form.pdf', 20, 'application/pdf'),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $document = Media::query()->where('name', 'Account form')->firstOrFail();
        Storage::disk('public')->assertExists($document->file_path);
        $this->assertDatabaseHas('media', [
            'name' => 'Account form',
            'disk' => 'public',
        ]);
    }

    public function test_only_document_files_are_served_from_the_public_download_route(): void
    {
        $image = Media::create([
            'name' => 'image',
            'file_path' => 'downloads/not-a-document.jpg',
            'disk' => 'public',
        ]);

        $this->get("/resources/downloads/{$image->id}/file")->assertNotFound();
    }
}
