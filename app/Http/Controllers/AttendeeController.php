<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\AttendeeRequest;
use App\Http\Requests\AddAttendeeToTableRequest;
use App\Http\Requests\RemoveAttendeeFromTableRequest;
use App\Http\Requests\ToggleConfirmedRequest;
use App\Services\AttendeeService;
use App\Services\EventService;
use App\Services\AttendeeTableService;

class AttendeeController extends Controller
{
	public function __construct(
		private AttendeeService $attendeeService,
		private EventService $eventService,
		private AttendeeTableService $attendeeTableService
	) {
	}

	public function index(Request $request): JsonResponse
	{
		$filters = $request->only([
			'name', 'companies', 'attendee_groups', 'confirmed'
		]);

		$eventId = $request->get('event_id');
		$perPage = 10;
		$page = $request->get('page', 1);

		$attendees = $this->attendeeService->getAllAttendees(
			$filters, $eventId, $perPage, $page
		);

		return response()->json($attendees, Response::HTTP_OK);
	}

	public function store(AttendeeRequest $request): JsonResponse
	{
		$validated = $request->validated();
		$attendee = $this->attendeeService->createAttendee($validated);

		return response()->json($attendee, Response::HTTP_CREATED);
	}

	public function show(int $id): JsonResponse
	{
		$attendee = $this->attendeeService->getAttendeeById($id);

		return response()->json($attendee, Response::HTTP_OK);
	}

	public function update(
		AttendeeRequest $request, 
		int $id
	): JsonResponse {
		$validated = $request->validated();
		$attendee = $this->attendeeService
										->updateAttendee($id, $validated);

		return response()->json($attendee, Response::HTTP_OK);
	}

	public function destroy(int $id): JsonResponse
	{
		$this->attendeeService->deleteAttendee($id);
		
		return response()->json(null, Response::HTTP_NO_CONTENT);
	}

	public function getAllAttendees(): JsonResponse
	{
		$event = $this->eventService->getCurrentEvent();
		$attendees = $this->attendeeService
										->getAllAttendeesByEvent($event->id);

		return response()->json($attendees);
	}

	public function setAttendeesToTable(
		Request $request
	): JsonResponse {
		$eventId = $request->query('event_id');
		$attendeeTable = $this->attendeeTableService
												->getAllAttendeeTables($eventId);

		return response()->json($attendeeTable);
	}

	public function addAttendeeToTable(
		AddAttendeeToTableRequest $request
	): JsonResponse {
		$validatedData = $request->validated();
		$attendeeTable = $this->attendeeTableService
												->addAttendeeToTable($validatedData);

		return response()->json(
				$attendeeTable, 
				Response::HTTP_CREATED
		);
	}

	public function removeAttendeeFromTable(
		RemoveAttendeeFromTableRequest $request
	): JsonResponse {
		$validatedData = $request->validated();
		$this->attendeeTableService
				->removeAttendeeFromTable($validatedData);

		return response()->json([
				'message' => 'Attendee removed from table'
		], Response::HTTP_OK);
	}

	public function toggleConfirmed(
		ToggleConfirmedRequest $request, 
		int $id
	): JsonResponse {
		$validatedData = $request->validated();
		$attendee = $this->attendeeService
										->toggleConfirmed(
												$id, 
												$validatedData['event_id'], 
												$validatedData['confirmed']
										);

		return response()->json($attendee, Response::HTTP_OK);
	}
}
