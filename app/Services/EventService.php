<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EventService
{
  public function getAllEvents(
		int $perPage, 
		int $page
	): LengthAwarePaginator {
    return Event::with('location')->paginate($perPage, ['*'], 'page', $page);
  }

  public function createEvent(array $data): Event
  {
    $event = Event::create([
      'name' => $data['name'],
      'start_time' => $data['start_time'],
      'end_time' => $data['end_time'],
      'location_id' => $data['location_id'],
      'is_set' => $data['is_set'] ?? 0,
    ]);

    if ($event->is_set) {
      Event::where('id', '!=', $event->id)->update(['is_set' => 0]);
    }

    return $event;
  }

  public function getEventById(int $id): Event
  {
    return Event::with('location')->findOrFail($id);
  }

  public function updateEvent(array $data, Event $event): Event
  {
    Event::where('id', '!=', $event->id)->update(['is_set' => 0]);

    if (isset($data['is_set']) && count($data) == 1) {
      $event->update(['is_set' => $data['is_set']]);
      return $event;
    } 
      $event->update([
        'name' => $data['name'],
        'start_time' => $data['start_time'],
        'end_time' => $data['end_time'],
        'location_id' => $data['location_id'],
        'is_set' => $data['is_set'] ?? $event->is_set,
      ]);

    return $event;
  }

  public function deleteEvent(Event $event): bool
  {
    return $event->delete();
  }

  public function getCurrentEvent(): ?Event
  {
    return Event::with('location')
      ->where('is_set', 1)
      ->first();
  }
}
