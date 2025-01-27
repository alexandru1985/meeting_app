<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('attendee_table', function (Blueprint $table) {
      $table->id();
      $table
        ->foreignId('table_id')
        ->constrained()
        ->onDelete('cascade');
      $table
        ->foreignId('attendee_id')
        ->constrained('users')
        ->onDelete('cascade');
      $table
        ->foreignId('event_id')
        ->constrained()
        ->onDelete('cascade');
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('attendee_table');
  }
};
