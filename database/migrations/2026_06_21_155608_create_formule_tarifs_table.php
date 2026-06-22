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
    Schema::create('formules_tarifs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
        $table->string('formule');
        $table->string('type_chambre')->nullable();
        $table->decimal('prix_chambre', 10, 2)->nullable();
        $table->decimal('prix_formule', 10, 2);
        $table->decimal('promotion', 5, 2)->default(0);
        $table->date('periode_debut')->nullable();
        $table->date('periode_fin')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formule_tarifs');
    }
};
