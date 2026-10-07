<?php

namespace App\Services;

use App\Models\CoveragePoint;

class CoverageService
{
    /**
     * Check a lat/lng against every active coverage point and return the nearest one,
     * plus whether that point's radius actually covers the location.
     *
     * @return array{covered: bool, distance_km: float|null, point: CoveragePoint|null}
     */
    public function check(float $lat, float $lng): array
    {
        $nearest = null;
        $nearestDistance = null;

        foreach (CoveragePoint::where('is_active', true)->get() as $point) {
            $distance = $point->distanceTo($lat, $lng);

            if ($nearestDistance === null || $distance < $nearestDistance) {
                $nearestDistance = $distance;
                $nearest = $point;
            }
        }

        return [
            'covered' => $nearest !== null && $nearestDistance <= $nearest->radius_km,
            'distance_km' => $nearestDistance !== null ? round($nearestDistance, 2) : null,
            'point' => $nearest,
        ];
    }
}
