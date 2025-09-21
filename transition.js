document.addEventListener("DOMContentLoaded", () => {
  // Fungsi ini akan menjalankan animasi fade-out lalu pindah halaman
  const fadeOutAndNavigate = (url) => {
    // Tambahkan class 'fade-out' ke body
    document.body.classList.add("fade-out");

    // Tunggu animasi selesai (500ms, sesuai durasi di CSS)
    setTimeout(() => {
      // Pindah ke halaman tujuan
      window.location.href = url;
    }, 500);
  };

  // Cari semua link yang BUKAN link internal halaman (seperti #section-1)
  const allLinks = document.querySelectorAll('a[href]:not([href^="#"])');

  allLinks.forEach(link => {
    link.addEventListener("click", (event) => {
      // Hentikan aksi default link (pindah halaman secara instan)
      event.preventDefault();
      
      const destination = link.href;
      fadeOutAndNavigate(destination);
    });
  });
});