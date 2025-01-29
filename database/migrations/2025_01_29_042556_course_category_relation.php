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
        Schema::create('course_category_relation', function (Blueprint $table) {
            $table->foreignId('course_id')
                ->constrained(table: 'courses', column: 'id')
                ->cascadeOnDelete();

            $table->foreignId('course_category_id')
                ->constrained(table: 'course_categories', column: 'id')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_category_relation');
    }
};
