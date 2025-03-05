<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendeeTable extends Model
{
  protected $table = 'attendee_table';
  protected $fillable = ['table_id', 'attendee_id', 'event_id'];

  public function table()
  {
    return $this->belongsTo(Table::class);
  }

  public function attendee()
  {
    return $this->belongsTo(Attendee::class);
  }

  public function event()
  {
    return $this->belongsTo(Event::class);
  }
}
