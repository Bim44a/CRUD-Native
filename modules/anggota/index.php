<?php
$page_title = "Data Anggota";
require_once '../../config/database.php';

// Pagination
$limit = 10; //Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = ($page < 1) ? 1 : $page;
$offset = ($page - 1) * $limit;

// Input Search & Filter
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$jk_filter = isset($_GET['jk']) ? sanitize($_GET['jk']) : '';

// Export ke Excel
if (isset($_GET['export']) && $_GET['export'] == 'csv') {

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="data_anggota.csv"');

    $output = fopen('php://output', 'w');

    // Header
    fputcsv($output, [
        'No',
        'Kode',
        'Nama',
        'Email',
        'Telepon',
        'Status',
        'Jenis Kelamin',
        'Tanggal Daftar'
    ]);

    if (!empty($search) || !empty($status_filter) || !empty($jk_filter)) {

        $query_export = "SELECT * FROM anggota 
            WHERE (nama LIKE ? OR email LIKE ? OR telepon LIKE ?)
            " . (!empty($status_filter) ? "AND status = ?" : "") . "
            " . (!empty($jk_filter) ? "AND jenis_kelamin = ?" : "") . "
            ORDER BY created_at DESC";

        $stmt = $conn->prepare($query_export);

        $search_param = "%$search%";

        if (!empty($status_filter) && !empty($jk_filter)) {
            $stmt->bind_param("sssss", $search_param, $search_param, $search_param, $status_filter, $jk_filter);
        } elseif (!empty($status_filter)) {
            $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $status_filter);
        } elseif (!empty($jk_filter)) {
            $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $jk_filter);
        } else {
            $stmt->bind_param("sss", $search_param, $search_param, $search_param);
        }
    } else {
        $query_export = "SELECT * FROM anggota ORDER BY created_at DESC";
        $stmt = $conn->prepare($query_export);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $no = 1;
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $no++,
            $row['kode_anggota'],
            $row['nama'],
            $row['email'],
            "'" . $row['telepon'], // Saya gunakan tanda petik (') untuk menjaga format nomor di Excel
            $row['status'],
            $row['jenis_kelamin'],
            "'" . $row['tanggal_daftar']
        ]);
    }

    fclose($output);
    exit;
}
require_once '../../includes/header.php';

