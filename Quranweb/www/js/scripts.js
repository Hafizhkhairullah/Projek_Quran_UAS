document.addEventListener("DOMContentLoaded", function() {
  // Mendefinisikan elemen-elemen yang digunakan dalam kode
  const themeToggle = document.getElementById("theme-toggle");
  const searchToggle = document.getElementById("search-toggle");
  const searchOverlay = document.getElementById("search-overlay");
  const searchInput = document.getElementById("search-input");
  const searchResults = document.getElementById("search-results");
  const surahContainer = document.getElementById('surah-container');
  const body = document.body;
  let surahList = [];

  // Logika pengaturan tema
  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark-mode");
    themeToggle.querySelector("i").classList.replace("fa-moon", "fa-sun");
  }
  
  themeToggle.addEventListener("click", () => {
    body.classList.toggle("dark-mode");
    const icon = themeToggle.querySelector("i");
    if (body.classList.contains("dark-mode")) {
      icon.classList.replace("fa-moon", "fa-sun");
      localStorage.setItem("theme", "dark");
    } else {
      icon.classList.replace("fa-sun", "fa-moon");
      localStorage.setItem("theme", "light");
    }
  });

  // Logika toggle overlay pencarian
  searchToggle.addEventListener("click", () => {
    searchOverlay.style.display = "block";
    searchInput.focus();
  });
  
  searchOverlay.addEventListener("click", (e) => {
    if (e.target.id === "search-overlay") {
      searchOverlay.style.display = "none";
    }
  });

  // Fungsi untuk menampilkan Surah
  function displaySurahs(container, list, searchTerm = '') {
    container.innerHTML = '';
    list.forEach(surah => {
      const surahElement = document.createElement('div');
      const isHighlighted = surah.nama_latin.toLowerCase().includes(searchTerm);

      surahElement.className = `hover-highlight bg-white rounded-lg shadow p-4 hover:bg-green-100 dark:bg-gray-700 dark:hover:bg-green-900`;
      surahElement.innerHTML = `
        <a href="ayat.html?nomor=${surah.nomor}" class="flex justify-between items-center">
          <div>
            <p class="text-lg font-semibold">${surah.nomor}. ${surah.nama_latin}</p>
            <p class="text-gray-500">${surah.arti} · ${surah.jumlah_ayat} ayat · ${surah.tempat_turun}</p>
          </div>
          <p class="text-lg font-semibold arabic-font">${surah.nama}</p>
        </a>
      `;

      container.appendChild(surahElement);
    });
  }

  // Mengambil data Surah dari API
  fetch('http://127.0.0.1:8000/api/surah')
    .then(response => response.json())
    .then(data => {
      surahList = data.data;
      displaySurahs(surahContainer, surahList);

      // Menambahkan event listener untuk input pencarian
      searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredSurahs = surahList.filter(surah => surah.nama_latin.toLowerCase().includes(searchTerm));
        displaySurahs(searchResults, filteredSurahs, searchTerm);
      });
    })
    .catch(error => console.error('Error fetching surah data:', error));
});
