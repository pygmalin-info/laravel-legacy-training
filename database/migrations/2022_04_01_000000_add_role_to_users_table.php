<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ユーザーに権限（role）を追加。
 * admin = 管理者 / staff = 一般スタッフ
 * （認可の練習課題用。DB設計としては妥当なのでこのマイグレーションの修正は不要）
 */
class AddRoleToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->after('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
