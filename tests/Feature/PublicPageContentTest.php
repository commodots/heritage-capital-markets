<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicPageContentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'admin_role_id' => AdminRole::query()
                ->where('name', 'Legacy Administrator')
                ->value('id'),
        ]);
    }

    public function test_admin_can_update_public_page_content(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/admin/page-content/board', [
                'board_title' => 'Our Board and Management',
                'board_member_1_name' => 'Amina Example',
                'board_member_1_role' => 'Board Chair',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('site_settings', [
            'key' => 'board_title',
            'value' => 'Our Board and Management',
        ]);
        $this->get('/board-management')
            ->assertInertia(fn (Assert $page) => $page
                ->component('BoardManagement')
                ->where('content.board_title', 'Our Board and Management')
                ->where('content.board_intro', 'The people of Heritage bring together experience, market knowledge and a commitment to serving our clients.')
                ->where('content.board_member_1_name', 'Amina Example')
                ->where('content.board_member_1_role', 'Board Chair'));
    }

    public function test_admin_can_manage_management_team_profiles_separately(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin/page-content/board')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('group.fields.management_member_1_name.0', '')
                ->where('group.fields.management_member_1_role.0', ''));

        $this->actingAs($admin)
            ->post('/admin/page-content/board', [
                'management_member_1_name' => 'Tunde Example',
                'management_member_1_role' => 'Chief Executive Officer',
                'management_member_1_bio' => 'Leads the management team.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('site_settings', [
            'key' => 'management_member_1_name',
            'value' => 'Tunde Example',
        ]);

        $this->get('/board-management')
            ->assertInertia(fn (Assert $page) => $page
                ->where('content.management_member_1_name', 'Tunde Example')
                ->where('content.management_member_1_role', 'Chief Executive Officer')
                ->where('content.management_member_1_bio', 'Leads the management team.'));
    }

    public function test_non_admin_cannot_edit_public_page_content(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->post('/admin/page-content/board', [
                'board_title' => 'Unauthorized edit',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('site_settings', [
            'key' => 'board_title',
            'value' => 'Unauthorized edit',
        ]);
    }

    public function test_admin_can_upload_a_board_member_photo_and_public_page_displays_it(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/admin/page-content/board', [
                'board_member_1_name' => 'Amina Example',
                'board_member_1_image' => UploadedFile::fake()->createWithContent(
                    'amina.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/r1sAAAAASUVORK5CYII='),
                ),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $photoPath = SiteSetting::where('key', 'board_member_1_image')->value('value');
        $this->assertNotEmpty($photoPath);
        Storage::disk('public')->assertExists($photoPath);

        $this->get('/board-management')
            ->assertInertia(fn (Assert $page) => $page
                ->component('BoardManagement')
                ->where('content.board_member_1_image', $photoPath));
    }

    public function test_clearing_a_board_member_profile_removes_the_photo_and_stored_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/admin/page-content/board', [
                'board_member_1_name' => 'Amina Example',
                'board_member_1_role' => 'Board Chair',
                'board_member_1_bio' => 'A board biography.',
                'board_member_1_image' => UploadedFile::fake()->createWithContent(
                    'amina.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/r1sAAAAASUVORK5CYII='),
                ),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $photoPath = SiteSetting::where('key', 'board_member_1_image')->value('value');
        Storage::disk('public')->assertExists($photoPath);

        $this->actingAs($admin)
            ->post('/admin/page-content/board', [
                'board_member_1_name' => '',
                'board_member_1_role' => '',
                'board_member_1_bio' => '',
                'board_member_1_image' => null,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('site_settings', [
            'key' => 'board_member_1_image',
            'value' => '',
        ]);
        Storage::disk('public')->assertMissing($photoPath);

        $this->get('/board-management')
            ->assertInertia(fn (Assert $page) => $page
                ->where('content.board_member_1_image', ''));
    }

    public function test_admin_cannot_upload_unsupported_board_member_file_type(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from('/admin/page-content/board')
            ->post('/admin/page-content/board', [
                'board_member_1_image' => UploadedFile::fake()->create('profile.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect('/admin/page-content/board')
            ->assertSessionHasErrors('board_member_1_image');

        $this->assertDatabaseMissing('site_settings', [
            'key' => 'board_member_1_image',
        ]);
    }

    public function test_admin_has_separate_content_edit_pages(): void
    {
        $admin = $this->admin();

        foreach ([
            'board' => 'Admin/PublicPageContent/BoardManagement',
            'governance' => 'Admin/PublicPageContent/CorporateGovernance',
            'fees' => 'Admin/PublicPageContent/FeesCharges',
            'resources' => 'Admin/PublicPageContent/Resources',
            'privacy' => 'Admin/PublicPageContent/PrivacyPolicy',
            'terms' => 'Admin/PublicPageContent/TermsOfUse',
            'risk' => 'Admin/PublicPageContent/RiskDisclosure',
        ] as $group => $component) {
            $this->actingAs($admin)
                ->get("/admin/page-content/{$group}")
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page
                    ->component($component)
                    ->where('groupKey', $group));
        }
    }

    public function test_editing_one_admin_page_does_not_overwrite_another_page(): void
    {
        SiteSetting::create([
            'key' => 'board_title',
            'value' => 'Existing Board Title',
        ]);

        $this->actingAs($this->admin())
            ->post('/admin/page-content/fees', [
                'fees_title' => 'Updated Fees',
                'board_title' => 'Should Not Be Saved',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('site_settings', [
            'key' => 'fees_title',
            'value' => 'Updated Fees',
        ]);
        $this->assertDatabaseHas('site_settings', [
            'key' => 'board_title',
            'value' => 'Existing Board Title',
        ]);
    }

    public function test_legal_pages_can_be_edited_separately_and_render_their_content(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/admin/page-content/privacy', [
                'privacy_intro' => 'Our approved privacy statement.',
                'privacy_section_1_title' => 'Information collected',
                'privacy_section_1_body' => 'We collect information needed to respond to enquiries.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('site_settings', [
            'key' => 'privacy_intro',
            'value' => 'Our approved privacy statement.',
        ]);

        $this->get('/privacy-policy')
            ->assertInertia(fn (Assert $page) => $page
                ->component('LegalPage')
                ->where('pageKey', 'privacy')
                ->where('content.privacy_intro', 'Our approved privacy statement.')
                ->where('content.privacy_section_1_body', 'We collect information needed to respond to enquiries.'));

        $this->get('/terms-of-use')
            ->assertInertia(fn (Assert $page) => $page
                ->component('LegalPage')
                ->where('pageKey', 'terms'));

        $this->get('/risk-disclosure')
            ->assertInertia(fn (Assert $page) => $page
                ->component('LegalPage')
                ->where('pageKey', 'risk'));
    }

    public function test_public_resources_page_receives_editable_copy_and_market_data(): void
    {
        SiteSetting::create([
            'key' => 'resources_title',
            'value' => 'Investor Learning Centre',
        ]);

        $this->get('/resources')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Resources/Index')
                ->where('content.resources_title', 'Investor Learning Centre')
                ->has('markets'));
    }
}
