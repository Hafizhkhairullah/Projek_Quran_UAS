<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AyatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'nomor_surah' => $this->nomor_surah,
            'nomor_ayat' => $this->nomor_ayat,
            'teks_arab' => $this->teks_arab,
            'teks_latin' => $this->teks_latin,
            'teks_terjemahan' => $this->teks_terjemahan,
        ];
    }
}
