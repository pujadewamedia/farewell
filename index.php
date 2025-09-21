<?php
// Selalu mulai session di awal
session_start();

// 1. Cek apakah pengguna sudah login.
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login/");
    exit();
}

// Panggil file koneksi setelah pengecekan session
require_once 'koneksi.php';

// 2. Ambil data pengguna dari session
$userId = $_SESSION['user_id'];
$namaLengkap = $_SESSION['fullname'];

// --- QUERY PERTAMA UNTUK MENGAMBIL FOTO ---
$pathFotoDesktop = 'images/default-avatar.png'; // Fallback untuk desktop
$pathFotoMobile = 'images/default-avatar-cropped.png'; // Fallback untuk mobile (versi 1:1)

$sqlFoto = "SELECT path_foto, path_foto_cropped FROM foto WHERE id_user = ? ORDER BY id_photo DESC LIMIT 1";
$stmtFoto = $conn->prepare($sqlFoto);
$stmtFoto->bind_param("i", $userId);
$stmtFoto->execute();
$resultFoto = $stmtFoto->get_result();

if ($resultFoto->num_rows > 0) {
    $rowFoto = $resultFoto->fetch_assoc();
    $pathFotoDesktop = !empty($rowFoto['path_foto']) ? $rowFoto['path_foto'] : $pathFotoDesktop;
    $pathFotoMobile = !empty($rowFoto['path_foto_cropped']) ? $rowFoto['path_foto_cropped'] : $pathFotoMobile;
}
$stmtFoto->close();


// --- MULAI QUERY KEDUA UNTUK MENGAMBIL KATA ---

// Siapkan variabel fallback jika data tidak ditemukan
$kataIvan = "Pesan dari Ivan belum tersedia untukmu.";
$kataFarkhan = "Pesan dari Farkhan belum tersedia untukmu.";

// Query database untuk mendapatkan kata_ivan dan kata_farkhan
$sqlKata = "SELECT kata_ivan, kata_farkhan FROM kata WHERE id_user = ? ORDER BY id_kata DESC LIMIT 1";
$stmtKata = $conn->prepare($sqlKata);
$stmtKata->bind_param("i", $userId);
$stmtKata->execute();
$resultKata = $stmtKata->get_result();

// Jika data ditemukan, timpa variabel fallback
if ($resultKata->num_rows > 0) {
    $rowKata = $resultKata->fetch_assoc();
    $kataIvan = !empty($rowKata['kata_ivan']) ? $rowKata['kata_ivan'] : $kataIvan;
    $kataFarkhan = !empty($rowKata['kata_farkhan']) ? $rowKata['kata_farkhan'] : $kataFarkhan;
}
$stmtKata->close();


// Tutup koneksi setelah semua query selesai
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Media PKKMB UNNES 2025.</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="carousel.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:wght,FILL,GRAD@400,1,0&display=swap" rel="stylesheet">
    
  </head>
  <body>
    <div class="container">
      <div class="section-1">

        <div class="section-1-text">
          <p>Halo <?php echo htmlspecialchars($namaLengkap); ?>. Ciee yang udah "lulus" dari PKKMB UNNES 2025, media lagi.</p>
        </div>

        <div class="section-1-image" style="
          --bg-desktop: url('<?php echo htmlspecialchars($pathFotoDesktop); ?>');
          --bg-mobile: url('<?php echo htmlspecialchars($pathFotoMobile); ?>');
        "></div>
      </div> <div class="section-2">
        <div class="row-prekata">
          <p class="dm-sans-bolder prekata">Ada Sedikit Pesan dan Kesan dari Koor dan Wakoormu nih.</p>
        </div>
        <div class="row-kata">
          <div class="box-row-kata row-ivan">
            <div class="row-kata-atas">
              <div class="profile-picture-kata" id="ivan"></div>
              <div class="nama-kata">
                <p class="nama-kata-p dm-sans-bold">Ivan, Koor Media Kamu.</p>
              </div>
            </div>
            <div class="row-kata-bawah" id="ivan">
              <p><?php echo htmlspecialchars($kataIvan); ?></p>
            </div>
          </div>
          <div class="box-row-kata row-farkhan">
            <div class="row-kata-atas">
              <div class="profile-picture-kata" id="farkhan"></div>
              <div class="nama-kata">
                <p class="nama-kata-p dm-sans-bold">Farkhan, Wakil Koor Media Kamu.</p>
            </div>
            </div>
            <div class="row-kata-bawah" id="farkhan">
              <p><?php echo htmlspecialchars($kataFarkhan); ?></p>
            </div>
          </div>
        </div>
      </div>
      <div class="section-3">
        <div class="memories-opener">
          <p class="text-memories-opener dm-sans-bolder">Sudah kurang lebih 3 bulan kita bersama, sebagai satu keluarga, keluarga media.</p>
        </div>
        <div class="core-memories">
          <div class="maskot-kiri"></div>

          <div class="core-memories-container swiper">
            <div class="swiper-wrapper"></div>
          </div>

          <div class="maskot-kanan"></div>
        </div>

        <!-- Tombol & Progress di luar carousel -->
        <div class="carousel-controls">
          <div class="carousel-btn-container">
            <button class="carousel-btn prev">
              <span class="material-symbols-rounded">arrow_back</span>
            </button>
            <button class="carousel-btn play-pause">
              <span class="material-symbols-rounded">pause</span>
            </button>
            <button class="carousel-btn next">
              <span class="material-symbols-rounded">arrow_forward</span>
            </button>
          </div>
          <div class="carousel-progress-container">
            <div class="carousel-progress">
              <div class="progress-bar"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="section-4">
        <div class="kangenmedia dm-sans-bold">kaliankangenmediaga?</div>
      </div>
    </div>
    
    <nav class="floating-nav">
      <div class="logout-container">
        <div class="initial-state">
          <a href="#section-1" class="nav-button dm-sans">Halo, <?php echo htmlspecialchars($namaLengkap); ?>!</a>
        </div>
        <a href="logout.php" class="hover-state">
          <span class="logout-text dm-sans">Logout</span>
          <span class="material-symbols-rounded">logout</span>
        </a>
      </div>
    </nav>
    <script src="transition.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="carousel_builder.js"></script>
  </body>
</html>