<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID string
            $table->string('subject');
            $table->text('body');
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->string('category')->nullable();
            $table->text('note')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};