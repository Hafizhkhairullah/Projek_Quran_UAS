document.addEventListener("DOMContentLoaded", function () {
  // Mendefinisikan elemen-elemen yang digunakan dalam kode
  const body = document.body;
  const themeToggle = document.getElementById("theme-toggle");
  const ayatSelect = document.getElementById("ayat");
  const qariSelect = document.getElementById("qari");
  const searchToggle = document.getElementById("search-toggle");
  const searchOverlay = document.getElementById("search-overlay");
  const searchInput = document.getElementById("search-input");
  const searchResults = document.getElementById("search-results");
  

  let audioElement = null; // Variabel untuk menyimpan audio yang sedang diputar
  let currentAyatElement = null; // Elemen ayat yang sedang diputar
  let currentAudioElement = null; // Audio Element yang sedang diputar

// Periksa dan terapkan tema yang disimpan di localStorage
if (localStorage.getItem("theme") === "dark") {
  body.classList.add("dark-mode");
  const icon = themeToggle.querySelector("i");
  icon.classList.replace("fa-moon", "fa-sun");
} else {
  body.classList.remove("dark-mode");
  const icon = themeToggle.querySelector("i");
  icon.classList.replace("fa-sun", "fa-moon");
}

// Fungsi untuk memperbarui gaya dropdown
function updateDropdownStyle() {
  const isDarkMode = body.classList.contains("dark-mode");

  // Tambah atau hapus kelas "dark-dropdown" pada dropdown
  [ayatSelect, qariSelect].forEach((dropdown) => {
    if (dropdown) {
      dropdown.classList.toggle("dark-dropdown", isDarkMode);
      dropdown.classList.toggle("light-dropdown", !isDarkMode);
    }
  });
}

// Panggil fungsi untuk memperbarui dropdown saat halaman pertama kali dimuat
updateDropdownStyle();

// Ganti tema saat tombol tema diklik
themeToggle.addEventListener("click", function () {
  body.classList.toggle("dark-mode");
  const icon = themeToggle.querySelector("i");

  if (body.classList.contains("dark-mode")) {
    icon.classList.replace("fa-moon", "fa-sun");
    localStorage.setItem("theme", "dark"); // Simpan pilihan tema gelap
  } else {
    icon.classList.replace("fa-sun", "fa-moon");
    localStorage.setItem("theme", "light"); // Simpan pilihan tema terang
  }

  // Perbarui gaya dropdown setelah perubahan tema
  updateDropdownStyle();
});

// Buka overlay pencarian saat mengklik ikon pencarian
searchToggle.addEventListener("click", () => {
  searchOverlay.style.display = "block";
  searchInput.focus();
});

// Tutup overlay pencarian saat mengklik di luar kotak pencarian
searchOverlay.addEventListener("click", (e) => {
  if (e.target.id === "search-overlay") {
    searchOverlay.style.display = "none";
  }
});

  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  const surahNumber = urlParams.get("nomor");

  function loadSurahDetails() {
    fetch(`http://127.0.0.1:8000/api/surah`)
      .then((response) => response.json())
      .then((result) => {
        const surahData = result.data.find((surah) => surah.nomor == surahNumber);
        const surahDetailsElement = document.getElementById("surah-details");

        if (surahData) {
          surahDetailsElement.innerHTML = `
            <div class="surah-details bg-white rounded-lg shadow-lg p-6 w-full mx-auto mb-6">
              <div class="bg-purple-500 text-white rounded-lg p-4 flex items-center justify-between">
                <div>
                  <h1 class="text-2xl font-bold">${surahData.nama_latin} - ${surahData.nama}</h1>
                  <p class="text-sm surah-info">${surahData.arti} - ${surahData.jumlah_ayat} ayat - ${surahData.tempat_turun}</p>
                </div>
                <div class="text-white">
                  <i class="fas fa-star"></i>
                </div>
              </div>
              <div class="mt-4">
                <div class="flex justify-between items-center">
                  <div>
                    <label for="ayat" class="block text-white-700">Ayat</label>
                    <select id="ayat" class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3 text-gray-700">
                      ${Array.from(
                        { length: surahData.jumlah_ayat },
                        (_, i) => `<option value="${i + 1}">${i + 1}</option>`
                      ).join("")}
                    </select>
                  </div>
                  <div>
                    <label for="qari" class="block text-white-700">Qari</label>
                    <select id="qari" class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3 text-gray-700">
                      <option value="01">Abdullah-Al-Juhany</option>
                      <option value="02">Abdul-Muhsin-Al-Qasim</option>
                      <option value="03">Abdurrahman-as-Sudais</option>
                      <option value="04">Ibrahim-Al-Dossari</option>
                      <option value="05">Misyari-Rasyid-Al-Afasi</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          `;

          const ayatSelect = document.getElementById("ayat");
          ayatSelect.addEventListener("change", () => {
            const selectedAyatNumber = ayatSelect.value;
            const targetAyatElement = document.querySelector(`.ayat-item[data-nomor="${selectedAyatNumber}"]`);
            if (targetAyatElement) {
              targetAyatElement.scrollIntoView({ behavior: "smooth", block: "start" });
            }
          });

          const qariSelect = document.getElementById("qari");
          qariSelect.addEventListener("change", () => {
            if (audioElement) {
              const selectedQari = qariSelect.value;
              const audioDataString = currentAudioElement.getAttribute("data-audio");

              try {
                const audioData = JSON.parse(audioDataString);
                const newAudioUrl = audioData[selectedQari];

                if (newAudioUrl) {
                  audioElement.src = newAudioUrl;
                  audioElement.play()
                    .then(() => {
                      console.log("Audio switched to Qari:", selectedQari);
                    })
                    .catch((error) => {
                      console.error("Failed to play switched audio:", error);
                    });
                } else {
                  console.error(`Audio URL tidak ditemukan untuk Qari ${selectedQari}`);
                }
              } catch (error) {
                console.error("Parsing audio data gagal:", error);
              }
            }
          });
        } else {
          console.error("Surah data not found or incomplete");
        }
      })
      .catch((error) => console.error("Error fetching surah details:", error));
  }

  function loadAyat(nomorSurah) {
    fetch(`http://127.0.0.1:8000/api/surah/${nomorSurah}`)
      .then((response) => response.json())
      .then((result) => {
        const data = result.data;
        const ayatList = document.getElementById("ayat-list");
        ayatList.innerHTML = "";

        data.forEach((ayat) => {
          const ayatElement = document.createElement("div");
          ayatElement.className = "ayat-item p-4 border rounded shadow bg-white dark:bg-gray-700 mb-6 max-w-full mx-auto";
          ayatElement.setAttribute("data-nomor", ayat.nomor_ayat); // Tambahkan atribut nomor ayat
          ayatElement.innerHTML = `
            <div class="nomor-container mb-4">
              <div class="nomor text-white-600">${ayat.nomor_ayat}</div>
            </div>
            <div class="content">
              <p class="arabic-font mb-4">${ayat.teks_arab}</p>
              <p class="teks-latin mb-4">${ayat.teks_latin}</p>
              <p class="teks-terjemahan mb-4">${ayat.teks_terjemahan}</p>
              <div class="flex space-x-4 mt-4">
                <div class="bg-pink-500 rounded-full p-4 flex items-center justify-center play-audio" data-audio='${JSON.stringify(ayat.audio).replace(/\\/g, "")}'">
                  <i class="fas fa-play text-white"></i>
                </div>
                <div class="bg-black rounded-full px-4 py-2 flex items-center space-x-2">
                  <i class="fas fa-file-alt text-white"></i>
                  <span class="text-white">Tafsir</span>
                </div>
              </div>
            </div>
          `;
          ayatList.appendChild(ayatElement);
        });
        
        document.querySelectorAll(".play-audio").forEach((element) => {
          element.addEventListener("click", function () {
            let audioDataString = element.getAttribute("data-audio");

            try {
              audioDataString = audioDataString.replace(/^\ufeff/, "").trim();
              audioDataString = audioDataString.replace(/^[^:{\[]*/, "").replace(/[^}\]]*$/, "");
              const audioData = JSON.parse(audioDataString);

              const selectedQari = document.getElementById("qari").value;
              const audioUrl = audioData[selectedQari];

              if (audioElement && currentAudioElement === element) {
                if (!audioElement.paused) {
                  audioElement.pause();
                  element.querySelector("i").classList.replace("fa-pause", "fa-play");
                } else {
                  audioElement.play();
                  element.querySelector("i").classList.replace("fa-play", "fa-pause");
                }
                return;
              }

              if (audioElement) {
                audioElement.pause();
                audioElement.currentTime = 0;
                currentAudioElement.querySelector("i").classList.replace("fa-pause", "fa-play");
              }

              if (audioUrl) {
                audioElement = new Audio(audioUrl);
                audioElement.play()
                  .then(() => {
                    element.querySelector("i").classList.replace("fa-play", "fa-pause");
                    markAsPlaying(element.closest(".ayat-item"));
                  })
                  .catch((error) => {
                    console.error("Gagal memutar audio:", error);
                  });

                audioElement.addEventListener("ended", function () {
                  element.querySelector("i").classList.replace("fa-pause", "fa-play");

                  if (element.closest(".ayat-item").nextElementSibling === null) {
                    element.closest(".ayat-item").classList.remove("playing");
                  }

                  const nextAyatElement = element.closest(".ayat-item").nextElementSibling;
                  if (nextAyatElement) {
                    nextAyatElement.querySelector(".play-audio").click();
                  }
                });

                currentAudioElement = element;
              } else {
                console.error(`Audio URL tidak ditemukan untuk Qari ${selectedQari}`);
              }
            } catch (error) {
              console.error("Parsing JSON gagal:", error);
            }
          });
        });
      })
      .catch((error) => console.error("Error fetching ayat data:", error));
  }

  function markAsPlaying(element) {
    element.classList.add("playing");
    document.querySelectorAll(".ayat-item").forEach((other) => {
      if (other !== element) other.classList.remove("playing");
    });
  }

  searchInput.addEventListener("input", function () {
    const searchTerm = searchInput.value.toLowerCase();
    searchResults.innerHTML = "";
  
    if (searchTerm.trim() !== "") {
      document.querySelectorAll(".ayat-item").forEach((ayatItem) => {
        const arabicText = ayatItem.querySelector(".arabic-font")?.textContent || "";
        const latinText = ayatItem.querySelector(".teks-latin")?.textContent || "";
  
        if (
          arabicText.includes(searchTerm) ||
          latinText.toLowerCase().includes(searchTerm)
        ) {
          // Buat elemen baru hanya dengan teks Arab dan Latin
          const resultItem = document.createElement("div");
          resultItem.className = "search-result-item p-4 border rounded shadow bg-white dark:bg-gray-700 mb-6";
          resultItem.innerHTML = `
            <p class="arabic-font text-xl mb-2">${arabicText}</p>
            <p class="teks-latin text-sm">${latinText}</p>
          `;
  
          searchResults.appendChild(resultItem);
  
          // Tambahkan aksi klik untuk menggulir ke ayat asli
          resultItem.addEventListener("click", () => {
            ayatItem.scrollIntoView({ behavior: "smooth", block: "start" });
            searchOverlay.style.display = "none";
          });
        }
      });
  
      // Tampilkan pesan jika tidak ada hasil
      if (searchResults.innerHTML === "") {
        searchResults.innerHTML = "<p class='text-gray-500'>Tidak ditemukan ayat yang cocok.</p>";
      }
    }
  });
  

  if (surahNumber) {
    loadSurahDetails();
    loadAyat(surahNumber);
  }
});
