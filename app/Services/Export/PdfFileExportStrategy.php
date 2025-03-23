<?php

namespace App\Services\Export;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use App\Interfaces\FileExportStrategy;

class PdfFileExportStrategy implements FileExportStrategy
{
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
