# Heritage Capital Markets — Account Opening Module (Initial Integration Package)

Target branch: `feature/investor-login-changes`

## What this package does
- Adds a dedicated `account_applications` table and Eloquent model.
- Adds account-type routes/controllers for `individual`, `corporate`, `joint`, `minor`, and `estate`.
- Provides a step-based Inertia/Vue form with preliminary individual fields, document uploads, a submission confirmation, and admin review pages.
- Stores KYC documents on Laravel's private `local` disk, not the public Media Library.
- Adds a dedicated admin queue, filters, application detail, private download route and review status.

## Important: initial scaffold, not production-ready compliance sign-off
The public legacy pages are client-rendered and browser-readable web extraction exposes the Individual flow much more clearly than the other four. The individual page shows Personal Details, Next of Kin Details and Bank Details plus ID type/number/expiry, residential address, passport photo, birth certificate, utility bill and signature. The provided Vue view uses an initial 4-step redesign so the user can follow progress. The exact Corporate, Joint, Minor, Estate fields, all mandatory flags, permitted file sizes/types, and declaration text need review against the full legacy forms and HCML-approved KYC requirements.

Before production:
1. Inspect the actual rendered pages (all steps) and map every field and validation rule for all 5 account types.
2. Add type-specific validation in `AccountApplicationController`; current save validation only validates payload structure/file MIME and the submit route checks the declaration. Do not launch without full required-field validation.
3. Implement a real resume flow and expiring signed/session-bound resume token. The included scaffolding only keeps the application reference in the current browser session.
4. Confirm whether submission is simply a website enquiry for HCML review or requires integration to the account-opening/CSCS/back-office platform. No external integration is included.
5. Confirm retention policy, admin roles, audit logging, encryption/backups, and consent/privacy notice with HCML. Documents contain sensitive KYC details; restrict the database, storage, backups and logs accordingly.
6. Add automated feature tests; build frontend locally with `npm run build`.
7. Back up live files/database before deploying. Do not use `migrate:fresh` on production.

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

## Current field basis
Individual page evidence located during review:
- Personal Details: surname, first name, other names, gender, date of birth, mother's maiden name, telephone, occupation, email, nationality, state of origin, LGA, ID type, ID number, expiry date, residential address, passport photograph, birth certificate, valid utility bill, signature.
- Next of Kin Details.
- Bank Details: bank name, account number, account name, account type; legacy PDF also mentions CHN and BVN and settlement preference.

Do not assume every legacy field applies to each account type. Obtain HCML compliance/operations approval for the account-type matrix.
