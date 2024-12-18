<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Surah;
use App\Http\Resources\SurahResource;

class AlquranController extends Controller
{
    public function surah() {
        
        $response = Http::get('https://equran.id/api/v2/surat');

        if ($response->successful()) {
            $data = $response->json()['data'];

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

            $surah = Surah::all();

            return response()->json([
                'code' => 200,
                'message' => 'Data surah retrieved successfully',
                'data' => SurahResource::collection($surah),
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to fetch data surah from external API'
            ], 500);
        }
    }
}
