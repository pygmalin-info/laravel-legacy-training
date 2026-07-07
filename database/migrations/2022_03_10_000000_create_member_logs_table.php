<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 会員操作ログ
 *
 * 会員の登録・更新などの操作履歴を残すためのテーブル。
 * （この教材ではマイグレーションの修正は不要。DB設計としては妥当。）
 */
class CreateMemberLogsTable extends Migration
{
    public function up()
    {
        Schema::create('member_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->string('action', 30);
            $table->string('detail')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('member_logs');
    }
}
