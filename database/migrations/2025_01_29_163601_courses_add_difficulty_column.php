<?php

use App\Enums\CourseDifficultyEnum;
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
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('difficulty')
                ->default(CourseDifficultyEnum::BEGINNER->value)
                ->index();

            $table->unsignedTinyInteger('duration')
                ->default(0)
                ->index();

            $table->decimal('rating', 1, 1)
                ->default(0)
                ->index();


            $table->boolean('is_certified')
                ->default(false)
                ->index();



            $table->tinyText('format')
                ->default('text')
                ->index();


            $table->decimal('price', 8, 2)
                ->default(0)
                ->index();

            $table->tinyText('popularity')
                ->nullable()
                ->index();

            $table->tinyText('instructor')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['popularity']);
            $table->dropIndex(['price']);
            $table->dropIndex(['format']);
            $table->dropIndex(['is_certified']);
            $table->dropIndex(['rating']);
            $table->dropIndex(['difficulty']);
            $table->dropIndex(['duration']);

            $table->dropColumn(['popularity']);
            $table->dropColumn(['price']);
            $table->dropColumn(['format']);
            $table->dropColumn(['is_certified']);
            $table->dropColumn(['rating']);
            $table->dropColumn(['difficulty']);
            $table->dropColumn(['duration']);
        });
    }
};
