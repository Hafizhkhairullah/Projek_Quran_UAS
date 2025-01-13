document.addEventListener("DOMContentLoaded", () => {
    const ayatListContainer = document.getElementById("ayat-list");
    const surahTitle = document.getElementById("surah-title");
    const surahInfo = document.getElementById("surah-info");
    const params = new URLSearchParams(window.location.search);
    const surahNomor = params.get("surah");
    const ayatApiEndpoint = `https://639e-103-171-83-15.ngrok-free.app/api/surah/${surahNomor}`;
    const surahApiEndpoint = "https://639e-103-171-83-15.ngrok-free.app/api/surah";
    const audio = new Audio();
    let isPlaying = false;

    const lihatTafsirBtn = document.getElementById("tafsir-btn");
    if (lihatTafsirBtn) {
        lihatTafsirBtn.addEventListener("click", () => {
            window.location.href = `tafsir.html?surah=${surahNomor}`;
        });
    }

    function renderAyatList() {
        console.log("Fetching ayat data from:", ayatApiEndpoint); // Log endpoint
        fetch(ayatApiEndpoint, {
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
            console.log("Ayat data received:", data); // Log received data
            ayatListContainer.innerHTML = "";
            if (data && data.data) {
                data.data.forEach(ayat => {
                    const ayatElement = document.createElement("div");
                    ayatElement.className = "ayat";
                    ayatElement.innerHTML = `
                        <p><strong>${ayat.nomor_ayat}</strong></p>
                        <p class="arabic-text">${ayat.teks_arab}</p>
                        <p class="text-latin">${ayat.teks_latin}</p>
                        <p class="text-indonesia">${ayat.teks_terjemahan}</p>`;
                    ayatListContainer.appendChild(ayatElement);
                });
            } else {
                console.error("Invalid ayat data structure:", data);
            }
        })
        .catch(error => console.error("Error fetching ayat data:", error));
    }

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
                surahTitle.textContent = surah.nama_latin || "Surah Tidak Ditemukan";
                surahInfo.textContent = `${(surah.tempat_turun || "Tidak Diketahui").toUpperCase()} • ${surah.jumlah_ayat || "0"} Ayat • ${surah.arti || "Arti tidak tersedia"}`;

                try {
                    const parsedAudio = JSON.parse(JSON.parse(surah.audio_full)); // Parsing dua kali karena nested escape
                    if (parsedAudio && parsedAudio["05"]) { // Ganti dengan kunci sesuai preferensi audio (misalnya "05")
                        audio.src = parsedAudio["05"];
                        console.log("Audio source set to:", audio.src);
                    } else {
                        console.warn("Audio source not found in parsedAudio.");
                    }
                } catch (error) {
                    console.error("Error parsing audio_full:", error);
                }
            } else {
                console.error("Surah tidak ditemukan untuk nomor:", surahNomor);
                surahTitle.textContent = "Surah Tidak Ditemukan";
                surahInfo.textContent = "Informasi tidak tersedia";
            }
        })
        .catch(error => console.error("Error fetching surah data:", error));
    }

    document.getElementById("audio-btn").addEventListener("click", () => {
        if (isPlaying) {
            audio.pause();
            document.getElementById("audio-btn").innerHTML = '<i class="fas fa-play"></i> PUTAR AUDIO';
            localStorage.setItem("audioTime", audio.currentTime);
        } else {
            const savedTime = parseFloat(localStorage.getItem("audioTime")) || 0;
            if (savedTime && savedTime > 0) {
                audio.currentTime = savedTime;
            }
            audio.play();
            document.getElementById("audio-btn").innerHTML = '<i class="fas fa-pause"></i> PAUSE AUDIO';
        }
        isPlaying = !isPlaying;
    });

    audio.addEventListener("ended", () => {
        document.getElementById("audio-btn").innerHTML = '<i class="fas fa-play"></i> PUTAR AUDIO';
        isPlaying = false;
        localStorage.removeItem("audioTime");
    });

    window.addEventListener("beforeunload", () => {
        if (!audio.paused) {
            localStorage.setItem("audioTime", audio.currentTime);
        }
    });

    renderAyatList();
    renderSurahDetails();
});
