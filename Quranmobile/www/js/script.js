document.addEventListener("DOMContentLoaded", () => {
    const surahListContainer = document.getElementById("surah-list");
    const searchInput = document.getElementById("search-input");
    const apiEndpoint = "https://759d-103-171-83-15.ngrok-free.app/api/surah";

    let surahs = [];

    fetch(apiEndpoint, {
        mode: 'cors',
        headers: {
            'ngrok-skip-browser-warning': 'true',
            'User-Agent': 'CustomUserAgent/1.0'
        }
    })
        .then(response => {
            // Log response status and content type
            console.log("Response status:", response.status);
            console.log("Response content type:", response.headers.get("content-type"));
            if (response.headers.get("content-type").includes("application/json")) {
                return response.json();
            } else {
                return response.text().then(text => {
                    console.error("Non-JSON response received:", text);
                    throw new Error("Received non-JSON response");
                });
            }
        })
        .then(data => {
            console.log("Data received:", data); // Log received data
            if (data && data.data) {
                surahs = data.data;
                renderSurahList(surahs);
            } else {
                throw new Error("Invalid JSON structure");
            }
        })
        .catch(error => {
            console.error("Error fetching data:", error);
        });

    function renderSurahList(filteredSurahs) {
        surahListContainer.innerHTML = "";
        filteredSurahs.forEach(surah => {
            const surahElement = document.createElement("div");
            surahElement.className = "surah-item";

            surahElement.innerHTML = `
                <div class="surah-number">${surah.nomor}</div>
                <div class="surah-info-container">
                    <p class="surah-title">${surah.nama_latin}</p>
                    <p class="surah-info">${surah.tempat_turun.toUpperCase()} • ${surah.arti.toUpperCase()}</p>
                </div>
                <p class="surah-arabic">${surah.nama}</p>
            `;

            surahElement.addEventListener("click", () => {
                searchInput.value = ''; // Clear the search input when navigating away
                renderSurahList(surahs); // Reset the surah list
                window.location.href = `ayat.html?surah=${surah.nomor}`;
            });

            surahListContainer.appendChild(surahElement);
        });
    }

    function filterSurahs(query) {
        return surahs.filter(surah => surah.nama_latin.toLowerCase().includes(query));
    }

    searchInput.addEventListener("input", () => {
        const query = searchInput.value.toLowerCase().trim();
        const filteredSurahs = filterSurahs(query);
        renderSurahList(filteredSurahs);
    });

    const currentDate = new Date();
    const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    document.getElementById("day").textContent = days[currentDate.getDay()];
    document.getElementById("date").textContent = `${currentDate.getDate()} ${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
});
