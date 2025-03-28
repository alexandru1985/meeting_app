<?php

namespace App\Services\Export;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use App\Interfaces\FileExportStrategy;

class PdfFileExportStrategy implements FileExportStrategy
{
  public function exportRegisteredAttendees(
		array $attendees, 
		array $event
	): Response {
    return Pdf::loadView('exports.registered_attendees', [
      'attendees' => $attendees,
      'event' => $event,
    ])->download('registered_attendees.pdf');
  }

  public function exportBookedAttendees(
		array $tables, 
		array $event
	): Response {
    return Pdf::loadView('exports.booked_attendees', [
      'tables' => $tables,
      'event' => $event,
    ])->download('booked_attendees.pdf');
  }
}
