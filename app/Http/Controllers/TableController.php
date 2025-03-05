<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Table;
use App\Models\Event;
use Illuminate\Http\Response;
use App\Http\Requests\TableRequest;
use Illuminate\Http\Request;
use App\Services\TableService;

class TableController extends Controller
{
	public function __construct(
			private TableService $tableService
	) {   
	}

	public function index(Request $request): JsonResponse
	{
		$perPage = 10;  
		$page = $request->get('page', 1);  
		$tables = $this->tableService->getAllTables($perPage, $page);

		return response()->json($tables, Response::HTTP_OK);
	}

	public function store(TableRequest $request): JsonResponse
	{
		$validated = $request->validated();
		$table = $this->tableService->createTable($validated);

		return response()->json($table, Response::HTTP_CREATED);
	}

	public function show(Table $table): JsonResponse
	{
		$table = $this->tableService->getTableById($table->id);
		
		return response()->json($table, Response::HTTP_OK);
	}

	public function update(
		TableRequest $request, 
		Table $table
	): JsonResponse{
		$validated = $request->validated();
		$updatedTable = $this->tableService
												->updateTable($validated, $table);

		return response()->json($updatedTable, Response::HTTP_OK);
	}

	public function destroy(Table $table): JsonResponse
	{
		$this->tableService->deleteTable($table);
		
		return response()->json(null, Response::HTTP_NO_CONTENT);
	}

	public function tablesByEvent(
		Request $request, 
		Event $event
	): JsonResponse {
		$tables = $this->tableService->getTablesByEvent($event); 

		return response()->json($tables, Response::HTTP_OK);
	}    
}
