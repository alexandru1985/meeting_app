<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Company;
use App\Models\Event;
use Illuminate\Http\Response;
use App\Services\DashboardService;

class DashboardController extends Controller
{
	public function __construct(
			private DashboardService $dashboardService
	) {  
	}

	public function getConfirmedAttendeesPerEvent(): JsonResponse
	{
		$data = $this->dashboardService->getConfirmedAttendeesPerEvent();

		return response()->json($data->values(), Response::HTTP_OK);
	}

	public function getAttendeesPerCompany(): JsonResponse
	{
		$data = $this->dashboardService->getAttendeesPerCompany();

		return response()->json($data, Response::HTTP_OK);
	}
}
