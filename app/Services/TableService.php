<?php

namespace App\Services;

use App\Models\Table;
use App\Models\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TableService
{
  public function getAllTables(
		int $perPage, 
		int $page
	): LengthAwarePaginator {
    return Table::with('location')->paginate($perPage, ['*'], 'page', $page);
  }

  public function createTable(array $data): Table
  {
    return Table::create([
      'name' => $data['name'],
      'seats' => $data['seats'],
      'location_id' => $data['location_id'],
    ]);
  }

  public function getTableById(int $id): Table
  {
    return Table::with('location')->findOrFail($id);
  }

  public function updateTable(array $data, Table $table): Table
  {
    $table->update([
      'name' => $data['name'],
      'seats' => $data['seats'],
      'location_id' => $data['location_id'],
    ]);

    return $table;
  }

  public function deleteTable(Table $table): bool
  {
    return $table->delete();
  }

  public function getTablesByEvent(Event $event): Collection
  {
    return $event->location->tables;
  }
}
