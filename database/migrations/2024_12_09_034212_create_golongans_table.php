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
        Schema::create('msgolongan', function (Blueprint $table) {
            $table->increments('golonganId');
            $table->string('golonganNama');
            $table->double('golonganTarif')->nullable();
            $table->double('golonganAbonemen')->nullable();
            $table->String('golonganStatus')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('msgolongan');
    }
};
