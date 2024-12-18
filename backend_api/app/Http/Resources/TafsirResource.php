<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TafsirResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nomor_surah' => $this->nomor_surah,
            'nomor_ayat' => $this->nomor_ayat,
            'teks_tafsir' => $this->teks_tafsir,
        ];
    }
}