// Build Query 
if (!empty($search) || !empty($status_filter) || !empty($jk_filter)) {
    // Query dengan search & filter
    $query = "SELECT * FROM anggota 
        WHERE (nama LIKE ? OR email LIKE ? OR telepon LIKE ?)
        " . (!empty($status_filter) ? "AND status = ?" : "") . "
        " . (!empty($jk_filter) ? "AND jenis_kelamin = ?" : "") . "
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($query);

    $search_param = "%$search%";

    if (!empty($status_filter) && !empty($jk_filter)) {
        $stmt->bind_param("sssssii", $search_param, $search_param, $search_param, $status_filter, $jk_filter, $limit, $offset);
    } elseif (!empty($status_filter)) {
        $stmt->bind_param("ssssii", $search_param, $search_param, $search_param, $status_filter, $limit, $offset);
    } elseif (!empty($jk_filter)) {
        $stmt->bind_param("ssssii", $search_param, $search_param, $search_param, $jk_filter, $limit, $offset);
    } else {
        $stmt->bind_param("sssii", $search_param, $search_param, $search_param, $limit, $offset);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Hitung total rows untuk pagination
    $count_query = "SELECT COUNT(*) as total FROM anggota 
        WHERE (nama LIKE ? OR email LIKE ? OR telepon LIKE ?)
        " . (!empty($status_filter) ? "AND status = ?" : "") . "
        " . (!empty($jk_filter) ? "AND jenis_kelamin = ?" : "");

    $stmt_count = $conn->prepare($count_query);

    if (!empty($status_filter) && !empty($jk_filter)) {
        $stmt_count->bind_param("sssss", $search_param, $search_param, $search_param, $status_filter, $jk_filter);
    } elseif (!empty($status_filter)) {
        $stmt_count->bind_param("ssss", $search_param, $search_param, $search_param, $status_filter);
    } elseif (!empty($jk_filter)) {
        $stmt_count->bind_param("ssss", $search_param, $search_param, $search_param, $jk_filter);
    } else {
        $stmt_count->bind_param("sss", $search_param, $search_param, $search_param);
    }

    $stmt_count->execute();
    $total_rows = $stmt_count->get_result()->fetch_assoc()['total'];
} else {
    // Query tanpa search & filter
    $query = "SELECT * FROM anggota 
              ORDER BY created_at DESC 
              LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_rows = $conn->query("SELECT COUNT(*) as total FROM anggota")->fetch_assoc()['total'];
}

// Hitung total halaman
$total_pages = ceil($total_rows / $limit);

// Dashboard Statistik
$query_stat = "SELECT 
    COUNT(*) as total,
    SUM(status='Aktif') as aktif,
    SUM(status='Nonaktif') as nonaktif,
    SUM(jenis_kelamin='Laki-laki') as laki,
    SUM(jenis_kelamin='Perempuan') as perempuan
FROM anggota";

$data_stat = $conn->query($query_stat)->fetch_assoc();

$total_all = $data_stat['total'];
$total_aktif = $data_stat['aktif'];
$total_nonaktif = $data_stat['nonaktif'];
$total_laki = $data_stat['laki'];
$total_perempuan = $data_stat['perempuan'];
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2><i class="bi bi-people"></i> Data Anggota</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="create.php" class="btn btn-primary">+ Tambah Anggota</a>

            <!-- Export data ke Excel -->
            <a href="?export=csv&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&jk=<?= urlencode($jk_filter) ?>"
                class="btn btn-success">
                <i class="bi bi-file-earmark-excel"> Excel</i>
            </a>
        </div>
    </div>

    <?php
    // Success/Error messages (sama seperti sebelumnya)
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show">';
        echo '<i class="bi bi-check-circle"></i> ' . htmlspecialchars($_GET['success']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }

    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show">';
        echo '<i class="bi bi-x-circle"></i> ' . htmlspecialchars($_GET['error']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
    ?>

    <!-- Statistik -->
    <div class="row mb-3">
        <div class="col">
            <div class="card text-center bg-secondary text-white">
                <div class="card-body">
                    <h6>Total</h6>
                    <h5><?= $total_all ?></h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <h6>Aktif</h6>
                    <h5><?= $total_aktif ?></h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center bg-danger text-white">
                <div class="card-body">
                    <h6>Nonaktif</h6>
                    <h5><?= $total_nonaktif ?></h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center bg-primary text-white">
                <div class="card-body">
                    <h6>Laki-laki</h6>
                    <h5><?= $total_laki ?></h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center bg-warning text-dark">
                <div class="card-body">
                    <h6>Perempuan</h6>
                    <h5><?= $total_perempuan ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari nama, email, atau telepon..."
                        value="<?= htmlspecialchars($search) ?>">
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Aktif" <?= $status_filter == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Nonaktif" <?= $status_filter == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="jk" class="form-select">
                        <option value="">Semua Jenis Kelamin</option>
                        <option value="Laki-laki" <?= $jk_filter == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= $jk_filter == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            Daftar Anggota
        </div>
        <div class="card-body">

            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Jenis Kelamin</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = $offset + 1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>

                                    <!-- Foto -->
                                    <td>
                                        <?php if (!empty($row['foto']) && file_exists("uploads/" . $row['foto'])): ?>
                                            <img src="uploads/<?= $row['foto'] ?>" width="50" height="50" style="object-fit:cover;">
                                        <?php else: ?>
                                            <span class="text-muted">No Image</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?= htmlspecialchars($row['kode_anggota']) ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['telepon']) ?></td>

                                    <!-- Badge Jenis Kelamin -->
                                    <td>
                                        <?php if ($row['jenis_kelamin'] == 'Laki-laki'): ?>
                                            <span class="badge bg-primary">Laki-laki</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Perempuan</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Badge Status -->
                                    <td>
                                        <?php if ($row['status'] == 'Aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <a href="edit.php?id=<?= $row['id_anggota'] ?>"
                                            class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete.php?id=<?= $row['id_anggota'] ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <!-- Previous -->
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="?page=<?= ($page - 1) ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&jk=<?= urlencode($jk_filter) ?>">
                                Previous
                            </a>
                        </li>

                        <!-- Nomor halaman -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link"
                                    href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&jk=<?= urlencode($jk_filter) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next -->
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="?page=<?= ($page + 1) ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&jk=<?= urlencode($jk_filter) ?>">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="alert alert-info mt-3">
                    Total: <?= $total_rows ?> anggota | Halaman <?= $page ?> dari <?= $total_pages ?>
                </div>

            <?php else: ?>
                <div class="alert alert-warning">
                    Tidak ada data ditemukan
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
if (isset($stmt)) $stmt->close();
if (isset($stmt_count)) $stmt_count->close();
closeConnection();
require_once '../../includes/footer.php';
?>