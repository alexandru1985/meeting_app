<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('attendee_event', function (Blueprint $table) {
      $table->id();
      $table
        ->foreignId('event_id')
        ->constrained()
        ->onDelete('cascade');
      $table
        ->foreignId('attendee_id')
        ->constrained('users')
        ->onDelete('cascade');
      $table->integer('confirmed');
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('attendee_event');
  }
};
