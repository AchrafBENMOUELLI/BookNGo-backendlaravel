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
    Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
        $table->foreignId('id_hotel')->constrained('hotels')->onDelete('cascade');
        $table->date('date_arrivee');
        $table->date('date_depart');
        $table->integer('nombre_adultes')->default(1);
        $table->integer('nombre_enfants')->default(0);
        $table->string('etat')->default('en_attente'); // en_attente, confirmee, annulee
        $table->string('formule')->nullable();
        $table->decimal('prix', 10, 2);
        $table->integer('nbr_chambre')->default(1);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
