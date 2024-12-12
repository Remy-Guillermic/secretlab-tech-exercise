<?php

namespace App\Http\Controllers;

use App\DTO\VersionQueryData;
use App\Http\Requests\ShowVersionRequest;
use App\Http\Requests\StoreVersionRequest;
use App\Services\VersionService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class VersionControlController extends Controller
{
    public function __construct(protected VersionService $versionService)
    {
    }

    public function index(): JsonResponse
    {
        $versions = $this->versionService->getAllVersionsGroupedByKey();

        return response()->json($versions);
    }

    public function show(ShowVersionRequest $request, int|string $key): JsonResponse
    {
        $queryData = new VersionQueryData($key, $request->query('timestamp'));

        $version = $this->versionService->getVersionByKeyAndTimestamp($queryData);

        return response()->json($version);
    }

    public function store(StoreVersionRequest $request): JsonResponse
    {
        $this->versionService->storeVersions($request->collect());

        return response()->json(status: Response::HTTP_CREATED);
    }
}