document.addEventListener("DOMContentLoaded", () => {
  console.log("🚀 JS dimulai");

  const swiperWrapper = document.querySelector(
    ".core-memories-container .swiper-wrapper"
  );

  if (!swiperWrapper) {
    console.warn("⚠️ Tidak ada .swiper-wrapper ditemukan!");
    return;
  }

  fetch("get_carousel_data.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("📦 Data dari PHP:", data);

      if (data.length === 0) {
        console.warn("⚠️ Data kosong dari server!");
        return;
      }

      // ==== Build Slide ====
      data.forEach((item, index) => {
        let mediaElement = "";
        if (item.jenis === "foto") {
          mediaElement = `
            <div class="memories-image">
              <img src="${item.path}" alt="foto" />
            </div>`;
        } else if (item.jenis === "video") {
          mediaElement = `
            <div class="memories-video-wrapper">
              <video 
                src="${item.path}" 
                playsinline 
                autoplay 
                muted 
                loop
              ></video>
            </div>`;
        }

        const slideHTML = `
          <div class="swiper-slide">
            ${mediaElement}
            <div class="memories-caption">
              <div class="memories-title-date">
                <div class="memories-title">${item.judul || ""}</div>
                <span class="memories-separator">&bull;</span>
                <div class="memories-date">${item.tanggal_formatted || ""}</div>
              </div>
              <div class="memories-description">
                <p>${item.deskripsi || ""}</p>
              </div>
            </div>
          </div>
        `;
        swiperWrapper.innerHTML += slideHTML;
        console.log(`✅ Slide #${index + 1} ditambahkan`);
      });

      // ==== Init Swiper ====
      const swiper = new Swiper(".core-memories-container.swiper", {
        loop: true,
        grabCursor: true,
        slidesPerView: "auto",
        centeredSlides: true,
        spaceBetween: 30,
        allowTouchMove: true,
      });
      console.log("🎉 Swiper berhasil dibuat:", swiper);

      // ==== Custom Navigation ====
      const btnPrev = document.querySelector(".carousel-btn.prev");
      const btnNext = document.querySelector(".carousel-btn.next");
      const btnPlayPause = document.querySelector(".carousel-btn.play-pause");
      const progressBar = document.querySelector(
        ".carousel-progress .progress-bar"
      );

      btnPrev?.addEventListener("click", () => {
        swiper.slidePrev();
        resetProgress();
      });

      btnNext?.addEventListener("click", () => {
        swiper.slideNext();
        resetProgress();
      });

      // ==== Autoplay + Progress ====
      const autoplayDelay = 7000; // 7 detik
      let startTime;
      let elapsedBeforePause = 0;
      let isPlaying = true;
      let rafId;

      function updateProgress() {
        const now = performance.now();
        const elapsed = now - startTime + elapsedBeforePause;
        const progress = Math.min(elapsed / autoplayDelay, 1);

        progressBar.style.width = `${progress * 100}%`;

        if (progress < 1) {
          rafId = requestAnimationFrame(updateProgress);
        } else {
          swiper.slideNext();
          resetProgress();
        }
      }

      function resetProgress() {
        cancelAnimationFrame(rafId);
        startTime = performance.now();
        elapsedBeforePause = 0;
        progressBar.style.width = "0%";
        if (isPlaying) {
          rafId = requestAnimationFrame(updateProgress);
        }
      }

      function pauseProgress() {
        if (!isPlaying) return;
        isPlaying = false;
        cancelAnimationFrame(rafId);
        const now = performance.now();
        elapsedBeforePause += now - startTime;
      }

      function resumeProgress() {
        if (isPlaying) return;
        isPlaying = true;
        startTime = performance.now();
        rafId = requestAnimationFrame(updateProgress);
      }

      // ==== Play / Pause Button ====
      btnPlayPause?.addEventListener("click", () => {
        const icon = btnPlayPause.querySelector("span");

        if (isPlaying) {
          pauseProgress();
          icon.textContent = "play_arrow"; 
        } else {
          resumeProgress();
          icon.textContent = "pause"; 
        }
      });

      // ==== Video Control Saat Slide Berubah ====
      swiper.on("slideChangeTransitionStart", () => {
        const slides = document.querySelectorAll(".swiper-slide");
        slides.forEach((slide) => {
          const video = slide.querySelector("video");
          if (video) {
            if (
              slide.classList.contains("swiper-slide-active") ||
              slide.classList.contains("swiper-slide-duplicate-active")
            ) {
              video.play().catch(() => {});
              video.muted = false;
            } else {
              video.muted = true;
            }
          }
        });
      });

      // ==== Tap & Hold untuk Semua Slide ====
      const holdThreshold = 500; // ms
      document.querySelectorAll(".swiper-slide").forEach((slide) => {
        let holdTimer = null;
        let isHold = false;

        slide.addEventListener("pointerdown", (e) => {
          e.preventDefault();
          isHold = false;

          holdTimer = setTimeout(() => {
            pauseProgress();
            isHold = true;
          }, holdThreshold);
        });

        slide.addEventListener("pointerup", (e) => {
          e.preventDefault();
          clearTimeout(holdTimer);

          const video = slide.querySelector("video");

          if (!isHold && video) {
            video.muted = !video.muted;

            const feedbackIcon = document.createElement("span");
            feedbackIcon.className = "material-symbols-rounded video-mute-feedback";
            feedbackIcon.textContent = video.muted ? "volume_off" : "volume_up";
            slide.appendChild(feedbackIcon);

            requestAnimationFrame(() => feedbackIcon.classList.add("show"));
            setTimeout(() => {
              feedbackIcon.classList.remove("show");
              setTimeout(() => feedbackIcon.remove(), 300);
            }, 1000);
          }

          resumeProgress();
        });

        slide.addEventListener("pointerleave", () => {
          clearTimeout(holdTimer);
        });
      });

      // ==== Start First Progress ====
      resetProgress();
      swiper.emit("slideChangeTransitionStart"); 
    })
    .catch((error) => console.error("❌ Error fetching carousel data:", error));
});
