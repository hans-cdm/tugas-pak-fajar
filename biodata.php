<?php
// 1. MEMULAI SESSION
// Session digunakan untuk menyimpan data siswa sementara di server.
session_start();

// 2. INISIALISASI ARRAY DATA SISWA
// Jika array 'siswa' belum ada di session, buat array kosong.
if (!isset($_SESSION['siswa'])) {
    $_SESSION['siswa'] = [];
}

// 3. BAGIAN LOGIKA UNTUK MEMPROSES AKSI (HAPUS, UPDATE, SIMPAN SEMUA)

// Aksi: Menghapus Siswa
// Aksi ini dijalankan jika ada parameter 'hapus' di URL (misal: index.php?hapus=0)
if (isset($_GET['hapus'])) {
    $index = $_GET['hapus'];
    // Pastikan data dengan index tersebut ada, lalu hapus
    if (isset($_SESSION['siswa'][$index])) {
        array_splice($_SESSION['siswa'], $index, 1);
    }
    // Arahkan kembali ke halaman utama untuk membersihkan URL
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Aksi: Menyimpan Perubahan dari Form Edit
// Aksi ini dijalankan saat tombol 'update_siswa' dari form edit ditekan.
if (isset($_POST['update_siswa'])) {
    $index = $_POST['edit_index'];
    if (isset($_SESSION['siswa'][$index])) {
        $_SESSION['siswa'][$index] = [
            'nama_panjang' => $_POST['nama_panjang'],
            'nama_panggilan' => $_POST['nama_panggilan'],
            'usia' => $_POST['usia'],
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Aksi: Menyimpan Semua Data dari Form Input Banyak Siswa
// Aksi ini dijalankan saat tombol 'simpan_semua' ditekan.
if (isset($_POST['simpan_semua'])) {
    $daftar_nama_panjang = $_POST['nama_panjang']; // Ini akan menjadi array
    $daftar_nama_panggilan = $_POST['nama_panggilan']; // Ini akan menjadi array
    $daftar_usia = $_POST['usia']; // Ini akan menjadi array

    // Loop sebanyak data yang dikirim dari form
    for ($i = 0; $i < count($daftar_nama_panjang); $i++) {
        // Cek sederhana agar baris kosong tidak ikut tersimpan
        if (!empty($daftar_nama_panjang[$i])) {
            $_SESSION['siswa'][] = [
                'nama_panjang' => $daftar_nama_panjang[$i],
                'nama_panggilan' => $daftar_nama_panggilan[$i],
                'usia' => $daftar_usia[$i],
            ];
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Biodata Siswa</title>
</head>
<body>

<?php
// 4. BAGIAN TAMPILAN (VIEW) YANG BERUBAH SESUAI KONDISI

// Kondisi 1: Jika pengguna mengklik link 'Edit'
if (isset($_GET['edit'])) {
    $edit_index = $_GET['edit'];
    // Pastikan index siswa yang akan di-edit benar-benar ada
    if (isset($_SESSION['siswa'][$edit_index])) {
        $siswa_edit = $_SESSION['siswa'][$edit_index];
?>
        <h1>Edit Biodata Siswa</h1>
        <form action="" method="post">
            <input type="hidden" name="edit_index" value="<?php echo $edit_index; ?>">
            <table>
                <tr>
                    <td><label>Nama Panjang</label></td>
                    <td>:</td>
                    <td><input type="text" name="nama_panjang" value="<?php echo htmlspecialchars($siswa_edit['nama_panjang']); ?>" required></td>
                </tr>
                <tr>
                    <td><label>Nama Panggilan</label></td>
                    <td>:</td>
                    <td><input type="text" name="nama_panggilan" value="<?php echo htmlspecialchars($siswa_edit['nama_panggilan']); ?>" required></td>
                </tr>
                <tr>
                    <td><label>Usia</label></td>
                    <td>:</td>
                    <td><input type="number" name="usia" value="<?php echo htmlspecialchars($siswa_edit['usia']); ?>" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><button type="submit" name="update_siswa">Update Siswa</button></td>
                </tr>
            </table>
        </form>
        <br>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>">Batal Edit & Kembali ke Daftar</a>

<?php
    } else {
        echo "<p>Error: Data siswa tidak ditemukan.</p>";
    }

// Kondisi 2: Jika pengguna sudah memasukkan jumlah siswa
} elseif (isset($_POST['jumlah_submit'])) {
    $jumlah = (int)$_POST['jumlah_siswa'];
    if ($jumlah > 0) {
?>
        <h1>Formulir Tambah Biodata Siswa</h1>
        <form action="" method="post">
            <?php for ($i = 0; $i < $jumlah; $i++): ?>
                <h3>Data Siswa ke-<?php echo $i + 1; ?></h3>
                <table>
                    <tr>
                        <td><label>Nama Panjang</label></td>
                        <td>:</td>
                        <td><input type="text" name="nama_panjang[]" required></td>
                    </tr>
                    <tr>
                        <td><label>Nama Panggilan</label></td>
                        <td>:</td>
                        <td><input type="text" name="nama_panggilan[]" required></td>
                    </tr>
                    <tr>
                        <td><label>Usia</label></td>
                        <td>:</td>
                        <td><input type="number" name="usia[]" required></td>
                    </tr>
                </table>
                <hr>
            <?php endfor; ?>
            <button type="submit" name="simpan_semua">Simpan Semua Data</button>
        </form>
<?php
    } else {
        echo "<p>Jumlah siswa harus lebih dari 0.</p>";
        echo '<a href="' . $_SERVER['PHP_SELF'] . '">Kembali</a>';
    }

// Kondisi 3: Tampilan Awal
} else {
?>
    <h1>Input Data Siswa</h1>
    <form action="" method="post">
        <label for="jumlah">Berapa siswa yang datanya akan diinput?</label><br><br>
        <input type="number" id="jumlah" name="jumlah_siswa" min="1" required>
        <button type="submit" name="jumlah_submit">Lanjutkan</button>
    </form>
<?php
}
?>

<hr>

<h2>Daftar Siswa yang Telah Ditambahkan</h2>
<?php if (!empty($_SESSION['siswa'])): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Panjang</th>
                <th>Nama Panggilan</th>
                <th>Usia</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['siswa'] as $index => $siswa): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($siswa['nama_panjang']); ?></td>
                    <td><?php echo htmlspecialchars($siswa['nama_panggilan']); ?></td>
                    <td><?php echo htmlspecialchars($siswa['usia']); ?></td>
                    <td>
                        <a href="?edit=<?php echo $index; ?>">Edit</a> |
                        <a href="?hapus=<?php echo $index; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Belum ada data siswa yang ditambahkan.</p>
<?php endif; ?>

</body>
</html>