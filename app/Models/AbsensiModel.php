<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AbsensiModel
 * Menangani data tabel `absensi` (kehadiran siswa per tanggal).
 * Gaya meniru SiswaModel.php yang sudah ada: polos, tanpa timestamp CI4.
 */
class AbsensiModel extends Model
{
    protected $table         = 'absensi';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['siswa_id', 'tanggal', 'status', 'keterangan'];

    // Timestamps dimatikan di level PHP karena tabel memakai
    // DEFAULT CURRENT_TIMESTAMP pada kolom `dibuat_pada` (lihat absensi.sql).
    protected $useTimestamps = false;
}
