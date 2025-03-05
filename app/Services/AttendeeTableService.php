<?php

namespace App\Services;

use App\Models\AttendeeTable;
use Illuminate\Database\Eloquent\Collection;

class AttendeeTableService
{
  public function getAllAttendeeTables(int $eventId): Collection
  {
    return AttendeeTable::where('event_id', $eventId)
      ->with([
        'attendee',
        'attendee.company',
        'attendee.attendeeGroup',
        'attendee.events' => function ($query) use ($eventId) {
          $query->where('event_id', $eventId)
					->where('confirmed', 1);
        },
      ])
      ->get()
      ->filter(function ($attendeeTable) {
        return $attendeeTable->attendee->events->contains(
					function ($event) {
          	return $event->pivot->confirmed == 1;
        });
      })
      ->values();
  }

  public function addAttendeeToTable(array $data): AttendeeTable
  {
    return AttendeeTable::create($data);
  }

  public function removeAttendeeFromTable(array $data): bool
  {
    return AttendeeTable::where('attendee_id', $data['attendee_id'])
      ->where('table_id', $data['table_id'])
      ->where('event_id', $data['event_id'])
      ->delete();
  }
}
