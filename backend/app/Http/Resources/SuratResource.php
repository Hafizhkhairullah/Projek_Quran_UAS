<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuratResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray($request) {
    return [
        'nomor' => $this->nomor,
        'nama' => $this->nama,
        'nama_latin' => $this->nama_latin,
        'arti' => $this->arti,
        'jumlah_ayat' => $this->jumlah_ayat,
        'tempat_turun' => $this->tempat_turun,
        'audio_full' => json_encode($this->audio_full),
    ];
   }
}
