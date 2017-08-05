<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name')->unique();
            $table->string('slug')->unique();

            $table->timestamps();
        });

        Schema::create('categorizables', function (Blueprint $table) {
            
            $table->integer('category_id')->unsigned();
            $table->integer('categorizable_id')->unsigned();
            $table->text('categorizable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorizables');
        Schema::dropIfExists('categories');
    }
}