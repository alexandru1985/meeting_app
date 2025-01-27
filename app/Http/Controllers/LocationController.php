<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Location;
use Illuminate\Http\Response;
use App\Http\Requests\LocationRequest;
use Illuminate\Http\Request;
use App\Services\LocationService;

class LocationController extends Controller
{
	public function __construct(
		private LocationService $locationService
	) {
	}

	public function index(Request $request): JsonResponse
	{
		$perPage = 10;  
		$page = $request->get('page', 1);  
		$locations = $this->locationService
										->getAllLocations($perPage, $page);

		return response()->json($locations, Response::HTTP_OK);
	}

	public function store(LocationRequest $request): JsonResponse
	{
		$validated = $request->validated();
		$location = $this->locationService->createLocation($validated);

		return response()->json($location, Response::HTTP_CREATED);
	}

	public function show(Location $location): JsonResponse
	{
		$location = $this->locationService
										->getLocationById($location->id);

		return response()->json($location, Response::HTTP_OK);
	}

	public function update(
		LocationRequest $request, 
		Location $location
	): JsonResponse {
		$validated = $request->validated();
		$updatedLocation = $this->locationService
														->updateLocation($validated, $location);

		return response()->json($updatedLocation, Response::HTTP_OK);
	}

	public function destroy(Location $location): JsonResponse
	{
		$this->locationService->deleteLocation($location);
		
		return response()->json(null, Response::HTTP_NO_CONTENT);
	}
}
