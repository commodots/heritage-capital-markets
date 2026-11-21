# Heritage Capital Markets — Account Opening Module

Target branch: `feature/investor-login-changes`

## What this package does
- Adds a dedicated `account_applications` table and Eloquent model.
- Adds account-type routes/controllers for `individual`, `corporate`, `joint`, `minor`, and `estate`.
- Provides a step-based Inertia/Vue form for the five account types, document uploads, a submission confirmation, and admin review pages.
- Stores KYC documents on Laravel's private `local` disk, not the public Media Library.
- Adds a dedicated admin queue, filters, application detail, private download route and review status.

## Form mapping and compliance review
The five online schemas now include the account-entry fields and the Terms and Conditions, Anti-Money Laundering Act, E-Indemnity, and Risk Disclosure text from their corresponding source PDFs. The final step captures the declaration, signatory names/signatures/dates, and optional witness details; the generated application PDF includes these values and the disclosure text. The Individual and Minor forms allow an identification type and number, with the 11-digit rule applied only to NIN. The photograph area is handled through document upload, and the birth-certificate upload is labelled without an applicability qualifier.

The public form uses a responsive, step-based layout rather than reproducing the source PDFs page-for-page. The original PDFs remain available as downloads. HCML compliance/operations should verify the legal wording, document checklist, and required-field policy before production.

Before production:
1. Have HCML compliance/operations verify legal wording, the document checklist, and required-field policy against each complete source PDF.
2. Implement an expiring, signed/session-bound resume token. The current resume flow keeps the application reference in the browser session.
3. Confirm whether submission is simply a website enquiry for HCML review or requires integration to the account-opening/CSCS/back-office platform. No external integration is included.
4. Confirm retention policy, admin roles, audit logging, encryption/backups, and consent/privacy notice with HCML. Documents contain sensitive KYC details; restrict the database, storage, backups and logs accordingly.
5. Back up live files/database before deploying. Do not use `migrate:fresh` on production.

## Files and merge instructions
Copy files into matching paths, then merge the route snippets below into the existing `routes/web.php` (do not replace it entirely). `OpenAccount.vue` remains available as the existing short call-back/enquiry form while users can choose one of the new full-form links from that page or the site's CTA.

### Public routes (add near the existing Open Account routes)
```php
use App\\Http\\Controllers\\AccountApplicationController;
Route::get('/open-account/{type}', [AccountApplicationController::class, 'start'])->whereIn('type', ['individual','corporate','joint','minor','estate'])->name('account-application.start');
Route::post('/open-account/{type}/save-step', [AccountApplicationController::class, 'saveStep'])->whereIn('type', ['individual','corporate','joint','minor','estate'])->middleware('throttle:20,1')->name('account-application.save-step');
Route::post('/open-account/{type}/submit', [AccountApplicationController::class, 'submit'])->whereIn('type', ['individual','corporate','joint','minor','estate'])->middleware('throttle:5,1')->name('account-application.submit');
```

### Admin routes (inside the existing `/admin` group protected by `auth` and `admin`)
```php
use App\\Http\\Controllers\\Admin\\AccountApplicationAdminController;
Route::get('/account-applications', [AccountApplicationAdminController::class, 'index'])->name('admin.account-applications');
Route::get('/account-applications/{application}', [AccountApplicationAdminController::class, 'show'])->name('admin.account-applications.show');
Route::post('/account-applications/{application}', [AccountApplicationAdminController::class, 'update'])->name('admin.account-applications.update');
Route::get('/account-applications/{application}/documents/{document}', [AccountApplicationAdminController::class, 'download'])->name('admin.account-applications.documents.download');
```

The form schemas are maintained in `app/Support/AccountOpeningFormSchema.php`; submission validation and PDF generation are in `app/Http/Controllers/AccountApplicationController.php`. Do not assume every supporting-document requirement applies to each account type; obtain HCML compliance/operations approval for the account-type matrix.
