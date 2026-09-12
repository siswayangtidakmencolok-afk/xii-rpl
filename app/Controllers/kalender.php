<?php
/*
  View: attendance/kalender.php
  Kalender absensi 1 siswa untuk 1 bulan. Self-contained (CSS inline).
  Data dari controller Attendance::index():
    $daftarSiswa, $siswa_id, $year, $month, $absensi (map tanggal=>row), $siswa
*/
$namaBulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Nama hari pendek, index = date('N') (1=Senin .. 7=Minggu)
$hariPendek = [1=>'Sen', 2=>'Sel', 3=>'Rab', 4=>'Kam', 5=>'Jum', 6=>'Sab', 7=>'Min'];

// Hitung sel kalender
$padMonth   = str_pad($month, 2, '0', STR_PAD_LEFT);
$firstDay   = "$year-$padMonth-01";
$daysInMon  = (int) date('t', strtotime($firstDay));
// Monday = 0 .. Sunday = 6
$firstWday  = (int) date('N', strtotime($firstDay)) - 1;
$hariIni    = date('Y-m-d');

if (! function_exists('warnaKelas')) {
    function warnaKelas($status) {
        return [
            'hadir'  => 'hadir',
            'sakit'  => 'sakit',
            'izin'   => 'izin',
            'alpha'  => 'alpha',
        ][$status] ?? 'kosong';
    }
}

// Navigasi bulan sebelumnya / berikutnya
$prevMonth = $month - 1; $prevYear = $year;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
$nextMonth = $month + 1; $nextYear = $year;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

