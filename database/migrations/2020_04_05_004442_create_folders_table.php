<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vault_id');
            $table->string('name');
            $table->string('icon', 25)->nullable();
            $table->timestamps();

            $table->foreign('vault_id')
                ->references('id')
                ->on('vaults')
                ->onDelete('cascade');
        });

        Schema::create('folder_site', function(Blueprint $table) {
            $table->uuid('folder_id');
            $table->uuid('site_id');
            $table->primary(['folder_id', 'site_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_site');
        Schema::dropIfExists('folders');
    }
}
