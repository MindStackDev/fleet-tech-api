<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->restrictOnDelete();
            $table->string('plate_number', 20)->unique();
            $table->string('vin', 50)->nullable()->unique();
            $table->string('brand', 100);
            $table->string('model', 100);
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedInteger('current_mileage')->default(0);
            $table->string('status', 30)->default('active'); // active, in_maintenance, out_of_service
            $table->jsonb('specs')->nullable(); // Especificaciones técnicas detalladas
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_types');
    }
};
