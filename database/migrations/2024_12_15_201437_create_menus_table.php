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
        Schema::create('msmenu', function (Blueprint $table) {
            $table->increments('menuId');
            $table->string('menuName');
            $table->string('menuRoute');
            $table->integer('menuParentId')->nullable();
            $table->integer('menuOrder')->nullable();
            $table->string('menuIcon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('msmenu');
    }
};
