<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Event;
use Illuminate\Http\Response;
use App\Http\Requests\EventRequest;
use Illuminate\Http\Request;
use App\Services\EventService;

class EventController extends Controller
{
	public function __construct(
			private EventService $eventService
	) {
	}

	public function index(Request $request): JsonResponse
	{
		$event = Event::first();
		$perPage = 10;  
		$page = $request->get('page', 1);  
		$events = $this->eventService->getAllEvents($perPage, $page);

		return response()->json($events, Response::HTTP_OK);
	}

	public function store(EventRequest $request): JsonResponse
	{
		$validated = $request->validated();
		$event = $this->eventService->createEvent($validated);

		return response()->json($event, Response::HTTP_CREATED);
	}

	public function show(Event $event): JsonResponse
	{
		$event = $this->eventService->getEventById($event->id);
		
		return response()->json($event, Response::HTTP_OK);
	}

	public function update(EventRequest $request, Event $event): JsonResponse
	{
		$validated = $request->validated();

		$updatedEvent = $this->eventService->updateEvent($validated, $event);

		return response()->json($updatedEvent, Response::HTTP_OK);
	}

	public function destroy(Event $event): JsonResponse
	{
		$this->eventService->deleteEvent($event);

		return response()->json(null, Response::HTTP_NO_CONTENT);
	}

	public function eventSet(): JsonResponse
	{
		$event = $this->eventService->getCurrentEvent();

		if ($event) {
				return response()->json($event, Response::HTTP_OK);
		}

		return response()->json([
				'message' => 'No event is currently set'
		], Response::HTTP_NOT_FOUND);
	}
}
