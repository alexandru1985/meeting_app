<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendeeGroup extends Model
{
  protected $fillable = ['name'];

  public function users()
  {
    return $this->hasMany(User::class);
  }
}
