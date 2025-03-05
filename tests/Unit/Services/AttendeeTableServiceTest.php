<?php

namespace Tests\Unit\Services;

use App\Models\AttendeeTable;
use App\Models\Attendee;
use App\Models\Company;
use App\Models\AttendeeGroup;
use App\Models\Event;
use App\Services\AttendeeTableService;
use Tests\TestCase;

class AttendeeTableServiceTest extends TestCase
{
  private AttendeeTableService $attendeeTableService;

  protected function setUp(): void
  {
    parent::setUp();
    $this->attendeeTableService = new AttendeeTableService();
  }

  public function testAddAttendeeToTable()
  {
    $attendee = Attendee::latest()->first();
    $event = Event::latest()->first();

    $data = [
      'attendee_id' => $attendee->id,
      'table_id' => 1, 
      'event_id' => $event->id,
    ];

    $attendeeTable = $this->attendeeTableService->addAttendeeToTable($data);

    $this->assertEquals($attendee->id, $attendeeTable->attendee_id);
    $this->assertEquals(1, $attendeeTable->table_id);
    $this->assertEquals($event->id, $attendeeTable->event_id);
  }

  public function testGetAllAttendeeTables()
  {
    $event = Event::latest()->first();
    $attendeeTable = AttendeeTable::latest()->first();

    $attendeeTable->attendee->events()->attach($event->id, ['confirmed' => 1]);

    $result = $this->attendeeTableService->getAllAttendeeTables($event->id);

    $this->assertGreaterThan(0, $result->count());
    $this->assertContains($attendeeTable->id, $result->pluck('id')->toArray());
  }

  public function testRemoveAttendeeFromTable()
  {
    $attendee = Attendee::latest()->first();
    $event = Event::latest()->first();

    $data = [
      'attendee_id' => $attendee->id,
      'table_id' => 1, 
      'event_id' => $event->id,
    ];

    AttendeeTable::create($data);

    $result = $this->attendeeTableService->removeAttendeeFromTable($data);

    $this->assertTrue($result);
    $this->assertDatabaseMissing('attendee_table', $data);
  }
}
