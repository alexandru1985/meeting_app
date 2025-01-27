<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  protected $fillable = [
		'name', 'company_id', 'attendee_id', 'email', 'password'
	];

  protected $hidden = ['password', 'remember_token'];

  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function events()
  {
    return $this->belongsToMany(Event::class)
      ->withPivot('confirmed')
      ->withTimestamps();
  }

  public function tables()
  {
    return $this->belongsToMany(Table::class)->withTimestamps();
  }

  public function attendeeGroup()
  {
    return $this->belongsTo(AttendeeGroup::class);
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
