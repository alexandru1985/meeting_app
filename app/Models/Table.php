<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
  protected $fillable = ['name', 'seats', 'location_id'];

  public function location()
  {
    return $this->belongsTo(Location::class);
  }

  public function attendees()
  {
    return $this->belongsToMany(User::class)->withTimestamps();
  }
}

