<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('checklist_template_id')->constrained('checklist_templates')->restrictOnDelete();
            $table->string('inspection_type', 40)->default('routine_check'); // shift_handover, routine_check, fault_diagnosis, post_repair
            $table->string('shift', 30); // morning, afternoon, night
            
            $table->foreignId('operator_id')->constrained('users')->comment('Operador que realiza la inspeccion / entrega turno');
            $table->foreignId('receiving_operator_id')->nullable()->constrained('users')->comment('Operador entrante que recibe el relevo');
            
            $table->unsignedInteger('start_mileage');
            $table->unsignedInteger('end_mileage')->nullable();
            $table->string('fuel_level', 20)->nullable(); // 100%, 75%, 50%, 25%, Reserva
            
            $table->string('yard_location', 100)->nullable(); // Bahía 3, Patio Norte, Rampa 2
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            $table->string('status', 30)->default('completed'); // draft, completed, flagged, approved, rejected
            
            $table->string('operator_signature_path')->nullable();
            $table->string('receiving_signature_path')->nullable();
            
            $table->text('general_notes')->nullable();
            $table->timestamp('inspected_at');
            $table->timestamps();
        });

        Schema::create('inspection_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->constrained('checklist_items')->restrictOnDelete();
            $table->string('status', 20)->default('pass'); // pass, fail, na
            $table->text('value')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inspection_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections')->cascadeOnDelete();
            $table->foreignId('inspection_answer_id')->nullable()->constrained('inspection_answers')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('caption')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_evidences');
        Schema::dropIfExists('inspection_answers');
        Schema::dropIfExists('inspections');
    }
};
