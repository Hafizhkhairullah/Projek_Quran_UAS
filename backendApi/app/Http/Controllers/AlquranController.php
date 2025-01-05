<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\Surah;
use App\Models\Ayat;
use App\Models\Tafsir;
use App\Http\Resources\SurahResource;
use App\Http\Resources\AyatResource;
use App\Http\Resources\TafsirResource;

class AlquranController extends Controller
{
    // Function untuk mendapatkan data surah dari API eksternal dan menyimpannya ke database
    public function surah() {
        // Mengirim permintaan GET ke API eksternal
        $response = Http::get('https://equran.id/api/v2/surat');
        
        // Memeriksa apakah permintaan berhasil
        if ($response->successful()) {
            $data = $response->json()['data'];
            
            // Loop melalui setiap item data surah
            foreach ($data as $item) {
                // Memperbarui atau membuat data surah di database
                Surah::updateOrCreate(
                    ['nomor' => $item['nomor']],
                    [
                        'nama' => $item['nama'],
                        'nama_latin' => $item['namaLatin'],
                        'arti' => $item['arti'],
                        'jumlah_ayat' => $item['jumlahAyat'],
                        'tempat_turun' => $item['tempatTurun'],
                        'audio_full' => json_encode($item['audioFull']),
                    ]
                );
            }
            
            // Mengambil semua data surah dari database
            $surah = Surah::all();
            
            // Mengembalikan respon JSON dengan data surah
            return response()->json([
                'code' => 200,
                'message' => 'Data surah retrieved successfully',
                'data' => SurahResource::collection($surah),
            ]);
        } else {
            // Mengembalikan respon JSON jika permintaan gagal
            return response()->json([
                'code' => 500,
                'message' => 'Failed to fetch data surah from external API'
            ], 500);
        }
    }

    // Function untuk mengimpor data ayat dari API eksternal dan menyimpannya ke database
    public function importAyat() {
        // Membuat instance client Guzzle
        $client = new Client([
            'timeout' => 7200,
            'connect_timeout' => 7200,
            'retry' => 10,
        ]);
        
        // Loop melalui setiap nomor surah
        for ($nomor = 1; $nomor <= 114; $nomor++) {
            $url = "https://equran.id/api/v2/surat/{$nomor}";
            $response = Http::get($url);
            
            // Memeriksa apakah permintaan berhasil
            if ($response->successful()) {
                $data = $response->json()['data']['ayat'];
                
                // Loop melalui setiap data ayat
                foreach ($data as $ayat) {
                    // Memperbarui atau membuat data ayat di database
                    Ayat::updateOrCreate([
                        'nomor_surah' => $nomor,
                        'nomor_ayat' => $ayat['nomorAyat'],
                    ],
                    [
                        'teks_arab' => $ayat['teksArab'],
                        'teks_latin' => $ayat['teksLatin'],
                        'teks_terjemahan' => $ayat['teksIndonesia'],
                        'audio' => json_encode($ayat['audio']),
                    ]);
                }
            } else {
                // Mengembalikan respon JSON jika permintaan gagal
                return response()->json([
                    'code' => 500,
                    'message' => "Failed to fetch data ayat for surah {$nomor} from external API"
                ], 500);
            }
        }

        // Mengembalikan respon JSON jika semua data ayat berhasil diambil
        return response()->json([
            'code' => 200,
            'message' => 'All ayat data retrieved successfully'
        ]);
    }

    // Fungsi untuk mendapatkan data ayat berdasarkan nomor surah
    public function ayat($nomor) {
        $ayat = Ayat::where('nomor_surah', $nomor)->get();
        return response()->json([
            'code' => 200,
            'message' => 'Data ayat retrieved successfully',
            'data' => AyatResource::collection($ayat),
        ]);
    }

    // Fungsi untuk mengimpor data tafsir dari API eksternal dan menyimpannya ke database
    public function importTafsir() {
        // Membuat instance client Guzzle
        $client = new Client([
            'timeout' => 7200,
            'connect_timeout' => 7200,
            'retry' => 10,
        ]);

        // Loop melalui setiap nomor surah
        for ($nomor = 1; $nomor <= 114; $nomor++) {
            $url = "https://equran.id/api/v2/tafsir/{$nomor}";
            $response = Http::get($url);

            // Memeriksa apakah permintaan berhasil
            if ($response->successful()) {
                $data = $response->json()['data']['tafsir'];

                // Loop melalui setiap data tafsir ayat
                foreach ($data as $ayat) {
                    // Memperbarui atau membuat data tafsir di database
                    Tafsir::updateOrCreate([
                        'nomor_surah' => $nomor,
                        'nomor_ayat' => $ayat['ayat'],
                    ],
                    [
                        'teks_tafsir' => $ayat['teks'],
                    ]);
                }
            } else {
                // Mengembalikan respon JSON jika permintaan gagal
                return response()->json([
                    'code' => 500,
                    'message' => 'Failed to fetch data tafsir for surah {$nomor} from external API'
                ], 500);
            }
        }

        // Mengembalikan respon JSON jika semua data tafsir berhasil diambil
        return response()->json([
            'code' => 200,
            'message' => 'All tafsir data retrieved successfully'
        ]);
    }

    // Fungsi untuk mendapatkan data tafsir berdasarkan nomor surah
    public function tafsir($nomor) {
        $tafsir = Tafsir::where('nomor_surah', $nomor)->get();
        return response()->json([
            'code' => 200,
            'message' => 'Data tafsir retrieved successfully',
            'data' => TafsirResource::collection($tafsir),
        ]);
    }

    // Fungsi untuk mendapatkan data tafsir berdasarkan nomor surah dan nomor ayat
    public function tafsirAyat($nomorSurah, $nomorAyat) {
        $tafsir = Tafsir::where('nomor_surah', $nomorSurah)
                        ->where('nomor_ayat', $nomorAyat)
                        ->get();

        return response()->json([
            'code' => 200,
            'message' => 'Data tafsir retrieved successfully',
            'data' => TafsirResource::collection($tafsir),
        ]);
    }
}
