<?php include "koneksi.php"; ?>
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

  <!-- Header & Sidebar -->
  <?php include 'header_sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
    <h1>Detail Jual</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
            <li class="breadcrumb-item">Transaksi</li>
            <li class="breadcrumb-item active">Detail Jual</li>
        </ol>
    </nav>
</div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <a href="t_produk.php" class="btn btn-primary mt-3">
              <i class="bi bi-plus-lg"></i> Tambah Data
            </a>
          </div>
        </div>
      </div>
    </div>

    <<section class="section">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Lihat Detail Transaksi</h5>
                    <div class="table-responsive">
                        <?php
                        include 'koneksi.php'; // pastikan koneksi DB kamu benar

                        $id_jual = $_GET['id']; // misalnya dari URL atau request

                        // ambil data tb_jual
                        $jual = mysqli_fetch_assoc(mysqli_query($koneksi, "
                            SELECT * FROM tb_jual tj
                            JOIN tb_user tu ON tj.id_user = tu.id_user
                            WHERE tj.id_jual = '$id_jual'
                        "));

                        // ambil data detail jual
                        $detail = mysqli_query($koneksi, "
                            SELECT tjd.id_produk, tjd.qty, tjd.harga AS subtotal, tp.nm_produk, tp.harga AS harga_produk
                            FROM tb_jualdt tjd
                            JOIN tb_produk tp ON tjd.id_produk = tp.id_produk
                            WHERE tjd.id_jual = '$id_jual'
                        ");
                        ?>

                        <table class="table table-striped mt-2">
                            <tbody>
                                <tr>
                                    <th>Kode Belanja</th>
                                    <td><?= $jual['id_jual'] ?></td>
                                </tr>
                                <tr>
                                    <th>Pengguna</th>
                                    <td><?= $jual['username'] ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td><?= date('d-m-Y H:i:s', strtotime($jual['tgl_jual'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Total Bayar</th>
                                    <td>Rp <?= number_format($jual['total'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <th>Diskon</th>
                                    <td>Rp <?= number_format($jual['diskon'], 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <h5>Detail Pembelian:</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($d = mysqli_fetch_assoc($detail)) :
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $d['nm_produk'] ?></td>
                                    <td>Rp <?= number_format($d['harga_produk'], 0, ',', '.') ?></td>
                                    <td><?= $d['qty'] ?></td>
                                    <td>Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                    </div>
                    <a href="transaksi.php" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</section>

              <!-- Table with stripped rows -->
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Keterangan</th>
                    <th>Kategori</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $query = isset($_GET['query']) ? mysqli_real_escape_string($koneksi, $_GET['query']) : '';
                  $sql_query = "SELECT tb_produk.*, tb_ktg.nm_ktg 
                                FROM tb_produk 
                                LEFT JOIN tb_ktg ON tb_produk.id_ktg = tb_ktg.id_ktg";
                  if (!empty($query)) {
                      $sql_query .= " WHERE tb_produk.nm_produk LIKE '%$query%' 
                                      OR tb_ktg.nm_ktg LIKE '%$query%' 
                                      OR tb_produk.ket LIKE '%$query%'";
                  }

                  $sql = mysqli_query($koneksi, $sql_query);
                  if (mysqli_num_rows($sql) > 0) {
                      while ($hasil = mysqli_fetch_array($sql)) {
                          ?>
                          <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($hasil['nm_produk']); ?></td>
                            <td>Rp <?php echo number_format($hasil['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $hasil['stok']; ?></td>
                            <td><?php echo htmlspecialchars($hasil['ket']); ?></td>
                            <td><?php echo htmlspecialchars($hasil['nm_ktg']); ?></td>
                            <td>
                              <?php if (!empty($hasil['gambar'])): ?>
                                <img src="produk_img/<?php echo htmlspecialchars($hasil['gambar']); ?>" width="100">
                              <?php else: ?>
                                Tidak ada gambar
                              <?php endif; ?>
                            </td>
                            <td>
                              <a href="e_produk.php?id=<?php echo $hasil['id_produk']; ?>" class="btn btn-warning">
                                <i class="bi bi-pencil-square"></i>
                              </a>
                              <a href="h_produk.php?id=<?php echo $hasil['id_produk']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                <i class="bi bi-trash"></i>
                              </a>
                            </td>
                          </tr>
                          <?php
                      }
                  } else {
                      echo '<tr><td colspan="8" class="text-center">Belum ada data</td></tr>';
                  }
                  ?>
                </tbody>
              </table>
              <!-- End Table -->

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
      Designed by <a href="https://instagram.com/tashagiri_ken">TauraGufranz</a>
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>

</body>

</html>
