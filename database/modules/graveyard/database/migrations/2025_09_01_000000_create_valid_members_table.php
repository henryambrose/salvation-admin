<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValidMembersTable extends Migration
{
    public function up()
    {
        Schema::create('valid_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permanent_grave_id')
                  ->constrained('permanent_graves')
                  ->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('member_id')
                  ->constrained('members')
                  ->onDelete('cascade');
            $table->string('contact_no');
            $table->string('aadhar_no')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('valid_members');
    }
}
