<?php

namespace App\Services;

use App\DTO\VersionQueryData;
use App\Models\Version;
use Illuminate\Support\Collection;

class VersionService
{
    public function getAllVersionsGroupedByKey(): Collection
    {
        return Version::query()
            ->defaultSelect()
            ->latest('timestamp')
            ->get()
            ->groupBy('key')
            ->map(function ($items) {
                return $items->pluck('value')->all();
            });
    }

    public function getVersionByKeyAndTimestamp(VersionQueryData $versionData): Collection
    {
        return Version::query()
            ->defaultSelect()
            ->where('key', $versionData->getKey())
            ->when($versionData->hasTimestamp(), function ($query) use ($versionData) {
                $query->where('timestamp', '<=', $versionData->getCaronDateTimeFromTimestamp());
            })
            ->latest('timestamp')
            ->limit(1)
            ->get()
            ->pluck('value', 'key');
    }

    public function storeVersions(Collection $data): void
    {
        $versions = $data
            ->mapWithKeys(function ($value, $key) {
                return [
                    'key' => $key,
                    'value' => $value,
                    'timestamp' => now('UTC'),
                ];
            })->toArray();

        Version::insert($versions);
    }
}