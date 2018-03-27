<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('accountGUID')->unique();
            $table->string('displayName')->nullable();
            $table->string('email')->unique();
            $table->string('password');

            $table->string('gender')->default('male');
            $table->string('firstName')->nullable();
            $table->string('insertion')->nullable();
            $table->string('surname')->nullable();
            $table->string('salutation')->nullable();

            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();

            ## Login type and Basic Auth API token
            $table->string('loginType')->default('basic_auth');

            $table->boolean('disabled')->default(false);

            $table->softDeletes();
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
        Schema::dropIfExists('accounts');
    }
}
