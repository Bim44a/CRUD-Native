<?php
$page_title = "Tambah Anggota";
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Inisialisasi
$errors = [];
$kode = '';
$nama = '';
$email = '';
$telepon = '';
$alamat = '';
$tgl_lahir = '';
$jk = '';
$pekerjaan = '';
$tanggal_daftar = date('Y-m-d');
$status = 'Aktif';

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $kode = sanitize($_POST['kode_anggota']);
    $nama = sanitize($_POST['nama']);
    $email = sanitize($_POST['email']);
    $telepon = sanitize($_POST['telepon']);
    $alamat = sanitize($_POST['alamat']);
    $tgl_lahir = $_POST['tanggal_lahir'];
    $jk = sanitize($_POST['jenis_kelamin']);
    $pekerjaan = sanitize($_POST['pekerjaan']);

    // Validasi semua field
    // Kode Anggota
    if (empty($kode)) {
        $errors[] = "Kode anggota wajib diisi";
    }

    // Nama
    if (empty($nama)) {
        $errors[] = "Nama wajib diisi";
    } elseif (strlen($nama) < 3) {
        $errors[] = "Nama minimal 3 karakter";
    }

    // Email
    if (empty($email)) {
        $errors[] = "Email wajib diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }

    // Telepon
    if (empty($telepon)) {
        $errors[] = "Nomor Telepon wajib diisi";
    } elseif (!preg_match('/^08[0-9]{8,11}$/', $telepon)) {
        $errors[] = "Format telepon harus 08xxxxxxxxxx";
    }

    // Alamat
    if (empty($alamat)) {
        $errors[] = "Alamat wajib diisi";
    }

    // Tanggal Lahir + Umur
    if (empty($tgl_lahir)) {
        $errors[] = "Tanggal lahir wajib diisi";
    } else {
        $lahir = new DateTime($tgl_lahir);
        $today = new DateTime();
        $umur = $today->diff($lahir)->y;

        if ($umur < 10) {
            $errors[] = "Umur minimal 10 tahun";
        }
    }

    // Jenis Kelamin
    if (empty($jk)) {
        $errors[] = "Jenis kelamin wajib dipilih";
    } elseif (!in_array($jk, ['Laki-laki', 'Perempuan'])) {
        $errors[] = "Jenis kelamin tidak valid";
    }

    // Cek Duplikat 
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id_anggota FROM anggota WHERE kode_anggota=? OR email=?");
        $stmt->bind_param("ss", $kode, $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Kode atau Email sudah digunakan";
        }
        $stmt->close();
    }

    // Upload Foto
    $foto_name = null;

    if (!empty($_FILES['foto']['name'])) {
        $file = $_FILES['foto'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            $errors[] = "Format foto harus JPG/PNG";
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = "Ukuran maksimal 2MB";
        }

        if (empty($errors)) {
            $foto_name = time() . "_" . uniqid() . "." . $ext;
            move_uploaded_file($file['tmp_name'], "uploads/" . $foto_name);
        }
    }

    // INSERT
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO anggota 
        (kode_anggota, nama, email, telepon, alamat, tanggal_lahir, jenis_kelamin, pekerjaan, tanggal_daftar, status, foto) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssssssssss",
            $kode,
            $nama,
            $email,
            $telepon,
            $alamat,
            $tgl_lahir,
            $jk,
            $pekerjaan,
            $tanggal_daftar,
            $status,
            $foto_name
        );

        if ($stmt->execute()) {
            header("Location: index.php?success=" . urlencode("Data berhasil ditambahkan"));
            exit;
        } else {
            $errors[] = "Gagal menyimpan data";
        }

        $stmt->close();
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-person-plus"></i> Tambah Anggota
                    </h4>
                </div>

                <div class="card-body">

                    <!-- Error -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h6><i class="bi bi-exclamation-triangle"></i> Terjadi kesalahan:</h6>
                            <ul class="mb-0">
                                <?php foreach ($errors as $e): ?>
                                    <li><?= $e ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">

                        <!-- Kode & Nama -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kode Anggota *</label>
                                <input type="text" name="kode_anggota" class="form-control"
                                    value="<?= htmlspecialchars($kode) ?>" placeholder="AG-001">
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nama *</label>
                                <input type="text" name="nama" class="form-control"
                                    value="<?= htmlspecialchars($nama) ?>">
                            </div>
                        </div>

                        <!-- Email & Telepon -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($email) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon *</label>
                                <input type="text" name="telepon" class="form-control"
                                    value="<?= htmlspecialchars($telepon) ?>" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="mb-3">
                            <label class="form-label">Alamat *</label>
                            <textarea name="alamat" class="form-control"><?= htmlspecialchars($alamat) ?></textarea>
                        </div>

                        <!-- Tgl Lahir & Jenis Kelamin -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" class="form-control"
                                    value="<?= htmlspecialchars($tgl_lahir) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin *</label>
                                <select name="jenis_kelamin" class="form-select">
                                    <option value="">-- pilih --</option>
                                    <option value="Laki-laki" <?= $jk == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= $jk == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pekerjaan -->
                        <div class="mb-3">
                            <label class="form-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control"
                                value="<?= htmlspecialchars($pekerjaan) ?>">
                        </div>

                        <!-- Foto -->
                        <div class="mb-3">
                            <label class="form-label">Foto (optional)</label>
                            <input type="file" name="foto" class="form-control">
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Data
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
closeConnection();
require_once '../../includes/footer.php';
?>