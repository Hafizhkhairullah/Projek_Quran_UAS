<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Http\Resources\SuratResource;
use Illuminate\Support\Facades\Http;

class SuratController extends Controller
{
    public function index()
    {
        
        $response = Http::get('https://equran.id/api/v2/surat');

        
        if ($response->successful()) {
            $data = $response->json()['data'];  

            
            foreach ($data as $item) {
                Surat::updateOrCreate(
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

            
            $surat = Surat::all();


            return response()->json([
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => SuratResource::collection($surat),
            ]);
        } else {

            return response()->json([
                'code' => 500,
                'message' => 'Failed to fetch data from external API'
            ], 500);
        }
    }
}
