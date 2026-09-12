<h2>Edit Siswa</h2>
<form method="post" action="/siswa/update/<?= $siswa['id'] ?>">
Nama: <input type="text" name="nama" value="<?= $siswa['nama'] ?>"><br>
Kelas: <input type="text" name="kelas" value="<?= $siswa['kelas'] ?>"><br>
Jurusan: <input type="text" name="jurusan" value="<?= $siswa['jurusan'] ?>"><br>
Alamat: <textarea name="alamat"><?= $siswa['alamat'] ?></textarea><br>
<button type="submit">Update</button>
</form>
