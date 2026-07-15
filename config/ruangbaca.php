<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Aturan peminjaman fisik
    |--------------------------------------------------------------------------
    |
    | Nilai default menjawab pertanyaan terbuka PRD §11 sampai dikonfigurasi ulang.
    | Ubah lewat .env tanpa mengubah kode.
    |
    */

    'loan_period_days' => (int) env('RUANGBACA_LOAN_PERIOD_DAYS', 7),

    'max_active_loans' => (int) env('RUANGBACA_MAX_ACTIVE_LOANS', 3),

    /*
    |--------------------------------------------------------------------------
    | Skema denda
    |--------------------------------------------------------------------------
    */

    'fine_per_day' => env('RUANGBACA_FINE_PER_DAY', '2000.00'),

    'max_fine' => env('RUANGBACA_MAX_FINE', '50000.00'),

];
