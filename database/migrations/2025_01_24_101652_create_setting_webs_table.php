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
        Schema::create('mssetting_web', function (Blueprint $table) {
            $table->id();
            $table->string('settingWebNama')->nullable();
            $table->text('settingWebLogo')->nullable();
            $table->text('settingWebLogoLandscape')->nullable();
            $table->text('settingWebAlamat')->nullable();
            $table->string('settingWebEmail')->nullable();
            $table->string('settingWebPhone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mssetting_web');
    }
};
