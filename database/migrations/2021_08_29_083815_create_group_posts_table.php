<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onUpdate('cascade')->onDelete('cascade')->nullable();
            $table->longText('description');
            $table->string('source');
            $table->tinyInteger('colorabble');
            $table->string('likes');
            $table->tinyInteger('type');
            $table->tinyInteger('is_approved');
            $table->tinyInteger('anonym');
            $table->string('title_pitch');
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
        Schema::dropIfExists('group_posts');
    }
}
