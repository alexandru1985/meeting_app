<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AttendeeEvent extends Pivot
{
  protected $fillable = ['event_id', 'attendee_id', 'confirmed'];

  public function event()
  {
    return $this->belongsTo(Event::class);
  }

  public function attendee()
  {
    return $this->belongsTo(User::class);
  }
}


