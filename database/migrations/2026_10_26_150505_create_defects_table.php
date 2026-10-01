<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->constrained('checklist_items')->restrictOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            
            $table->string('severity', 20)->default('medium'); // low, medium, critical
            $table->string('status', 20)->default('open'); // open, in_workshop, resolved, dismissed
            $table->text('description');
            
            $table->foreignId('reported_by')->constrained('users')->comment('Operador que detecto la falla en patio');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->comment('Tecnico que reparo la falla');
            
            $table->text('resolution_notes')->nullable();
            $table->string('resolved_evidence_path')->nullable(); // Foto de la reparacion terminada
            $table->timestamp('resolved_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defects');
    }
};
