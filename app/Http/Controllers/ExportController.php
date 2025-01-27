<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Interfaces\FileExportStrategy;
use App\Services\Export\PdfFileExportStrategy;
use App\Services\Export\XlsxFileExportStrategy;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
	public function exportRegisteredAttendees(
		Request $request, 
		FileExportStrategy $strategy
	): Response | BinaryFileResponse {
		$attendees = $request->input('attendees');
		$event = $request->input('event');

		return $strategy->exportRegisteredAttendees($attendees, $event);
	}

	public function exportBookedAttendees(
		Request $request, 
		FileExportStrategy $strategy
	): Response | BinaryFileResponse {
		$attendees = $request->input('attendees');
		$event = $request->input('event');

		// Filter to include only tables with attendees
		$tables = array_filter($attendees, function($table) {
				return !empty($table['attendees']);
		});

		return $strategy->exportBookedAttendees($tables, $event);
	}
}

