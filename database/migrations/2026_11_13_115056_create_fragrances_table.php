<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fragrances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('scent_family_id')->constrained()->cascadeOnDelete();
            // Also names the bottle photo: public/assets/{slug}.webp
            $table->string('slug')->unique();
            $table->string('house');
            $table->string('name');
            $table->integer('tone');
            $table->string('size');
            $table->string('concentration');
            $table->decimal('price', 12, 2);
            $table->string('caption');
            $table->text('description');
            $table->string('opening_notes');
            $table->string('heart_notes');
            $table->string('dry_down_notes');
            $table->string('source_url');
            $table->boolean('is_finder_pick')->default(false);
            $table->integer('position')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->index('status');
            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fragrances');
    }
};
