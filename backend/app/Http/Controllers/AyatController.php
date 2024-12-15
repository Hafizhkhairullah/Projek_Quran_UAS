<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ayat;
use App\Http\Resources\AyatResource;
use Illuminate\Support\Facades\Http;

class AyatController extends Controller
{
    public function index()
    {
        $client = new \GuzzleHttp\Client([
            'timeout' => 7200,
            'connect_timeout' => 6000,  
            'retry' => 10,
        ]);

        for ($nomor = 1; $nomor <= 114; $nomor++) {
            $url = "https://equran.id/api/v2/surat/{$nomor}"; 
            try {
                $response = $client->get($url);
                $data = json_decode($response->getBody()->getContents(), true)['data'];  
                $ayatData = $data['ayat'];  

               
                foreach ($ayatData as $ayat) {
                    Ayat::updateOrCreate(
                        [
                            'nomor_surat' => $nomor,
                            'nomor_ayat' => $ayat['nomorAyat'],
                        ],
                        [
                            'teks_arab' => $ayat['teksArab'],
                            'teks_latin' => $ayat['teksLatin'],
                            'teks_terjemahan' => $ayat['teksIndonesia'],
                        ]
                    );
                }
            } catch (\Exception $e) {
                return response()->json([
                    'code' => 500,
                    'message' => "Failed to fetch data for surah {$nomor} from external API: " . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'All surah data retrieved and stored successfully'
        ]);
    }

    public function ayat($nomor) {
        $ayat = Ayat::where('nomor_surat', $nomor)->get();
        return response()->json([
            'code' => 200,
            'message' => 'Data retrieved successfully',
            'data' => AyatResource::collection($ayat),
        ]);
    }
}
