<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocationService
{
  public function getAllLocations(
		int $perPage, 
		int $page
	): LengthAwarePaginator {
    return Location::paginate($perPage, ['*'], 'page', $page);
  }

  public function createLocation(array $data): Location
  {
    return Location::create([
      'name' => $data['name'],
      'address' => $data['address'],
      'latitude' => $data['latitude'],
      'longitude' => $data['longitude'],
    ]);
  }

  public function getLocationById(int $id): Location
  {
    return Location::findOrFail($id);
  }

  public function updateLocation(array $data, Location $location): Location
  {
    $location->update([
      'name' => $data['name'],
      'address' => $data['address'],
      'latitude' => $data['latitude'],
      'longitude' => $data['longitude'],
    ]);

    return $location;
  }

  public function deleteLocation(Location $location): bool
  {
    return $location->delete();
  }
}
