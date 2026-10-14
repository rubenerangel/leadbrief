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
        Schema::create('contact_segment', function (Blueprint $table) {
            $table->foreignId('contact_id')
                ->constrained('contacts', 'id')
                ->cascadeOnDelete();
            $table->foreignId('segment_id')
                ->constrained('segments', 'id')
                ->cascadeOnDelete();
            $table->primary(['contact_id', 'segment_id']);
            $table->index('segment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_segment');
    }
};
