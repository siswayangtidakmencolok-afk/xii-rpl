<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\SiswaModel;

/**
 * Attendance
 * Controller sistem absensi siswa. Meniru gaya Siswa.php yang sudah ada:
 * - extends BaseController
 * - pakai view() biasa (bukan layout/template external)
 *
 * Route yang dipakai (lihat routes_snippet.php):
 *   GET  /attendance                  -> index()   (kalender)
 *   GET  /attendance/detail/{tanggal} -> detail()  (form ubah status)
 *   POST /attendance/simpan           -> simpan()  (simpan/ubah)
 *   GET  /attendance/hapus/{id}      -> hapus()   (opsional)
 */
class Attendance extends BaseController
{
    /**
     * Tampilkan kalender absensi untuk 1 siswa pada bulan/tahun tertentu.
     * Parameter via query string: ?siswa_id= &year= &month=
     */
    public function index()
    {
        $siswaModel   = new SiswaModel();
        $absensiModel = new AbsensiModel();

        // Ambil parameter (default: siswa pertama + bulan/tahun sekarang)
        $siswa_id = $this->request->getGet('siswa_id');
        $year     = $this->request->getGet('year');
        $month    = $this->request->getGet('month');

        $daftarSiswa = $siswaModel->orderBy('nama', 'ASC')->findAll();

        // Jika siswa_id kosong/tidak valid, pakai siswa pertama
        if (! $siswa_id || ! is_numeric($siswa_id)) {
            $siswa_id = $daftarSiswa ? $daftarSiswa[0]['id'] : null;
        }
        if (! $year)  { $year  = date('Y'); }
        if (! $month) { $month = date('n'); }

        // Ambil semua record absensi siswa terpilih di bulan/tahun tsb,
        // simpan sebagai map [ 'YYYY-MM-DD' => row ] supaya gampang dicocokkan.
        $absensi = [];
        if ($siswa_id) {
            $padMonth   = str_pad((int) $month, 2, '0', STR_PAD_LEFT);
            $firstDay   = "$year-$padMonth-01";
            $lastDayNum = date('t', strtotime($firstDay)); // jumlah hari di bulan tsb
            $lastDay    = "$year-$padMonth-$lastDayNum";

            $rows = $absensiModel
                ->where('siswa_id', $siswa_id)
                ->where('tanggal >=', $firstDay)
                ->where('tanggal <=', $lastDay)
                ->findAll();

            foreach ($rows as $r) {
                $absensi[$r['tanggal']] = $r;
            }
        }

        $data = [
            'daftarSiswa' => $daftarSiswa,
            'siswa_id'    => $siswa_id,
            'year'        => (int) $year,
            'month'       => (int) $month,
            'absensi'     => $absensi,
            'siswa'       => $siswa_id ? $siswaModel->find($siswa_id) : null,
        ];

        return view('attendance/kalender', $data);
    }

    /**
     * Detail absensi 1 siswa di 1 tanggal + form untuk ubah status.
     * ?siswa_id= wajib. Tanggal lewat URL segment.
     */
    public function detail($tanggal = null)
    {
        $siswaModel   = new SiswaModel();
        $absensiModel = new AbsensiModel();

        $siswa_id    = $this->request->getGet('siswa_id');
        $daftarSiswa = $siswaModel->orderBy('nama', 'ASC')->findAll();

        if (! $siswa_id || ! is_numeric($siswa_id)) {
            $siswa_id = $daftarSiswa ? $daftarSiswa[0]['id'] : null;
        }

        // Cari record yang sudah ada (siswa_id + tanggal unik)
        $absen = $absensiModel
            ->where('siswa_id', $siswa_id)
            ->where('tanggal', $tanggal)
            ->first();

        $data = [
            'daftarSiswa' => $daftarSiswa,
            'siswa_id'    => $siswa_id,
            'tanggal'     => $tanggal,
            'absen'       => $absen,
            'siswa'       => $siswa_id ? $siswaModel->find($siswa_id) : null,
        ];

        return view('attendance/detail', $data);
    }

    /**
     * Simpan atau ubah status absensi.
     * Pakai logika upsert manual (cek dulu ada/enggak) supaya UNIQUE KEY
     * unik_absen (siswa_id, tanggal) tidak dilewati.
     */
    public function simpan()
    {
        $absensiModel = new AbsensiModel();

        $siswa_id    = $this->request->getPost('siswa_id');
        $tanggal     = $this->request->getPost('tanggal');
        $status      = $this->request->getPost('status');
        $keterangan  = $this->request->getPost('keterangan');

        // Validasi sederhana
        $valid = ['hadir', 'sakit', 'izin', 'alpha'];
        if (! in_array($status, $valid, true)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $existing = $absensiModel
            ->where('siswa_id', $siswa_id)
            ->where('tanggal', $tanggal)
            ->first();

        if ($existing) {
            // Sudah ada -> update
            $absensiModel->update($existing['id'], [
                'status'     => $status,
                'keterangan' => $keterangan ?: null,
            ]);
        } else {
            // Belum ada -> insert
            $absensiModel->insert([
                'siswa_id'   => $siswa_id,
                'tanggal'    => $tanggal,
                'status'     => $status,
                'keterangan' => $keterangan ?: null,
            ]);
        }

        return redirect()->to('/attendance?siswa_id=' . $siswa_id);
    }

    /**
     * Hapus record absensi (opsional).
     */
    public function hapus($id = null)
    {
        $absensiModel = new AbsensiModel();
        $absensiModel->delete($id);
        return redirect()->back();
    }
}
