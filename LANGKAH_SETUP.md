# Setup TaskMate (Laravel 11/12 + MySQL XAMPP)

## 1. Prasyarat
- XAMPP (PHP 8.2+ dan MySQL), Composer
- Ekstensi PHP aktif: pdo_mysql, mbstring, fileinfo, openssl

## 2. Buat proyek
    composer create-project laravel/laravel taskmate
    cd taskmate

## 3. Buat database
Start Apache dan MySQL di XAMPP, buka phpMyAdmin, buat database `taskmate`.

## 4. Ubah file .env
    APP_NAME=TaskMate
    APP_URL=http://localhost:8000
    APP_TIMEZONE=Asia/Makassar

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=taskmate
    DB_USERNAME=root
    DB_PASSWORD=

    SESSION_DRIVER=file
    SESSION_LIFETIME=120
    CACHE_STORE=file
    QUEUE_CONNECTION=sync

    MAIL_MAILER=log

## 5. Hapus file bawaan Laravel yang bentrok
- database/migrations/0001_01_01_000000_create_users_table.php
- database/factories/UserFactory.php

## 6. Salin isi folder overlay ini ke root proyek (timpa jika diminta)
Isinya: app/, bootstrap/app.php, database/, resources/views/, routes/.

## 7. Naikkan batas unggah di php.ini XAMPP (default hanya 2 MB)
    upload_max_filesize = 20M
    post_max_size = 25M
Restart Apache.

## 8. Migrasi dan jalankan
    php artisan migrate
    php artisan serve
Buka http://localhost:8000

## 9. Uji pengingat deadline
    php artisan taskmate:kirim-pengingat
    php artisan schedule:work
Email tercatat pada storage/logs/laravel.log (MAIL_MAILER=log).
Produksi: cron `* * * * * php /path/taskmate/artisan schedule:run`
