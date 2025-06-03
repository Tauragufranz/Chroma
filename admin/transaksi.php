<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Transaksi - Chroma Admin</title>

  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Chroma</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="GET" action="transaksi.php">
        <input type="text" name="query" placeholder="Search" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
        <button type="submit"><i class="bi bi-search"></i></button>
      </form>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/shalltear.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">Tauragufranz</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>Tauragufranz</h6>
              <span>Admin</span>
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item"><a class="nav-link collapsed" href="index.php"><i class="bi-house-door-fill"></i><span>Beranda</span></a></li>
      <li class="nav-item"><a class="nav-link collapsed" href="kategori.php"><i class="bi-grid"></i><span>Kategori Produk</span></a></li>
      <li class="nav-item"><a class="nav-link collapsed" href="produk.php"><i class="bi-box"></i><span>Produk</span></a></li>
      <li class="nav-item"><a class="nav-link collapsed" href="keranjang.php"><i class="bi bi-cart4"></i><span>Keranjang</span></a></li>
      <li class="nav-item"><a class="nav-link" href="transaksi.php"><i class="bi bi-cash"></i><span>Transaksi</span></a></li>
      <li class="nav-item"><a class="nav-link collapsed" href="laporan.php"><i class="bi-bar-chart"></i><span>Laporan</span></a></li>
      <li class="nav-item"><a class="nav-link collapsed" href="pengguna.php"><i class="bi-person"></i><span>Pengguna</span></a></li>
    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Transaksi</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
          <li class="breadcrumb-item active">Transaksi</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body pt-4">

              <?php
              include 'koneksi.php';

              // Ambil kategori untuk dropdown
              $sql_kategori = "SELECT id_ktg, nm_ktg FROM tb_ktg";
              $result_kategori = $koneksi->query($sql_kategori);

              $kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : "";

              // Query utama transaksi
              $sql = "SELECT j.id_jual, u.username, j.tgl_jual, j.total, j.diskon
                      FROM tb_jual j
                      JOIN tb_user u ON j.id_user = u.id_user";

              if (!empty($kategori_filter)) {
                $sql .= " JOIN tb_jualdtl jd ON j.id_jual = jd.id_jual
                          JOIN tb_produk p ON jd.id_produk = p.id_produk
                          WHERE p.id_ktg = '$kategori_filter'";
              }

              $sql .= " GROUP BY j.id_jual ORDER BY j.tgl_jual DESC";

              $result = $koneksi->query($sql);
              ?>

              <form class="d-flex align-items-center mb-3" method="GET" action="">
                <select name="kategori" class="form-select me-2" style="max-width: 250px;">
                  <option value="">-- Semua Kategori --</option>
                  <?php
                  if ($result_kategori->num_rows > 0) {
                    while ($row = $result_kategori->fetch_assoc()) {
                      $selected = ($kategori_filter == $row['id_ktg']) ? "selected" : "";
                      echo "<option value='" . $row['id_ktg'] . "' $selected>" . htmlspecialchars($row['nm_ktg']) . "</option>";
                    }
                  }
                  ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
              </form>

              <!-- Table -->
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Kode Belanja</th>
                    <th>Pengguna</th>
                    <th>Tanggal</th>
                    <th>Total Bayar</th>
                    <th>Diskon</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . $no++ . "</td>";
                      echo "<td>" . htmlspecialchars($row["id_jual"]) . "</td>";
                      echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                      echo "<td>" . date("d-m-Y H:i:s", strtotime($row["tgl_jual"])) . "</td>";
                      echo "<td>Rp " . number_format($row["total"], 0, ",", ".") . "</td>";
                      echo "<td>Rp " . number_format($row["diskon"], 0, ",", ".") . "</td>";
                      echo "<td><a href='detail_jual.php?id=" . $row["id_jual"] . "' class='btn btn-info btn-sm'>Detail</a></td>";
                      echo "</tr>";
                    }
                  } else {
                    echo "<tr><td colspan='7' class='text-center'>Belum ada data penjualan</td></tr>";
                  }
                  ?>
                </tbody>
              </table>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Chroma</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed
 by <a href="https://instagram.com/tashagiri_ken">TauraGufranz</a>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

</body>

</html>