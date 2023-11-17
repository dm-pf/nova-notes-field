<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Outl1ne\NovaNotesField\NotesFieldServiceProvider;

class CreateNovaNotesTable extends Migration
{
    public function up()
    {
        Schema::create(NotesFieldServiceProvider::getTableName(), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('text');
            $table->boolean('system');
            $table->morphs('notable');
            $table->bigInteger('created_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists(NotesFieldServiceProvider::getTableName());
    }
}
