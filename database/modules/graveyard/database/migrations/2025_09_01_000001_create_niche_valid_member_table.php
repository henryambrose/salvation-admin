<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNicheValidMemberTable extends Migration
{
    public function up()
    {
        Schema::create('niche_valid_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niche_id')
                  ->constrained('niches')
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
            Schema::dropIfExists('niche_valid_member');
    }
}


