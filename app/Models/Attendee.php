<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
  protected $table = 'users';

  protected $fillable = ['name', 'company_id', 'attendee_group_id'];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function attendeeGroup()
  {
    return $this->belongsTo(AttendeeGroup::class);
  }

  public function events()
  {
    return $this->belongsToMany(Event::class)
      ->withPivot('confirmed')
      ->withTimestamps();
  }
}

