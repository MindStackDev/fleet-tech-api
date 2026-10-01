<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('shift_type', 30)->default('any'); // morning, afternoon, night, any
            $table->string('version', 20)->default('1.0');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('checklist_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_template_id')->constrained('checklist_templates')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });

        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('checklist_sections')->cascadeOnDelete();
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('input_type', 30)->default('boolean'); // boolean, select, number, text
            $table->jsonb('options')->nullable(); // Para listas de opciones o rangos numéricos
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('requires_photo_on_fail')->default(true);
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
        Schema::dropIfExists('checklist_sections');
        Schema::dropIfExists('checklist_templates');
    }
};
