<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('states', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('color');
        });
        Schema::create('types', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('normalized_name');
        });
        Schema::create('counterparties', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('normalized_name');
        });
        Schema::create('areas', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('normalized_name');
        });
        Schema::create('records', function(Blueprint $table){
            $table->id();
            $table->string('number')->unique()->index('record_number');
            $table->string('title');
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');
            $table->foreignId('type_id')->constrained('types')->onDelete('cascade');
            $table->foreignId('counterparty_id')->constrained('counterparties')->onDelete('cascade');
            $table->unsignedBigInteger('last_movement_id')->nullable();
            $table->date('created_at');
            $table->date('updated_at');
        });
        Schema::create('record_movements', function(Blueprint $table){
            $table->id();
            $table->foreignId('record_id')->constrained('records')->onDelete('cascade')->index('record_movement');
            $table->foreignId('to_area_id')->constrained('areas')->onDelete('cascade');
            $table->string('associated_doc');
            $table->date('moved_at');
            $table->foreignId('user_id')->constrained('users');
        });

        Schema::table('records', function (Blueprint $table) {
            $table->foreign('last_movement_id')
                ->references('id')
                ->on('record_movements')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
        Schema::dropIfExists('types');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('records');
        Schema::dropIfExists('record_movements');
    }
};
