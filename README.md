<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Account Opening

The Downloads page provides online applications and printable blank PDFs for individual, corporate, joint, minor, and estate accounts. These PDFs are generated from the same form schema used by the online forms and submission validation. Superadmins can publish additional approved PDF/DOC/DOCX documents from the Media Library.

Account-submission emails are encrypted and queued. Production must configure a real mail transport, set `QUEUE_CONNECTION=database` (or another persistent queue), and run a queue worker, for example:

```sh
php artisan queue:work --tries=5
```

The example environment uses the `log` mailer for local development; it does not deliver email externally. Email recipients are active superadmin users with a configured email address.

## Newsletter Campaigns

Admins with the Newsletter Subscribers permission can export subscriber status to CSV and send plain-text campaigns to subscribed addresses. Campaign delivery uses the configured queue and rechecks subscription status before sending. Campaign emails include a signed unsubscribe link that works without an account; unsubscribing immediately excludes the address from future campaigns, and only the subscriber can opt back in through the public signup form.

## Market Data

The public market ticker is shared by all public pages and displays active stock quotes and market indices. It only displays records stored through the admin tools; no demonstration or fallback prices are seeded. An empty market-data list is shown as unavailable.

Admins with the Market Indices permission can create and edit stock quotes under **Market Quotes** and manage indices under **Market Indices**. CSV uploads are validated and previewed before commit. Invalid rows are listed and skipped; repeated symbols/names are rejected, and successful, failed, and invalid rows are reported after commit.

Stock quote CSVs use the required header row `symbol,name,value,change,change_percent`; optional columns are `as_of,is_active,sort_order`. Symbols must already exist in Market Quotes, movements are signed numeric values, and `as_of` should be an ISO 8601 timestamp.

Market index CSVs use `name,value,change,change_percent`; `symbol,is_positive,is_active,sort_order,as_of` are optional. `change_value` is accepted instead of `change` for compatibility. Indices are matched by symbol or name, and a non-matching row creates a new index.

Market index API imports remain disabled unless HCML approves the integration and a valid HTTPS endpoint is configured through `MARKET_INDICES_API_URL` (set `MARKET_INDICES_API_APPROVED=true`; optionally configure `MARKET_INDICES_API_TOKEN`). Stock quote API integration is not enabled; HCML approval and an approved provider are required before one is connected.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
