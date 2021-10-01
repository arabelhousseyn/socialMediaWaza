<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullName');
            $table->string('subName');
            $table->date('dob');
            $table->string('picture');
            $table->tinyInteger('gender');
            $table->string('profession');
            $table->foreignId('wilaya_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('is_freelancer');
            $table->tinyInteger('is_verified');
            $table->tinyInteger('receive_ads');
            $table->string('token')->nullable();
            $table->string('code_verification')->nullable();
            $table->tinyInteger('hide_phone');
            $table->tinyInteger('is_kaiztech_team');
            $table->string('company');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
