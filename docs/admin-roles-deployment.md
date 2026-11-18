# Admin roles deployment

1. Take and verify a restorable backup of the production database before deployment.
2. Deploy the application code and run the normal production migration command: `php artisan migrate --force`.
3. The roles migration assigns existing active administrators to a Legacy Administrator user type, preserving their current module access. It does not promote every administrator to Super Admin.
4. An authorized operator must identify the approved primary administrator and mark that account as Super Admin before using User Types or Administrator Users. No account is selected automatically.

Do not use a database reset or fresh-migration command against production data.

Administrators are created with a temporary password. A Super Admin can set a new temporary password from the administrator's edit form; deliver it through an approved secure channel. No invitation or password-reset email is sent.
