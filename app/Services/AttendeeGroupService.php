<?php

namespace App\Services;

use App\Models\AttendeeGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AttendeeGroupService
{
  public function getAllAttendeeGroups(
		int $perPage, 
		int $page
	): LengthAwarePaginator {
    return AttendeeGroup::paginate($perPage, ['*'], 'page', $page);
  }

  public function createAttendeeGroup(array $data): AttendeeGroup
  {
    return AttendeeGroup::create([
      'name' => $data['name'],
    ]);
  }

  public function getAttendeeGroupById(int $id): AttendeeGroup
  {
    return AttendeeGroup::findOrFail($id);
  }

  public function updateAttendeeGroup(
		array $data, 
		AttendeeGroup $attendeeGroup
	): AttendeeGroup {
    $attendeeGroup->update([
      'name' => $data['name'],
    ]);

    return $attendeeGroup;
  }

  public function deleteAttendeeGroup(AttendeeGroup $attendeeGroup): bool
  {
    return $attendeeGroup->delete();
  }
}
