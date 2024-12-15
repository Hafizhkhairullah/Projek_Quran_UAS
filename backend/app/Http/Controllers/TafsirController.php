<?php

namespace App\Http\Controllers;

use App\Models\Tafsir;
use App\Http\Resources\TafsirResource;
use Illuminate\Support\Facades\Http;

class TafsirController extends Controller
{
    public function index()
    {
        $client = new \GuzzleHttp\Client([
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
                    Tafsir::updateOrCreate(
                        [
                            'nomor_surat' => $nomor,         
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
                    'message' => "Failed to fetch data for surah {$nomor} from external API"
                ], 500);
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'All tafsir data retrieved and stored successfully'
        ]);
    }

    public function tafsir($nomor) {
        $tafsir = Tafsir::where('nomor_surat', $nomor)->get();

        return response()->json([
            'code' => 200,
            'message' => 'Data retrieved successfully',
            'data' => TafsirResource::collection($tafsir),
        ]);
    }
}
