<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AttendeeGroupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Services\Export\PdfFileExportStrategy;
use App\Services\Export\XlsxFileExportStrategy;

Route::group(['middleware' => ['auth:api']], function () {
  Route::apiResource('events', EventController::class);
  Route::apiResource('locations', LocationController::class);
  Route::apiResource('tables', TableController::class);
  Route::apiResource('companies', CompanyController::class);
  Route::apiResource('attendee-groups', AttendeeGroupController::class);
  Route::apiResource('users', AttendeeController::class);
  Route::put(
		'/users/{attendee}/toggle-confirmed', 
		[AttendeeController::class, 'toggleConfirmed']
	);
  Route::post(
		'/logout', 
		[AuthController::class, 'logout']
	);
  Route::get(
		'/tables-by-event/{event}', 
		[TableController::class, 'tablesByEvent']
	);
  Route::get(
		'/event-set', 
		[EventController::class, 'eventSet']
	);
  Route::get(
		'/all-attendees', 
		[AttendeeController::class, 'getAllAttendees']
	);
  Route::post(
		'/add-attendee-to-table', 
		[AttendeeController::class, 'addAttendeeToTable']
	);
  Route::get(
		'/set-attendees-to-table', 
		[AttendeeController::class, 'setAttendeesToTable']
	);
  Route::delete(
		'/remove-attendee-from-table', 
		[AttendeeController::class, 'removeAttendeeFromTable']
	);
  Route::get(
		'confirmed-attendees-per-event', 
		[DashboardController::class, 'getConfirmedAttendeesPerEvent']
	);
  Route::get(
		'attendees-per-company', 
		[DashboardController::class, 'getAttendeesPerCompany']
	);
  Route::post(
		'/export/registered-attendees-to-xlsx', 
		[ExportController::class, 'exportRegisteredAttendeesToXlsx']
	);
  Route::post(
		'/export/registered-attendees-to-pdf', 
		[ExportController::class, 'exportRegisteredAttendeesToPdf']
	);
  Route::post(
		'/export/booked-attendees-to-xlsx', 
		[ExportController::class, 'exportBookedAttendeesToXlsx']
	);
  Route::post(
		'/export/booked-attendees-to-pdf', 
		[ExportController::class, 'exportBookedAttendeesToPdf']
	);
  Route::post(
		'/export/registered-attendees-to-xlsx', 
		[ExportController::class, 'exportRegisteredAttendees']
	)->defaults('strategy', new XlsxFileExportStrategy());
  Route::post(
		'/export/registered-attendees-to-pdf', 
		[ExportController::class, 'exportRegisteredAttendees']
	)->defaults('strategy', new PdfFileExportStrategy());
  Route::post(
		'/export/booked-attendees-to-xlsx', 
		[ExportController::class, 'exportBookedAttendees']
	)->defaults('strategy', new XlsxFileExportStrategy());
  Route::post(
		'/export/booked-attendees-to-pdf', 
		[ExportController::class, 'exportBookedAttendees']
	)->defaults('strategy', new PdfFileExportStrategy());
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/refresh', [AuthController::class, 'refreshToken']);
