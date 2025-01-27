<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Response;

interface FileExportStrategy
{
	public function exportRegisteredAttendees(
		array $attendees, 
		array $event
	): BinaryFileResponse|Response;

	public function exportBookedAttendees(
		array $tables, 
		array $event
	): BinaryFileResponse|Response;
}


