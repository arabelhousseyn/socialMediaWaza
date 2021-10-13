<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class CreateInnovationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('innovations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->tinyInteger('is_financed');
            $table->double('financementAmount');
            $table->string('pathBusinessPlan');
            $table->bigInteger('likes');
            $table->tinyInteger('type');
            $table->string('imageCompany');
            $table->tinyInteger('status');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('innovation_domain_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE innovations ADD audio LONGBLOB DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('innovations');
    }
}
