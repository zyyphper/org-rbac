<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrgTables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return config('org.database.connection') ?: config('database.default');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('org.database.users_table'), function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('platform_id');
            $table->bigInteger('company_id');
            $table->string('username', 64)->unique();
            $table->string('password', 60);
            $table->string('name',32);
            $table->string('avatar')->nullable();
            $table->tinyInteger('is_admin')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->integer('sort')->default(0);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create(config('org.database.user_infos_table'), function (Blueprint $table) {
            $table->bigInteger('user_id');
            $table->char('phone', 12)->nullable();
            $table->string('email', 32)->nullable();
            $table->tinyInteger('is_check_identity')->default(0);
            $table->string('realname',16)->nullable();
            $table->char('identity_code',18)->nullable();
            $table->timestamps();
        });

        Schema::create(config('org.database.menu_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->default(0);
            $table->integer('order')->default(0);
            $table->string('title', 50);
            $table->string('icon', 50)->nullable();
            $table->string('uri')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('is_admin')->default(0);
            $table->timestamps();
        });
        Schema::create(config('org.database.platform_menu_table'), function (Blueprint $table) {
            $table->integer('menu_id');
            $table->bigInteger('platform_id');
            $table->timestamps();
        });
        Schema::create(config('org.database.platforms_table'), function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('name',64);
            $table->tinyInteger('is_admin')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create(config('org.database.companies_table'), function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('parent_id')->default(0);
            $table->bigInteger('platform_id');
            $table->string('name', 32);
            $table->string('email', 32)->nullable();
            $table->string('phone', 12)->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        Schema::create(config('org.database.departments_table'), function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('parent_id')->default(0);
            $table->bigInteger('company_id');
            $table->string('name', 32);
            $table->string('leader', 32)->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        Schema::create(config('org.database.duties_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->bigInteger('department_id');
            $table->tinyInteger('department_type')->default(1);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });
        Schema::create(config('org.database.roles_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('platform_id');
            $table->string('name',50);
            $table->string('slug',50);
            $table->tinyInteger('is_admin')->default(0);
            $table->timestamps();
        });
        Schema::create(config('org.database.role_duty_table'), function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('duty_id');
            $table->timestamps();
        });
        Schema::create(config('org.database.role_menu_table'), function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('menu_id');
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
        Schema::dropIfExists(config('org.database.users_table'));
        Schema::dropIfExists(config('org.database.user_infos_table'));
        Schema::dropIfExists(config('org.database.menu_table'));
        Schema::dropIfExists(config('org.database.platform_menu_table'));
        Schema::dropIfExists(config('org.database.platforms_table'));
        Schema::dropIfExists(config('org.database.companies_table'));
        Schema::dropIfExists(config('org.database.departments_table'));
        Schema::dropIfExists(config('org.database.duties_table'));
        Schema::dropIfExists(config('org.database.roles_table'));
        Schema::dropIfExists(config('org.database.role_duty_table'));
        Schema::dropIfExists(config('org.database.role_menu_table'));
    }
}
