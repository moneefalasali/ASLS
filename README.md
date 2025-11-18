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

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).



# تطبيق محول لغة الإشارة (PWA)

هذا المشروع عبارة عن تطبيق ويب تقدمي (PWA) يهدف إلى تحويل النصوص أو الصوت (العربية والإنجليزية) إلى تمثيل مرئي بلغة الإشارة.

## الميزات

### الواجهة الأمامية (Frontend)
*   **تقنيات**: HTML, CSS, JavaScript.
*   **إدخال النص**: منطقة إدخال نص تدعم اللغتين العربية والإنجليزية.
*   **إدخال الصوت**: خيار لتحميل ملفات صوتية (WAV/MP3).
*   **عرض لغة الإشارة**: يتم عرض الإخراج كسلسلة من الصور أو الرسوم المتحركة البسيطة (أيقونات تمثل الحروف أو الكلمات بلغة الإشارة).
*   **عناصر التحكم في التشغيل**: تشغيل، إيقاف مؤقت، تعديل السرعة، وعرض الترجمة المصاحبة.
*   **تطبيق ويب تقدمي (PWA)**: قابل للتثبيت مع ملف `manifest.json` وعامل خدمة `service-worker.js` لدعم العمل دون اتصال بالإنترنت.

### الواجهة الخلفية (Backend)
*   **تقنيات**: Laravel (PHP).
*   **نقاط نهاية API**:
    *   `/api/convert-text`: تستقبل نصًا وتعيد JSON بتسلسل الإشارة المترجمة.
    *   `/api/convert-audio`: تستقبل ملفًا صوتيًا، تقوم بتحويله إلى نص (عبر خدمة STT خارجية أو وهمية)، ثم تعيد تسلسل الإشارة المترجمة.
*   **إدارة الأصول**: يتم تخزين أصول لغة الإشارة (الصور، الرسوم المتحركة) في مجلد التخزين، مع جدول قاعدة بيانات `sign_assets` لإدارتها.
*   **واجهة إدارة CRUD**: واجهة إدارية بسيطة لتحميل وإدارة أصول لغة الإشارة (الحروف، الكلمات، الرسوم المتحركة).

## هيكل المشروع

```
sign-language-pwa/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/SignLanguageController.php
│   │   │   └── SignAssetController.php
│   │   └── Middleware/
│   └── Models/
│       └── SignAsset.php
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   │   └── 2025_10_04_235110_create_new_sign_assets_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── SignAssetSeeder.php
├── public/
│   ├── frontend/
│   │   ├── app.js
│   │   ├── icon-192.png
│   │   ├── icon-512.png
│   │   ├── index.html
│   │   ├── manifest.json
│   │   ├── service-worker.js
│   │   └── styles.css
│   └── index.php
├── resources/
│   ├── views/
│   │   ├── sign_assets/
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── index.blade.php
│   │   └── welcome.blade.php
├── routes/
│   ├── api.php
│   └── web.php
├── storage/
│   ├── app/
│   │   └── public/
│   │       └── signs/
│   │           ├── arabic/
│   │           └── english/
│   └── logs/
├── tests/
├── .env
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── phpunit.xml
├── README.md
├── server.php
└── vite.config.js
```

## الإعداد والتشغيل

1.  **استنساخ المستودع**:
    ```bash
    git clone <repository-url>
    cd sign-language-pwa
    ```

2.  **تثبيت تبعيات Composer**:
    ```bash
    composer install
    ```

3.  **تكوين ملف `.env`**:
    انسخ ملف `.env.example` إلى `.env`:
    ```bash
    cp .env.example .env
    ```
    ثم قم بإنشاء مفتاح التطبيق:
    ```bash
    php artisan key:generate
    ```
    تأكد من أن إعدادات قاعدة البيانات في ملف `.env` تشير إلى SQLite:
    ```
    DB_CONNECTION=sqlite
    #DB_HOST=127.0.0.1
    #DB_PORT=3306
    #DB_DATABASE=laravel
    #DB_USERNAME=root
    #DB_PASSWORD=
    ```

4.  **إنشاء قاعدة بيانات SQLite**:
    ```bash
    touch database/database.sqlite
    ```

5.  **تشغيل الترحيلات (Migrations)**:
    ```bash
    php artisan migrate
    ```

6.  **ربط مجلد التخزين العام (Storage Link)**:
    ```bash
    php artisan storage:link
    ```

7.  **تشغيل خادم Laravel**:
    ```bash
    php artisan serve
    ```
    يمكنك الوصول إلى الواجهة الأمامية عبر `http://127.0.0.1:8000/frontend/`.

## المشكلات المعروفة

واجهت مشكلة مستمرة في مرحلة بذر قاعدة البيانات (Seeding) لجدول `sign_assets`. على الرغم من أن ملف الترحيل `2025_10_04_235110_create_new_sign_assets_table.php` يحدد الأعمدة المطلوبة (`character`, `language`, `type`, `src`)، إلا أن عملية البذر تفشل باستمرار مع الخطأ `SQLSTATE[HY000]: General error: 1 no such table: sign_assets` أو `table sign_assets has no column named character`. هذا يشير إلى أن بنية الجدول لا يتم تطبيقها بشكل صحيح قبل محاولة إدخال البيانات، أو أن هناك مشكلة في كيفية تعرف Laravel على التغييرات في ملفات الترحيل.

**الحل البديل المقترح (لم يتم تنفيذه بالكامل في هذا التسليم بسبب المشكلة المستمرة)**:
يمكن إدخال البيانات يدويًا باستخدام أوامر SQL مباشرة بعد تشغيل الترحيلات، أو التحقق من إعدادات بيئة Laravel بشكل أعمق لضمان تطبيق الترحيلات بشكل صحيح.

## الأصول (Assets)

تم إنشاء مجلدات للأصول الإنجليزية والعربية في `storage/app/public/signs/english` و `storage/app/public/signs/arabic`. ومع ذلك، لم يتمكن النظام من بذر البيانات الأولية بنجاح بسبب المشكلة المذكورة أعلاه. يجب وضع صور لغة الإشارة (مثل `A.png`, `B.png` للحروف الإنجليزية و `ا.png`, `ب.png` للحروف العربية) في هذه المجلدات يدويًا لتتوافق مع المسارات المحددة في `SignAssetSeeder.php`.

## الخطوات التالية (للتطوير المستقبلي)

*   **حل مشكلة بذر قاعدة البيانات**: يجب حل المشكلة المستمرة في بذر البيانات لجدول `sign_assets` لتمكين إضافة الأصول تلقائيًا.
*   **إكمال واجهة إدارة CRUD**: تطوير واجهة المستخدم الكاملة لإدارة أصول لغة الإشارة (التحميل، التعديل، الحذف).
*   **تكامل STT**: دمج خدمة تحويل الكلام إلى نص (STT) حقيقية لنقطة نهاية `/api/convert-audio`.
*   **تحسين عرض لغة الإشارة**: دمج رسوم متحركة ثنائية الأبعاد أو ثلاثية الأبعاد لعرض إيماءات لغة الإشارة بشكل أكثر وضوحًا.
*   **دعم كلمات وجمل كاملة**: توسيع قاعدة البيانات ومنطق التحويل لدعم كلمات وجمل كاملة بدلاً من الحروف الفردية فقط.

--- 

**التاريخ**: 05 أكتوبر 2025

