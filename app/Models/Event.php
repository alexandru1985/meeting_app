<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
  protected $fillable = [
		'name', 'start_time', 'end_time', 'location_id', 'is_set'
	];

  public function location()
  {
    return $this->belongsTo(Location::class);
  }

  public function attendees()
  {
    return $this->belongsToMany(Attendee::class)
      ->withPivot('confirmed')
      ->withTimestamps();
  }

  public function setEvent()
  {
    return self::where('is_set', 1)->first();
  }
}
