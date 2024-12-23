<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JadwalsholatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'gregorian_date' => $this->gregorian_date,
            'gregorian_weekday_en' => $this->gregorian_weekday_en,
            'gregorian_day' => $this->gregorian_day,
            'gregorian_month' => $this->gregorian_month,
            'gregorian_year' => $this->gregorian_year,
            'hijri_date' => $this->hijri_date,
            'hijri_weekday_en' => $this->hijri_weekday_en,
            'hijri_day' => $this->hijri_day,
            'hijri_month' => $this->hijri_month,
            'hijri_year' => $this->hijri_year,
            'imsak' => $this->imsak,
            'fajr' => $this->fajr,
            'sunrise' => $this->sunrise,
            'dhuhr' => $this->dhuhr,
            'asr' => $this->asr,
            'maghrib' => $this->maghrib,
            'isha' => $this->isha,
        ];
    }
}
