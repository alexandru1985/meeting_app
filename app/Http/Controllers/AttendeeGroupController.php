<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\AttendeeGroup;
use Illuminate\Http\Response;
use App\Http\Requests\AttendeeGroupRequest;
use Illuminate\Http\Request;
use App\Services\AttendeeGroupService;

class AttendeeGroupController extends Controller
{
	public function __construct(
		private AttendeeGroupService $attendeeGroupService
	) {
	}

	public function index(Request $request): JsonResponse
	{
		$perPage = 10;
		$page = $request->get('page', 1);
		$attendeeGroups = $this->attendeeGroupService
												->getAllAttendeeGroups($perPage, $page);

		return response()->json($attendeeGroups, Response::HTTP_OK);
	}

	public function store(AttendeeGroupRequest $request): JsonResponse
	{
		$validated = $request->validated();
		$attendeeGroup = $this->attendeeGroupService
												->createAttendeeGroup($validated);

		return response()->json($attendeeGroup, Response::HTTP_CREATED);
	}

	public function show(AttendeeGroup $attendeeGroup): JsonResponse
	{
		$attendeeGroup = $this->attendeeGroupService
												->getAttendeeGroupById($attendeeGroup->id);
												
		return response()->json($attendeeGroup, Response::HTTP_OK);
	}

	public function update(
		AttendeeGroupRequest $request, 
		AttendeeGroup $attendeeGroup
	): JsonResponse {
		$validated = $request->validated();
		$updatedAttendeeGroup = $this->attendeeGroupService
																->updateAttendeeGroup(
																		$validated, 
																		$attendeeGroup
																);

		return response()->json($updatedAttendeeGroup, Response::HTTP_OK);
	}

	public function destroy(AttendeeGroup $attendeeGroup): JsonResponse
	{
		$this->attendeeGroupService->deleteAttendeeGroup($attendeeGroup);

		return response()->json(null, Response::HTTP_NO_CONTENT);
	}
}
