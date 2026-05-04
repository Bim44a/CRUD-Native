<?php
$page_title = "Edit Anggota";
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Validasi ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=ID tidak valid");
    exit();
}

$id = (int)$_GET['id'];
$errors = [];

/* =========================
   AMBIL DATA (GET)
========================= */
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $stmt = $conn->prepare("SELECT * FROM anggota WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt->close();
        closeConnection();
        header("Location: index.php?error=Data tidak ditemukan");
        exit();
    }

    $data = $result->fetch_assoc();
    $stmt->close();

    // Set value ke form
    $kode = $data['kode_anggota'];
    $nama = $data['nama'];
    $email = $data['email'];
    $telepon = $data['telepon'];
    $alamat = $data['alamat'];
    $tgl_lahir = $data['tanggal_lahir'];
    $jk = $data['jenis_kelamin'];
    $pekerjaan = $data['pekerjaan'];
    $foto_lama = $data['foto'];
}

// Proses Update (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ambil data
    $kode = sanitize($_POST['kode_anggota']);
    $nama = sanitize($_POST['nama']);
    $email = sanitize($_POST['email']);
    $telepon = sanitize($_POST['telepon']);
    $alamat = sanitize($_POST['alamat']);
    $tgl_lahir = $_POST['tanggal_lahir'];
    $jk = sanitize($_POST['jenis_kelamin']);
    $pekerjaan = sanitize($_POST['pekerjaan']);

    // Validasi
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
        $errors[] = "Telepon wajib diisi";
    } elseif (!preg_match('/^08[0-9]{8,11}$/', $telepon)) {
        $errors[] = "Format telepon harus 08xxxxxxxxxx";
    }

    // Alamat
    if (empty($alamat)) {
        $errors[] = "Alamat wajib diisi";
    }

    // Tanggal Lahit + Umur
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
    }

    // Cek Duplikat  
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id_anggota FROM anggota WHERE (kode_anggota = ? OR email = ?) AND id_anggota != ?");
        $stmt->bind_param("ssi", $kode, $email, $id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Kode atau Email sudah digunakan";
        }

        $stmt->close();
    }

    // Ambil foto lama untuk dihapus jika ada foto baru
    $stmt = $conn->prepare("SELECT foto FROM anggota WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $foto_lama = $data['foto'];
    $stmt->close();
    //Upload Foto
    $foto_baru = $foto_lama;

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
            $foto_baru = time() . "_" . uniqid() . "." . $ext;
            move_uploaded_file($file['tmp_name'], "uploads/" . $foto_baru);

            // Hapus foto lama
            if (!empty($foto_lama) && file_exists("uploads/" . $foto_lama)) {
                unlink("uploads/" . $foto_lama);
            }
        }
    }

    // Update database
    if (count($errors) == 0) {

        $stmt = $conn->prepare("UPDATE anggota SET 
            kode_anggota = ?, 
            nama = ?, 
            email = ?, 
            telepon = ?, 
            alamat = ?, 
            tanggal_lahir = ?, 
            jenis_kelamin = ?, 
            pekerjaan = ?, 
            foto = ?
            WHERE id_anggota = ?");

        $stmt->bind_param(
            "sssssssssi",
            $kode,
            $nama,
            $email,
            $telepon,
            $alamat,
            $tgl_lahir,
            $jk,
            $pekerjaan,
            $foto_baru,
            $id
        );

        if ($stmt->execute()) {
            $stmt->close();
            closeConnection();
            header("Location: index.php?success=" . urlencode("Data berhasil diupdate"));
            exit();
        } else {
            $errors[] = "Error database: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">

            <div class="card">
                <div class="card-header bg-warning">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil"></i> Edit Anggota
                    </h4>
                </div>

                <div class="card-body">

                    <!-- Error -->
                    <?php if (count($errors) > 0): ?>
                        <div class="alert alert-danger">
                            <h6><i class="bi bi-exclamation-triangle"></i> Terdapat kesalahan:</h6>
                            <ul class="mb-0">
                                <?php foreach ($errors as $e): ?>
                                    <li><?php echo $e; ?></li>
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
                                    value="<?php echo htmlspecialchars($kode); ?>" required>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nama *</label>
                                <input type="text" name="nama" class="form-control"
                                    value="<?php echo htmlspecialchars($nama); ?>" required>
                            </div>
                        </div>

                        <!-- Email & Telepon -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon *</label>
                                <input type="text" name="telepon" class="form-control"
                                    value="<?php echo htmlspecialchars($telepon); ?>" required>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="mb-3">
                            <label class="form-label">Alamat *</label>
                            <textarea name="alamat" class="form-control" required><?php echo htmlspecialchars($alamat); ?></textarea>
                        </div>

                        <!-- Tgl Lahir & Jenis Kelamin -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" class="form-control"
                                    value="<?php echo htmlspecialchars($tgl_lahir); ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin *</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">-- pilih --</option>
                                    <option value="Laki-laki" <?php echo ($jk == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?php echo ($jk == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pekerjaan -->
                        <div class="mb-3">
                            <label class="form-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control"
                                value="<?php echo htmlspecialchars($pekerjaan); ?>">
                        </div>

                        <!-- Foto -->
                        <div class="mb-3">
                            <label class="form-label">Foto</label><br>

                            <?php if (!empty($foto_lama)): ?>
                                <img src="uploads/<?php echo $foto_lama; ?>" width="80" class="mb-2">
                            <?php endif; ?>

                            <input type="file" name="foto" class="form-control">
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button class="btn btn-warning">
                                <i class="bi bi-save"></i> Update Data
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