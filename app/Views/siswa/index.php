<h2>Data Siswa</h2>
<a href="/siswa/create">Tambah Siswa</a>
<table border="1">
<tr><th>Nama</th><th>Kelas</th><th>Jurusan</th><th>Alamat</th><th>Aksi</th></tr>
<?php foreach($siswa as $s): ?>
<tr>
<td><?= $s['nama'] ?></td>
<td><?= $s['kelas'] ?></td>
<td><?= $s['jurusan'] ?></td>
<td><?= $s['alamat'] ?></td>
<td>
<a href="/siswa/edit/<?= $s['id'] ?>">Edit</a> |
<a href="/siswa/delete/<?= $s['id'] ?>">Hapus</a>
</td>
</tr>
<?php endforeach; ?>
</table>
