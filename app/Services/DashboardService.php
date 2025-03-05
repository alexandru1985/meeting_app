<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Event;
use Illuminate\Support\Collection;

class DashboardService
{
  public function getConfirmedAttendeesPerEvent(): Collection
  {
    $events = Event::withCount([
      'attendees' => function ($query) {
        $query->where('attendee_event.confirmed', 1);
      },
    ])->get();

    return $events
      ->filter(function ($event) {
        return $event->attendees_count > 0;
      })
      ->map(function ($event) {
        return [
          'event' => $event->name,
          'confirmed_attendees' => $event->attendees_count,
        ];
      });
  }

  public function getAttendeesPerCompany(): Collection
  {
    $companies = Company::withCount('attendees')->get();

    return $companies->map(function ($company) {
      return [
        'company' => $company->name,
        'attendees' => $company->attendees_count,
      ];
    });
  }
}
