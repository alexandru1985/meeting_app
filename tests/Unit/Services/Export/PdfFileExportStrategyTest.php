<?php

namespace Tests\Unit\Services\Export;

use App\Models\Attendee;
use App\Models\Company;
use App\Models\AttendeeGroup;
use App\Models\Event;
use App\Services\Export\PdfFileExportStrategy;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Tests\TestCase;

class PdfFileExportStrategyTest extends TestCase
{
  private PdfFileExportStrategy $exportStrategy;

  protected function setUp(): void
  {
    parent::setUp();
    $this->exportStrategy = new PdfFileExportStrategy();
  }

  public function testExportRegisteredAttendees()
  {
    $company = Company::latest()->first();
    $attendeeGroup = AttendeeGroup::latest()->first();
    $event = Event::latest()->first();
    $attendee = Attendee::latest()->first();

    $attendees = [
      [
        'id' => $attendee->id,
        'name' => $attendee->name,
        'company' => ['name' => $company->name],
        'attendee_group' => ['name' => $attendeeGroup->name],
        'events' => [['pivot' => ['confirmed' => 1]]],
      ],
    ];

    $eventData = [
      'name' => $event->name,
      'location' => ['name' => 'Test Location'],
      'start_time' => '2023-01-01 00:00:00',
      'end_time' => '2023-01-01 23:59:59',
    ];

    $response = $this->exportStrategy
										->exportRegisteredAttendees($attendees, $eventData);

    $this->assertInstanceOf(Response::class, $response);
  }

  public function testExportBookedAttendees()
  {
    $company = Company::latest()->first();
    $attendeeGroup = AttendeeGroup::latest()->first();
    $event = Event::latest()->first();
    $attendee = Attendee::latest()->first();

    $tables = [
      [
        'name' => 'Table 1',
        'attendees' => [
          [
            'id' => $attendee->id,
            'name' => $attendee->name,
            'company' => ['name' => $company->name],
            'attendee_group' => ['name' => $attendeeGroup->name],
          ],
        ],
      ],
    ];

    $eventData = [
      'name' => $event->name,
      'location' => ['name' => 'Test Location'],
      'start_time' => '2023-01-01 00:00:00',
      'end_time' => '2023-01-01 23:59:59',
    ];

    $response = $this->exportStrategy->exportBookedAttendees($tables, $eventData);

    $this->assertInstanceOf(Response::class, $response);
  }
}
