<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Surah;
use App\Http\Resources\SurahResource;

class SurahController extends Controller
{
    public function index()
    {
        // Mengambil data dari API eksternal
        $response = Http::get('https://equran.id/api/v2/surat');

        if ($response->successful()) {
            // Memastikan data dari API tidak kosong dan sesuai dengan format yang diharapkan
            $data = $response->json()['data'];

            // Simpan setiap surah ke dalam database
            foreach ($data as $item) {
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

            // Ambil semua surah dari database
            $surah = Surah::all();

            // Kembalikan data surah sebagai respons JSON
            return response()->json([
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => SurahResource::collection($surah),
            ]);
        } else {
            // Kembalikan respons kesalahan jika gagal mengambil data dari API eksternal
            return response()->json([
                'code' => 500,
                'message' => 'Failed to fetch data from external API'
            ], 500);
        }
    }
}
