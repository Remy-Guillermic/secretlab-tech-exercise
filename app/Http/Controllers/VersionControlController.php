<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowVersionRequest;
use App\Http\Requests\StoreVersionRequest;
use App\Models\Version;
use Carbon\Carbon;
use Illuminate\Http\Response;

class VersionControlController extends Controller
{
    public function index(): ?string
    {
        return Version::query()
            ->defaultSelect()
            ->latest('timestamp')
            ->get()
            ->groupBy('key')
            ->map(function ($items) {
                return $items->pluck('value')->all();
            })
            ->toJson();
    }

    public function show(ShowVersionRequest $request, int|string $key): ?string
    {
        return Version::query()
            ->defaultSelect()
            ->where('key', $key)
            ->when($request->has('timestamp'), function ($query) use ($request) {

                $carbonDateTime = Carbon::createFromTimestampUTC($request->query('timestamp'));

                $query->where('timestamp', '<=', $carbonDateTime);

            })
            ->latest('timestamp')
            ->limit(1)
            ->get()
            ->values()
            ->pluck('value', 'key')
            ->toJson();
    }

    public function store(StoreVersionRequest $request): Response
    {
        $data = $request
            ->collect()
            ->mapWithKeys(function ($value, $key) {
                return [
                    'key' => $key,
                    'value' => $value,
                    'timestamp' => now('UTC'),
                ];
            })->toArray();

        Version::insert($data);

        return response(status: 201);
    }
}
