<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('files')) {
            Schema::create('files', function (Blueprint $table) {
                $table->id();
                $table->string('original_name');
                $table->string('name')->unique();
                $table->string('path');
                $table->string('mime_type', 20);
                $table->unsignedBigInteger('size');
                $table->string('disk');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->primary();
                $table->string('acronym');
                $table->string('name', 25);
            });
        }

        if (! Schema::hasTable('person_profiles')) {
            Schema::create('person_profiles', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('last_name');
                $table->date('birth_date');
                $table->enum('gender', ['m', 'f']);
                $table->string('nationality');
                $table->string('address');
                $table->integer('number');
                $table->string('complement');
                $table->string('neighborhood');
                $table->string('city');
                $table->foreignId('state_id')->constrained('states', 'id');
                $table->string('postal_code', 50);
                $table->foreignId('file_id')->nullable()->constrained('files', 'id');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('person_contacts')) {
            Schema::create('person_contacts', function (Blueprint $table) {
                $table->id();
                $table->string('phone', 11);
                $table->string('email')->nullable();
                $table->enum('type', ['per', 'com', 'eme'])->default('per'); // pessoal,comercial,emergência
                $table->foreignId('person_profile_id')->constrained('person_profiles', 'id');
                $table->timestamps();
            });
        }

    }

    public function down(): void
    {
        //
    }
};
