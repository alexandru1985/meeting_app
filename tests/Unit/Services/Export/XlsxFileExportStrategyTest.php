<?php

namespace Tests\Unit\Services\Export;

use App\Exports\RegisteredAttendeesExport;
use App\Exports\BookedAttendeesExport;
use App\Models\Attendee;
use App\Models\Company;
use App\Models\AttendeeGroup;
use App\Models\Event;
use App\Services\Export\XlsxFileExportStrategy;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class XlsxFileExportStrategyTest extends TestCase
{
  private XlsxFileExportStrategy $exportStrategy;

  protected function setUp(): void
  {
    parent::setUp();
    $this->exportStrategy = new XlsxFileExportStrategy();
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
      'location' => ['name' => 'LocationA'],
      'start_time' => '2023-01-01 00:00:00',
      'end_time' => '2023-01-01 23:59:59',
    ];

    $response = $this->exportStrategy
										->exportRegisteredAttendees($attendees, $eventData);

    $this->assertInstanceOf(BinaryFileResponse::class, $response);
  }

  public function testExportBookedAttendees()
  {
    $company = Company::latest()->first();
    $attendeeGroup = AttendeeGroup::latest()->first();
    $event = Event::latest()->first();
    $attendee = Attendee::latest()->first();

    $tables = [
      [
        'name' => 'Table1',
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
      'location' => ['name' => 'LocationA'],
      'start_time' => '2023-01-01 00:00:00',
      'end_time' => '2023-01-01 23:59:59',
    ];

    $response = $this->exportStrategy
										->exportBookedAttendees($tables, $eventData);

    $this->assertInstanceOf(BinaryFileResponse::class, $response);
  }
}
