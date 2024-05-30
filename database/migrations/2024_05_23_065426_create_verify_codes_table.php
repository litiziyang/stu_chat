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
        Schema::create('verify_codes', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->index()->comment('电话号码');
            $table->string('code')->index()->comment('验证码');
            $table->tinyInteger('type')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verify_codes');
    }
};
