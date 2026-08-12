<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Route utama aplikasi PresensiKita.
| Routes dipisah per domain untuk memudahkan pemeliharaan:
|   - auth.php  : Login & logout
|   - guru.php  : Halaman & fitur untuk role Guru
|   - admin.php : Halaman & fitur untuk role Admin
|
*/

require __DIR__ . '/auth.php';
require __DIR__ . '/guru.php';
require __DIR__ . '/admin.php';
