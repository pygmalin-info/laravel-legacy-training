<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 会員テーブル
 *
 * DB設計としては概ね妥当です。この教材ではマイグレーションの修正は不要とします。
 * （email の一意制約は「アプリ側のバリデーションで担保する」という当時の方針で
 *   あえて付けていません。実務でもよくある判断です。）
 */
class CreateMembersTable extends Migration
{
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_kana')->nullable();
            $table->string('email')->index();
            $table->string('phone', 20)->nullable();
            // gender: 1=男性, 2=女性, 3=その他
            $table->unsignedTinyInteger('gender')->default(3);
            $table->date('birthday')->nullable();
            $table->string('postal_code', 8)->nullable();
            $table->string('prefecture', 20)->nullable();
            $table->string('address')->nullable();
            // rank: 1=通常, 2=ゴールド, 3=プラチナ
            $table->unsignedTinyInteger('rank')->default(1);
            // status: 0=仮登録, 1=有効, 2=退会
            $table->unsignedTinyInteger('status')->default(1);
            $table->text('memo')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
}
