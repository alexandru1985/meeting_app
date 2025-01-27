<?php

namespace App\Services\Export;

use App\Exports\RegisteredAttendeesExport;
use App\Exports\BookedAttendeesExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Interfaces\FileExportStrategy;

class XlsxFileExportStrategy implements FileExportStrategy
{
  public function exportRegisteredAttendees(
		array $attendees, 
		array $event
	): BinaryFileResponse {
    $data = [];
    foreach ($attendees as $key => $attendee) {
      $data[$key]['id'] = (string) $attendee['id'];
      $data[$key]['name'] = $attendee['name'];
      $data[$key]['company'] = $attendee['company']['name'];
      $data[$key]['attendee_type'] = $attendee['attendee_group']['name'];
      $data[$key]['confirmed'] 
				= isset($attendee['events'][0]['pivot']['confirmed']) && 
					$attendee['events'][0]['pivot']['confirmed'] == 1 ? 'Yes' : 'No';
    }

    return Excel::download(
			new RegisteredAttendeesExport($data, $event), 
			'registered_attendees.xlsx'
		);
  }

  public function exportBookedAttendees(
		array $tables, 
		array $event
	): BinaryFileResponse {
    $filteredTables = array_filter($tables, function ($table) {
      return !empty($table['attendees']);
    });

    $data = [];
    foreach ($filteredTables as $table) {
      foreach ($table['attendees'] as $attendee) {
        $data[] = [
          'id' => $attendee['id'],
          'name' => $attendee['name'],
          'company' => $attendee['company']['name'],
          'attendee_type' 
						=> substr($attendee['attendee_group']['name'], 0, -1),
          'table' => $table['name'],
        ];
      }
    }

    return Excel::download(
			new BookedAttendeesExport($data, $event), 
			'booked_attendees.xlsx'
		);
  }
}
