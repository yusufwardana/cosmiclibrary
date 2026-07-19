<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Loan Period (Days)
    |--------------------------------------------------------------------------
    | Durasi default peminjaman buku dalam hari.
    */
    'loan_period_days' => 7,

    /*
    |--------------------------------------------------------------------------
    | Max Extend Count
    |--------------------------------------------------------------------------
    | Jumlah maksimum perpanjangan yang diizinkan per peminjaman.
    */
    'max_extend_count' => 2,

    /*
    |--------------------------------------------------------------------------
    | Extend Days
    |--------------------------------------------------------------------------
    | Jumlah hari tambahan per perpanjangan.
    */
    'extend_days' => 7,

    /*
    |--------------------------------------------------------------------------
    | Overdue Fine Per Day
    |--------------------------------------------------------------------------
    | Nominal denda per hari keterlambatan (dalam Rupiah).
    */
    'overdue_fine_per_day' => 1000,

    /*
    |--------------------------------------------------------------------------
    | Reservation Hold Hours
    |--------------------------------------------------------------------------
    | Durasi maksimum reservasi ditahan sebelum kedaluwarsa (dalam jam).
    */
    'reservation_hold_hours' => 48,

    /*
    |--------------------------------------------------------------------------
    | Max Borrow Per Member
    |--------------------------------------------------------------------------
    | Jumlah maksimum buku yang dapat dipinjam oleh satu anggota secara bersamaan.
    */
    'max_borrow_per_member' => 3,

];
