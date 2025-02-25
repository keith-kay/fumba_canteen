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
        Schema::create('bsl_cmn_user_types', function (Blueprint $table) {
            $table->increments('bsl_cmn_user_types_id');
            $table->string('bsl_cmn_user_types_name');
            $table->integer('bsl_cmn_user_types_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bsl_cmn_user_types');
    }
};
