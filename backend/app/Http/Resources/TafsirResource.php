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
    public function toArray($request): array
    {
        return [
        'nomor_surat' => $this->nomor_surat,
        'nomor_ayat' => $this->nomor_ayat,
        'teks_tafsir' => $this->teks_tafsir,
        ];
    }
}
