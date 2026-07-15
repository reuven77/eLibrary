<?php

namespace App\Exceptions;

use RuntimeException;

class LoanException extends RuntimeException
{
    public static function bukuTidakDapatDipinjam(): self
    {
        return new self('Buku ini tidak tersedia untuk peminjaman fisik. Gunakan opsi baca digital bila ada.');
    }

    public static function stokHabis(): self
    {
        return new self('Stok buku habis. Tidak dapat meminjam saat ini.');
    }

    public static function stokHabisSaatPersetujuan(): self
    {
        return new self('Stok buku habis. Persetujuan dibatalkan; pengajuan tetap menunggu atau ditolak manual.');
    }

    public static function batasPinjamanTercapai(int $max): self
    {
        return new self("Batas peminjaman aktif tercapai (maksimal {$max} buku).");
    }

    public static function sudahMeminjamBukuIni(): self
    {
        return new self('Anda masih memiliki peminjaman/pengajuan aktif untuk buku ini.');
    }

    public static function pinjamanTidakAktif(): self
    {
        return new self('Pinjaman ini sudah tidak aktif atau sudah dikembalikan.');
    }

    public static function pinjamanBukanMenunggu(): self
    {
        return new self('Pinjaman ini tidak dalam status menunggu persetujuan.');
    }

    public static function userTidakAktif(): self
    {
        return new self('Akun Anda dinonaktifkan dan tidak dapat mengajukan peminjaman.');
    }

    public static function bukuTidakDitemukan(): self
    {
        return new self('Buku tidak ditemukan.');
    }

    public static function pinjamanTidakDitemukan(): self
    {
        return new self('Data peminjaman tidak ditemukan.');
    }
}
