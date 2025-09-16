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
        Schema::create('aadhaar_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('aadhaar_number')->nullable()->unique();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->year('year_of_birth')->nullable();
            $table->string('mobile_hash')->nullable();
            $table->string('email_hash')->nullable();
            $table->string('care_of', 100)->nullable();
            $table->text('full_address')->nullable();
            $table->string('house', 100)->nullable();
            $table->string('street', 100)->nullable();
            $table->string('landmark', 100)->nullable();
            $table->string('vtc', 100)->nullable();
            $table->string('subdistrict', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->longText('photo_encoded')->nullable();
            $table->string('share_code', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aadhaar_details');
    }
};