<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
                        ]
                        );
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'code' => 500,
                        'message' => "Failed to fetch data Ayat for surah {$nomor} from external API: " . $e->getMessage()
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

    public function importJadwalsholat(Request $request) {
        
        $client = new Client();
        
        
        $ipResponse = $client->get('http://ip-api.com/json');
        $location = json_decode($ipResponse->getBody(), true);

        $city = $location['city'];
        $country = $location['countryCode'];
        $timezone = new \DateTimeZone($location['timezone']);
        
        $year = $request->year;

        for ($month = 1; $month <= 12; $month++) {
            $response = $client->get("http://api.aladhan.com/v1/calendarByCity/{$year}/{$month}?city={$city}&country={$country}");
            $data = json_decode($response->getBody(), true);

            foreach ($data['data'] as $day) {
                
                $date = \DateTime::createFromFormat('d-m-Y', $day['date']['gregorian']['date']);
                if ($date) {
                    $formattedDate = $date->format('Y-m-d');

                    $fajr = new \DateTime($day['timings']['Fajr']);
                    $fajr->setTimezone($timezone);

                    $dhuhr = new \DateTime($day['timings']['Dhuhr']);
                    $dhuhr->setTimezone($timezone);

                    $asr = new \DateTime($day['timings']['Asr']);
                    $asr->setTimezone($timezone);

                    $maghrib = new \DateTime($day['timings']['Maghrib']);
                    $maghrib->setTimezone($timezone);

                    $isha = new \DateTime($day['timings']['Isha']);
                    $isha->setTimezone($timezone);
                    
                    Jadwalsholat::updateOrCreate(
                        ['date' => $formattedDate],
                        [
                            'fajr' => $fajr->format('H:i'),
                            'dhuhr' => $dhuhr->format('H:i'),
                            'asr' => $asr->format('H:i'),
                            'maghrib' => $maghrib->format('H:i'),
                            'isha' => $isha->format('H:i'),
                        ]
                    );
                } else {
                    
                    continue;
                }
            }
        }

        return response()->json(['message' => 'Prayer times imported successfully for the entire year']);
    }

    public function jadwalsholat(){
        
        $jadwalsholat = Jadwalsholat::all();

        return response()->json([
            'code' => 200,
            'message' => 'Data jadwal sholat retrieved successfully',
            'data' => JadwalsholatResource::collection($jadwalsholat),
        ]);
    }

    public function jadwalsholatYM($year) {
        
        $jadwalsholat = Jadwalsholat::whereYear('date', $year)
        ->get();

        return response()->json([
            'code' => 200,
            'message' => 'Data jadwal sholat retrieved successfully',
            'data' => JadwalsholatResource::collection($jadwalsholat),
        ]);
    }

    public function jadwalsholatM($year, $month) {
        
        $jadwalsholat = Jadwalsholat::whereYear('date', $year)
        ->whereMonth('date', $month)
        ->get();

        return response()->json([
            'code' => 200,
            'message' => 'Data jadwal sholat retrieved successfully',
            'data' => JadwalsholatResource::collection($jadwalsholat),
        ]);
    }
}
