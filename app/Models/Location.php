<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
  protected $fillable = ['name', 'address', 'latitude', 'longitude'];

  public function events()
  {
    return $this->hasMany(Event::class);
  }

  public function tables()
  {
    return $this->hasMany(Table::class);
  }
}