$urlBase = '/attendance?siswa_id=' . urlencode($siswa_id) . '&';
$urlPrev = $urlBase . 'year=' . $prevYear . '&month=' . $prevMonth;
$urlNext = $urlBase . 'year=' . $nextYear . '&month=' . $nextMonth;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kalender Absensi - <?= esc($siswa['nama'] ?? 'Siswa') ?></title>
<style>
  :root {
    --hijau:#22c55e; --hijau-l:#dcfce7;
    --kuning:#f59e0b; --kuning-l:#fef3c7;
    --biru:#3b82f6;  --biru-l:#dbeafe;
    --merah:#ef4444; --merah-l:#fee2e2;
    --abu:#e5e7eb;
    --teks:#1f2937; --teks-redup:#6b7280;
    --bg1:#eef2ff; --bg2:#faf5ff;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    background: linear-gradient(135deg, var(--bg1), var(--bg2));
    color: var(--teks);
    min-height: 100vh;
    padding: 24px 16px;
  }
  .wrap { max-width: 920px; margin: 0 auto; }
  .card {
    background:#fff; border-radius:18px; padding:28px;
    box-shadow:0 10px 30px rgba(79,70,229,.08); margin-bottom:18px;
  }
  h1 { font-size:1.5rem; margin-bottom:2px; }
  .sub { color:var(--teks-redup); font-size:.9rem; margin-bottom:18px; }
  .toolbar {
    display:flex; flex-wrap:wrap; gap:12px; align-items:center;
    justify-content:space-between; margin-bottom:22px;
  }
  .toolbar form { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
  label { font-size:.85rem; color:var(--teks-redup); }
  select, input[type=text] {
    padding:9px 12px; border:1px solid #e5e7eb; border-radius:10px;
    font-size:.9rem; background:#fff; cursor:pointer;
  }
  .btn {
    display:inline-block; padding:9px 16px; border:none; border-radius:10px;
    font-size:.9rem; text-decoration:none; cursor:pointer; background:#6366f1; color:#fff;
    transition:background .15s;
  }
  .btn:hover { background:#4f46e5; }
  .btn.ghost { background:#f3f4f6; color:var(--teks); }
  .nav-bulan {
    display:flex; align-items:center; gap:14px; justify-content:center;
    margin-bottom:18px;
  }
  .nav-bulan a {
    width:38px; height:38px; border-radius:50%; background:#f3f4f6;
    display:flex; align-items:center; justify-content:center; text-decoration:none;
    color:var(--teks); font-size:1.1rem; transition:background .15s;
  }
  .nav-bulan a:hover { background:#e5e7eb; }
  .nav-bulan .judul { font-weight:600; font-size:1.05rem; min-width:180px; text-align:center; }

  .legenda {
    display:flex; flex-wrap:wrap; gap:14px; justify-content:center;
    margin-bottom:20px; font-size:.82rem; color:var(--teks-redup);
  }
  .legenda span { display:inline-flex; align-items:center; gap:6px; }
  .dot { width:14px; height:14px; border-radius:4px; display:inline-block; }

  table.kalender {
    width:100%; border-collapse:separate; border-spacing:6px;
  }
  table.kalender th {
    text-align:center; font-size:.78rem; color:var(--teks-redup);
    padding:8px 0; text-transform:uppercase; letter-spacing:.5px;
  }
  table.kalender td {
    height:78px; vertical-align:top; border-radius:10px; padding:8px;
    position:relative; font-size:.85rem;
  }
  td.kosong-cell { background:#fafafa; }
  .hari { font-weight:600; font-size:.8rem; color:var(--teks-redup); }
  .hari-nomor { font-size:1rem; font-weight:700; }
  .status-pill {
    display:inline-block; margin-top:6px; padding:2px 8px; border-radius:20px;
    font-size:.72rem; font-weight:600; text-transform:capitalize;
  }
  .hadir  { background:var(--hijau-l); color:var(--hijau); }
  .sakit  { background:var(--kuning-l); color:var(--kuning); }
  .izin   { background:var(--biru-l);  color:var(--biru); }
  .alpha  { background:var(--merah-l); color:var(--merah); }
  .cell.hadir  { background:var(--hijau-l); }
  .cell.sakit  { background:var(--kuning-l); }
  .cell.izin   { background:var(--biru-l); }
  .cell.alpha  { background:var(--merah-l); }
  .cell.kosong { background:#f9fafb; }
  .cell.hari-ini { outline:2px solid #6366f1; outline-offset:-2px; }
  .cell a { text-decoration:none; color:inherit; display:block; height:100%; }
  .cell.belum { color:#9ca3af; }
  .ringkas { margin-top:14px; font-size:.85rem; color:var(--teks-redup); }
  @media (max-width:560px) {
    .card { padding:18px; }
    table.kalender td { height:60px; padding:5px; }
    .hari-nomor { font-size:.85rem; }
    .status-pill { font-size:.65rem; }
  }
</style>
</head>
<body>
<div class="wrap">

  <div class="card">
    <h1>Kalender Absensi Siswa</h1>
    <p class="sub">Klik tanggal untuk melihat / mengubah status kehadiran.</p>

    <div class="toolbar">
      <form method="get" action="/attendance">
        <label for="siswa_id">Siswa:</label>
        <select name="siswa_id" id="siswa_id" onchange="this.form.submit()">
          <?php foreach ($daftarSiswa as $s): ?>
            <option value="<?= $s['id'] ?>" <?= ($s['id'] == $siswa_id) ? 'selected' : '' ?>>
              <?= esc($s['nama']) ?> (<?= esc($s['kelas']) ?> <?= esc($s['jurusan']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </form>
      <a class="btn ghost" href="/siswa">← Data Siswa</a>
    </div>

    <?php if (! empty($siswa)): ?>
    <p class="ringkas">
      <strong><?= esc($siswa['nama']) ?></strong> —
      Kelas <?= esc($siswa['kelas']) ?>, Jurusan <?= esc($siswa['jurusan']) ?>.
    </p>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="legenda">
      <span><i class="dot" style="background:var(--hijau)"></i> Hadir</span>
      <span><i class="dot" style="background:var(--kuning)"></i> Sakit</span>
      <span><i class="dot" style="background:var(--biru)"></i> Izin</span>
      <span><i class="dot" style="background:var(--merah)"></i> Alpha</span>
      <span><i class="dot" style="background:var(--abu)"></i> Belum ada data</span>
    </div>

    <div class="nav-bulan">
      <a href="<?= $urlPrev ?>" title="Bulan sebelumnya">‹</a>
      <span class="judul"><?= $namaBulan[$month] ?? '' ?> <?= esc($year) ?></span>
      <a href="<?= $urlNext ?>" title="Bulan berikutnya">›</a>
    </div>

    <table class="kalender">
      <thead>
        <tr>
          <th>Sen</th><th>Sel</th><th>Rab</th><th>Kam</th><th>Jum</th><th>Sab</th><th>Min</th>
        </tr>
      </thead>
      <tbody>
        <tr>
        <?php
        // Sel kosong di awal
        for ($i = 0; $i < $firstWday; $i++) {
            echo '<td class="kosong-cell"></td>';
        }
        for ($d = 1; $d <= $daysInMon; $d++):
            $tgl = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $row = $absensi[$tgl] ?? null;
            $kelas = warnaKelas($row['status'] ?? null);
            $isHariIni = ($tgl === $hariIni);
            $urlDetail = '/attendance/detail/' . $tgl . '?siswa_id=' . urlencode($siswa_id);
        ?>
          <td class="cell <?= $kelas ?> <?= $isHariIni ? 'hari-ini' : '' ?>">
            <a href="<?= $urlDetail ?>">
              <div class="hari"><?= $hariPendek[(int) date('N', strtotime($tgl))] ?? '' ?></div>
              <div class="hari-nomor"><?= $d ?></div>
              <?php if ($row): ?>
                <span class="status-pill <?= $kelas ?>"><?= esc($row['status']) ?></span>
              <?php else: ?>
                <span class="status-pill" style="background:#f3f4f6;color:#9ca3af">belum</span>
              <?php endif; ?>
            </a>
          </td>
        <?php
        // Tutup baris tiap 7 sel (Minggu = indeks ke-6 dalam Monday-based)
        if ((($firstWday + $d) % 7) === 0) echo '</tr><tr>';
        endfor;
        // Sel kosong di akhir
        $sisa = (7 - (($firstWday + $daysInMon) % 7)) % 7;
        for ($i = 0; $i < $sisa; $i++) {
            echo '<td class="kosong-cell"></td>';
        }
        ?>
        </tr>
      </tbody>
    </table>
  </div>

</div>
</body>
</html>
