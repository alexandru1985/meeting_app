<?php

namespace App\Services;

use App\Models\Attendee;
use App\Models\AttendeeEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AttendeeService
{
  public function getAllAttendees(
		array $filters, 
		?int $eventId, 
		int $perPage = 10, 
		int $page = 1
	): LengthAwarePaginator {
    $query = Attendee::with([
      'company',
      'attendeeGroup',
      'events' => function ($q) use ($eventId) {
        $q->where('event_id', $eventId);
      },
    ])->where('id', '>', 1);

    if (isset($filters['name'])) {
      $query->where('name', 'like', '%' . $filters['name'] . '%');
    }

    if (isset($filters['companies'])) {
      $query->whereIn('company_id', $filters['companies']);
    }

    if (isset($filters['attendee_groups'])) {
      $query->whereIn('attendee_group_id', $filters['attendee_groups']);
    }

    if (isset($filters['confirmed'])) {
      $confirmed = $filters['confirmed'];
      $notConfirmed = in_array(0, $confirmed);
      $confirmed = in_array(1, $confirmed);
  
      if (!$notConfirmed || !$confirmed) {
          if ($notConfirmed) {
              $query->where(function ($query) use ($eventId) {
                  $query
                      ->whereDoesntHave('events', function ($q) use ($eventId) {
                          $q->where('event_id', $eventId);
                      })
                      ->orWhereHas('events', function ($q) use ($eventId) {
                          $q->where('event_id', $eventId)->where('confirmed', 0);
                      });
              });
          }
  
          if ($confirmed) {
              $query->whereHas('events', function ($q) use ($eventId) {
                  $q->where('attendee_event.confirmed', 1)
                    ->where('event_id', $eventId);
              });
          }
      }
    }
    
    return $query->paginate($perPage, ['*'], 'page', $page);
  }

  public function createAttendee(array $data): Attendee
  {
    $attendee = Attendee::create([
      'name' => $data['name'],
      'company_id' => $data['company_id'],
      'attendee_group_id' => $data['attendee_group_id'],
    ]);

    if (isset($data['confirmed'])) {
      $this->addAttendeeEvent(
				$attendee->id, $data['event_id'], 
				$data['confirmed']
			);
    }

    return $attendee;
  }

  public function getAttendeeById(int $id): Attendee
  {
    return Attendee::with('company', 'attendeeGroup')->findOrFail($id);
  }

  public function updateAttendee(int $id, array $data): Attendee
  {
    $attendee = Attendee::findOrFail($id);
    $attendee->update([
      'name' => $data['name'],
      'company_id' => $data['company_id'],
      'attendee_group_id' => $data['attendee_group_id'],
    ]);

    if (isset($data['confirmed'])) {
      $this->addAttendeeEvent(
				$attendee->id, 
				$data['event_id'], 
				$data['confirmed']
			);
    }

    return $attendee;
  }

  public function deleteAttendee(int $id): bool
  {
    $attendee = Attendee::findOrFail($id);

    return $attendee->delete();
  }

  public function getAllAttendeesByEvent(int $eventId): Collection
  {
    return Attendee::with([
      'company',
      'attendeeGroup',
      'events' => function ($query) use ($eventId) {
        $query->where('event_id', $eventId)->where('confirmed', 1);
      },
    ])
      ->whereHas('events', function ($query) use ($eventId) {
        $query->where('event_id', $eventId)->where('confirmed', 1);
      })
      ->where('id', '>', 1)
      ->get()
      ->sortBy(function ($attendee) use ($eventId) {
        return $attendee->events
												->firstWhere('pivot.event_id', $eventId)
												->pivot->attendee_id;
      })->values();
  }

  public function toggleConfirmed(
		int $attendeeId, 
		int $eventId, 
		bool $confirmed
	): AttendeeEvent {
    return AttendeeEvent::updateOrCreate(
      [
        'event_id' => $eventId,
        'attendee_id' => $attendeeId,
      ],
      [
        'confirmed' => $confirmed,
      ]
    );
  }

  private function addAttendeeEvent(
		int $attendeeId, 
		int $eventId, 
		int $confirmed
	): AttendeeEvent {
    return AttendeeEvent::updateOrCreate(
      [
        'event_id' => $eventId,
        'attendee_id' => $attendeeId,
      ],
      [
        'confirmed' => $confirmed,
      ]
    );
  }
}
