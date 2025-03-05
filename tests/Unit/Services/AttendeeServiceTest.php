<?php

namespace Tests\Unit\Services;

use App\Models\Attendee;
use App\Models\Company;
use App\Models\AttendeeGroup;
use App\Models\Event;
use App\Models\User;
use App\Services\AttendeeService;
use Tests\TestCase;

class AttendeeServiceTest extends TestCase
{
  private AttendeeService $attendeeService;

  protected function setUp(): void
  {
    parent::setUp();
    $this->attendeeService = new AttendeeService();
  }

  public function testGetAllAttendees()
  {
    $company = Company::latest()->first();
    $attendeeGroup = AttendeeGroup::latest()->first();
    $event = Event::latest()->first();
    $attendee = Attendee::latest()->first();

    $attendee->events()->attach($event->id, ['confirmed' => 1]);

    $filters = [];
    $result = $this->attendeeService->getAllAttendees($filters, $event->id);

    $this->assertGreaterThan(0, $result->total());
  }

  public function testCreateAttendee()
  {
    $company = Company::latest()->first();
    $attendeeGroup = AttendeeGroup::latest()->first();
    $event = Event::latest()->first();

    $data = [
      'name' => 'John Doe',
      'company_id' => $company->id,
      'attendee_group_id' => $attendeeGroup->id,
      'event_id' => $event->id,
      'confirmed' => 1,
    ];

    $attendee = $this->attendeeService->createAttendee($data);

    $this->assertEquals('John Doe', $attendee->name);
    $this->assertEquals($company->id, $attendee->company_id);
    $this->assertEquals($attendeeGroup->id, $attendee->attendee_group_id);
    $this->assertEquals(1, $attendee->events()->first()->pivot->confirmed);
  }

  public function testGetAttendeeById()
  {
    $company = Company::latest()->first();
    $attendeeGroup = AttendeeGroup::latest()->first();
    $attendee = Attendee::latest()->first();

    $result = $this->attendeeService->getAttendeeById($attendee->id);

    $this->assertEquals($attendee->id, $result->id);
    $this->assertEquals($attendee->company_id, $result->company->id);
    $this->assertEquals($attendee->attendee_group_id, $result->attendeeGroup->id);
  }

  public function testUpdateAttendee()
  {
    $attendee = Attendee::latest()->first();

    $newCompany = Company::find(2); 
    $newAttendeeGroup = AttendeeGroup::find(2); 
    $data = [
      'name' => 'Jane Doe',
      'company_id' => $newCompany->id,
      'attendee_group_id' => $newAttendeeGroup->id,
    ];

    $updatedAttendee = $this->attendeeService
														->updateAttendee($attendee->id, $data);

    $this->assertEquals('Jane Doe', $updatedAttendee->name);
    $this->assertEquals($newCompany->id, $updatedAttendee->company_id);
    $this->assertEquals($newAttendeeGroup->id, $updatedAttendee->attendee_group_id);
  }

  public function testDeleteAttendee()
  {
    $attendee = Attendee::latest()->first();

    $result = $this->attendeeService->deleteAttendee($attendee->id);

    $this->assertTrue($result);
    $this->assertDatabaseMissing('users', ['id' => $attendee->id]); 
  }

  public function testGetAllAttendeesByEvent()
  {
    $event = Event::latest()->first();
    $attendee = Attendee::latest()->first();

    $attendee->events()->attach($event->id, ['confirmed' => 1]);

    $result = $this->attendeeService->getAllAttendeesByEvent($event->id);

    $this->assertGreaterThan(0, $result->count());
    $this->assertContains($attendee->id, $result->pluck('id')->toArray());
  }

  public function testToggleConfirmed()
  {
    $event = Event::latest()->first();
    $attendee = Attendee::latest()->first();

    $attendeeEvent = $this->attendeeService
													->toggleConfirmed($attendee->id, $event->id, true);

    $this->assertEquals($attendee->id, $attendeeEvent->attendee_id);
    $this->assertEquals($event->id, $attendeeEvent->event_id);
    $this->assertTrue($attendeeEvent->confirmed);

    $attendeeEvent = $this->attendeeService
													->toggleConfirmed($attendee->id, $event->id, false);

    $this->assertEquals($attendee->id, $attendeeEvent->attendee_id);
    $this->assertEquals($event->id, $attendeeEvent->event_id);
    $this->assertFalse($attendeeEvent->confirmed);
  }
}
