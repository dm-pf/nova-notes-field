<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Outl1ne\NovaNotesField\NotesFieldServiceProvider;

class ChangeTextColumnToText extends Migration
{
    public function up()
    {
        Schema::table(NotesFieldServiceProvider::getTableName(), function (Blueprint $table) {
            $table->text('text')->change();
        });
    }

    public function down()
    {
        Schema::table(NotesFieldServiceProvider::getTableName(), function (Blueprint $table) {
            $table->string('text')->change();
        });
    }
}
