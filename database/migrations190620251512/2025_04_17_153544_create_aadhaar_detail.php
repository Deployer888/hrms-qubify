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
        Schema::create('aadhaar_detail', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->index();
            $table->string('name');
            $table->string('aadhaar_number')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->year('year_of_birth')->nullable();
            $table->string('mobile_hash')->nullable();
            $table->string('email_hash')->nullable();
            $table->string('care_of')->nullable();
            $table->text('full_address')->nullable();
            $table->string('house')->nullable();
            $table->string('street')->nullable();
            $table->string('landmark')->nullable();
            $table->string('vtc')->nullable(); // Village/Town/City
            $table->string('subdistrict')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable()->default('India');
            $table->string('pincode')->nullable();
            $table->longText('photo_encoded')->nullable();
            $table->string('share_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aadhaar_detail');
    }
};
