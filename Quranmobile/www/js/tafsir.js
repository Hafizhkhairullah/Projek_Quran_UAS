document.addEventListener("DOMContentLoaded", () => {
    const tafsirListContainer = document.getElementById("tafsir-list");
    const surahTitle = document.getElementById("surah-title");
    const surahInfo = document.getElementById("surah-info");
    const lihatSurahBtn = document.getElementById("lihat-surah-btn");
    const params = new URLSearchParams(window.location.search);
    const surahNomor = params.get('surah');
    const tafsirApiEndpoint = `https://759d-103-171-83-15.ngrok-free.app/api/tafsir/${surahNomor}`;
    const surahApiEndpoint = "https://759d-103-171-83-15.ngrok-free.app/api/surah";

    // Fungsi untuk menampilkan informasi Surah
    function renderSurahDetails() {
        console.log("Fetching surah data from:", surahApiEndpoint); // Log endpoint
        fetch(surahApiEndpoint, {
            mode: 'cors',
            headers: {
                'ngrok-skip-browser-warning': 'true',
                'User-Agent': 'CustomUserAgent/1.0'
            }
        })
        .then(response => {
            console.log("Response status:", response.status); // Log response status
            return response.json();
        })
        .then(data => {
            console.log("Surah data received:", data); // Log received data
            const surah = data.data.find(item => item.nomor == surahNomor);
            if (surah) {
                surahTitle.textContent = surah.nama_latin;
                surahInfo.textContent = `${surah.tempat_turun.toUpperCase()} • ${surah.jumlah_ayat} Ayat • ${surah.arti}`;
            } else {
                console.error("Surah tidak ditemukan.");
            }
        })
        .catch(error => {
            console.error("Error fetching surah data:", error);
        });
    }

    // Fungsi untuk menampilkan Tafsir
    function renderTafsir() {
        console.log("Fetching tafsir data from:", tafsirApiEndpoint); // Log endpoint
        fetch(tafsirApiEndpoint, {
            mode: 'cors',
            headers: {
                'ngrok-skip-browser-warning': 'true',
                'User-Agent': 'CustomUserAgent/1.0'
            }
        })
        .then(response => {
            console.log("Response status:", response.status); // Log response status
            return response.json();
        })
        .then(data => {
            console.log("Data tafsir yang diterima:", data); // Log untuk memeriksa data yang diterima
            if (data && data.data && data.data.length > 0) {
                tafsirListContainer.innerHTML = "";
                data.data.forEach(tafsir => {
                    const tafsirElement = document.createElement("div");
                    tafsirElement.className = "tafsir-ayat";
                    tafsirElement.innerHTML = `
                        <p class="teks-ayat">Ayat ${tafsir.nomor_ayat}</p>
                        <p class="teks-tafsir">${tafsir.teks_tafsir}</p>
                    `;
                    tafsirListContainer.appendChild(tafsirElement);
                });
            } else {
                console.error("Data tafsir tidak ditemukan atau tidak memiliki format yang benar");
            }
        })
        .catch(error => {
            console.error("Error fetching tafsir data:", error);
        });
    }

    // Ketika tombol "Lihat Surah" ditekan
    if (lihatSurahBtn) {
        lihatSurahBtn.addEventListener("click", () => {
            // Redirect to ayat.html with a flag in localStorage
            localStorage.setItem('navigateFromTafsir', true);
            window.location.href = `ayat.html?surah=${surahNomor}`;
        });
    }

    // Check if navigating back to index from ayat.html or tafsir.html
    window.addEventListener('popstate', () => {
        if (localStorage.getItem('navigateFromTafsir') === 'true') {
            localStorage.removeItem('navigateFromTafsir');
            window.location.href = `index.html`;
        }
    });

    // Render informasi surah dan tafsir setelah halaman dimuat
    renderSurahDetails();
    renderTafsir();
});
