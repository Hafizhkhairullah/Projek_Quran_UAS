<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\Surah;
use App\Models\Ayat;
use App\Models\Tafsir;
use App\Models\Jadwalsholat;
use App\Http\Resources\SurahResource;
use App\Http\Resources\AyatResource;
use App\Http\Resources\TafsirResource;
use App\Http\Resources\JadwalSholatResource;

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

        $failedSurah = [];  

        for ($nomor = 1; $nomor <= 114; $nomor++) {
            $url = "https://equran.id/api/v2/surat/{$nomor}";
            try {
                $response = $client->get($url);
                $data = json_decode($response->getBody()->getContents(), true)['data'];
                $ayatData = $data['ayat'];

                foreach ($ayatData as $ayat) {
                    Ayat::updateOrCreate(
                        [
                            'nomor_surah' => $nomor,
                            'nomor_ayat' => $ayat['nomorAyat'],
                        ],
                        [
                            'teks_arab' => $ayat['teksArab'],
                            'teks_latin' => $ayat['teksLatin'],
                            'teks_terjemahan' => $ayat['teksIndonesia'],
                            'audio' => json_encode($ayat['audio']),
                        ]
                        );
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to fetch data Ayat for surah {$nomor} from external API: " . $e->getMessage());
                    $failedSurah[] = $nomor;
                }
            } 

            if (empty($failedSurahs)) { 
                return response()->json([ 
                    'code' => 200, 
                    'message' => 'All ayat data retrieved successfully' 
                ]);
        } else {
            return response()->json([ 
                'code' => 206, 
                'message' => 'Some ayat data failed to be retrieved', 
                'failedSurahs' => $failedSurahs 
            ]); 
        } 
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

    public function importJadwalsholat(Request $request) {

        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');
        $years = $request->query('years');

        if (empty($latitude) || empty($longitude) || empty($years)) {
            return response()->json([
                'error' => 'Latitude, longitude, dan tahun wajib diisi.'
            ], 400);
        }

        $years = explode(',', $years);
        sort($years);
        
        $client = new Client();
        
        try { 
            foreach ($years as $year) { 
                for ($month = 1; $month <= 12; $month++) { 
                    $response = $client->request('GET', "http://api.aladhan.com/v1/calendar?latitude={$latitude}&longitude={$longitude}&method=2&month={$month}&year={$year}"); 
                    
                    if ($response->getStatusCode() == 200) { 
                        $body = $response->getBody()->getContents(); 
                        $data = json_decode($body, true); 
                        Log::info('Response body for year ' . $year . ', month ' . $month . ': ' . print_r($data, true)); 
                        
                        if (is_array($data) && isset($data['data'])) { 
                            foreach ($data['data'] as $jadwal) { 
                                Log::info('Data jadwal: ' . print_r($jadwal, true)); 
                                
                                JadwalSholat::create([ 
                                    'latitude' => $latitude, 
                                    'longitude' => $longitude, 
                                    'gregorian_date' => $jadwal['date']['gregorian']['date'], 
                                    'gregorian_weekday_en' => $jadwal['date']['gregorian']['weekday']['en'], 
                                    'gregorian_day' => $jadwal['date']['gregorian']['day'], 
                                    'gregorian_month' => $jadwal['date']['gregorian']['month']['en'], 
                                    'gregorian_year' => $jadwal['date']['gregorian']['year'], 
                                    'hijri_date' => $jadwal['date']['hijri']['date'], 
                                    'hijri_weekday_en' => $jadwal['date']['hijri']['weekday']['en'], 
                                    'hijri_day' => $jadwal['date']['hijri']['day'], 
                                    'hijri_month' => $jadwal['date']['hijri']['month']['en'], 
                                    'hijri_year' => $jadwal['date']['hijri']['year'], 
                                    'imsak' => $jadwal['timings']['Imsak'], 
                                    'fajr' => $jadwal['timings']['Fajr'], 
                                    'sunrise' => $jadwal['timings']['Sunrise'], 
                                    'dhuhr' => $jadwal['timings']['Dhuhr'], 
                                    'asr' => $jadwal['timings']['Asr'], 
                                    'maghrib' => $jadwal['timings']['Maghrib'], 
                                    'isha' => $jadwal['timings']['Isha'] 
                                ]); 
                                
                                Log::info('Data berhasil disimpan untuk tanggal: ' . $jadwal['date']['gregorian']['date']); 
                            } 
                        } else { 
                            Log::error("Data tidak sesuai atau kosong untuk bulan: {$month}, tahun: {$year}."); 
                        } 
                    } else { 
                        Log::error("Request gagal untuk bulan: {$month}, tahun: {$year} dengan status: " . $response->getStatusCode()); 
                    } 
                } 
            } 
            
            return response()->json([ 
                'message' => 'Data jadwal sholat berhasil diimpor untuk tahun ' . implode(',', $years) ]); 
            } catch (\Exception $e) { 
                Log::error('Exception during import: ' . $e->getMessage()); 
                return response()->json([ 
                    'error' => 'Terjadi kesalahan saat mengimpor data. Silakan coba lagi nanti.' 
                ], 500); 
            }
    }

    public function JadwalsholatYear(Request $request) {

        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');
        $year = $request->query('year');
 
        if (empty($latitude) || empty($longitude) || empty($year)) {
            return response()->json([
                'error' => 'Latitude, longitude, dan tahun wajib diisi.'
            ], 400);
        }
        
        try { 
         
         Log::info("Menerima parameter - Latitude: {$latitude}, Longitude: {$longitude}, Tahun: {$year}"); 
         
         $jadwalSholat = JadwalSholat::where('latitude', $latitude) 
         ->where('longitude', $longitude) 
         ->whereRaw('YEAR(STR_TO_DATE(gregorian_date, "%d-%m-%Y")) = ?', [$year])
         ->get();
         
         $response = $jadwalSholat->map(function ($item) { 
             return [ 
                 'latitude' => $item->latitude, 
                 'longitude' => $item->longitude, 
                 'gregorian_weekday_en' => $item->gregorian_weekday_en, 
                 'gregorian_day' => $item->gregorian_day, 
                 'gregorian_month' => $item->gregorian_month, 
                 'gregorian_year' => $item->gregorian_year, 
                 'hijri_date' => $item->hijri_date, 
                 'hijri_weekday_en' => $item->hijri_weekday_en, 
                 'hijri_day' => $item->hijri_day, 
                 'hijri_month' => $item->hijri_month, 
                 'hijri_year' => $item->hijri_year, 
                 'imsak' => $item->imsak, 
                 'fajr' => $item->fajr, 
                 'sunrise' => $item->sunrise, 
                 'dhuhr' => $item->dhuhr, 
                 'asr' => $item->asr, 
                 'maghrib' => $item->maghrib, 
                 'isha' => $item->isha, 
             ]; 
         });
         
         Log::info("Hasil query: " . $jadwalSholat->toJson()); 
         
         return response()->json(['data' => $response]);
     } catch (\Exception $e) { 
         
         Log::error('Exception during fetching data: ' . $e->getMessage()); 
         return response()->json([ 
             'error' => 'Terjadi kesalahan saat mengambil data. Silakan coba lagi nanti.' 
         ], 500); 
     } 
    }

    public function Jadwalsholat(Request $request) {

       $latitude = $request->query('latitude');
       $longitude = $request->query('longitude');
       $month = $request->query('month');
       $year = $request->query('year');

       if (empty($latitude) || empty($longitude) || empty($month) || empty($year)) {
           return response()->json([
               'error' => 'Latitude, longitude, bulan, dan tahun wajib diisi.'
           ], 400);
       }
       
       try { 
        
        Log::info("Menerima parameter - Latitude: {$latitude}, Longitude: {$longitude}, Bulan: {$month}, Tahun: {$year}"); 
        
        $jadwalSholat = JadwalSholat::where('latitude', $latitude) 
        ->where('longitude', $longitude) 
        ->whereRaw('MONTH(STR_TO_DATE(gregorian_date, "%d-%m-%Y")) = ?', [$month]) 
        ->whereRaw('YEAR(STR_TO_DATE(gregorian_date, "%d-%m-%Y")) = ?', [$year])
        ->get();
        
        $response = $jadwalSholat->map(function ($item) { 
            return [ 
                'latitude' => $item->latitude, 
                'longitude' => $item->longitude, 
                'gregorian_weekday_en' => $item->gregorian_weekday_en, 
                'gregorian_day' => $item->gregorian_day, 
                'gregorian_month' => $item->gregorian_month, 
                'gregorian_year' => $item->gregorian_year, 
                'hijri_date' => $item->hijri_date, 
                'hijri_weekday_en' => $item->hijri_weekday_en, 
                'hijri_day' => $item->hijri_day, 
                'hijri_month' => $item->hijri_month, 
                'hijri_year' => $item->hijri_year, 
                'imsak' => $item->imsak, 
                'fajr' => $item->fajr, 
                'sunrise' => $item->sunrise, 
                'dhuhr' => $item->dhuhr, 
                'asr' => $item->asr, 
                'maghrib' => $item->maghrib, 
                'isha' => $item->isha, 
            ]; 
        });
        
        Log::info("Hasil query: " . $jadwalSholat->toJson()); 
        
        return response()->json(['data' => $response]); 
    } catch (\Exception $e) { 
        
        Log::error('Exception during fetching data: ' . $e->getMessage()); 
        return response()->json([ 
            'error' => 'Terjadi kesalahan saat mengambil data. Silakan coba lagi nanti.' 
        ], 500); 
    }
    }  
}
