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
    
    public function importAyat() {
        
        $client = new Client([
            'timeout' => 7200,
            'connect_timeout' => 7200,
            'retry' => 10,
        ]);
        
        for ($nomor = 1; $nomor <= 114; $nomor++) {
            $url = "https://equran.id/api/v2/surat/{$nomor}"; 
            $response = Http::get($url);
            
            if ($response->successful()) {
                $data = $response->json()['data']['ayat'];
                
                foreach ($data as $ayat) {
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
                return response()->json([
                    'code' => 500,
                    'message' => "Failed to fetch data ayat for surah {$nomor} from external API"
                ], 500);
            }
        } 
    
        return response()->json([
            'code' => 200,
            'message' => 'All ayat data retrieved successfully'
        ]);
    }
    
    

    public function ayat($nomor) {
        $ayat = Ayat::where('nomor_surah', $nomor)->get();
            return response()->json([
               'code' => 200,
               'message' => 'Data ayat retrieved successfully',
               'data' => AyatResource::collection($ayat),
            ]);
    }

    public function importTafsir() {

            $client = new Client([
                'timeout' => 7200,
                'connect_timeout' => 7200,
               'retry' => 10,
            ]);

            for ($nomor = 1; $nomor <= 114; $nomor++) {
                $url = "https://equran.id/api/v2/tafsir/{$nomor}"; 
                $response = Http::get($url);

                if ($response->successful()) {
                    $data = $response->json()['data']['tafsir'];

                    foreach ($data as $ayat) {
                        Tafsir::updateOrCreate([
                            'nomor_surah' => $nomor,
                            'nomor_ayat' => $ayat['ayat'],
                        ],
                        [
                            'teks_tafsir' => $ayat['teks'],
                        ]
                    );
                }
            } else {
                return response()->json([
                    'code' => 500,
                    'message' => 'Failed to fetch data tafsir for surah {$nomor} from external API'
                ], 500);
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'All tafsir data retrieved successfully'
        ]);
    }

    public function tafsir($nomor) {
        
        $tafsir = Tafsir::where('nomor_surah', $nomor)->get();

        return response()->json([
            'code' => 200,
            'message' => 'Data tafsir retrieved successfully',
            'data' => TafsirResource::collection($tafsir),
        ]);
    }
}