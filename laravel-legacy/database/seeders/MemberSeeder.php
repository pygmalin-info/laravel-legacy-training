<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run()
    {
        // ページネーション・N+1 の確認用にある程度の件数を投入する
        Member::factory()->count(60)->create();

        // 動作確認用の固定データ
        Member::create([
            'name' => '教材 太郎',
            'name_kana' => 'キョウザイ タロウ',
            'email' => 'taro@example.com',
            'phone' => '090-1111-2222',
            'gender' => 1,
            'birthday' => '1990-04-01',
            'postal_code' => '100-0001',
            'prefecture' => '東京都',
            'address' => '千代田1-1-1',
            'rank' => 3,
            'status' => 1,
            'memo' => '優良会員。<b>太字メモ</b>',
        ]);

        // メモに HTML/スクリプトが入っているデータ（表示のされ方を確認する用）
        Member::create([
            'name' => 'テスト 花子',
            'name_kana' => 'テスト ハナコ',
            'email' => 'hanako@example.com',
            'phone' => '080-3333-4444',
            'gender' => 2,
            'birthday' => '1988-12-24',
            'postal_code' => '150-0001',
            'prefecture' => '東京都',
            'address' => '渋谷2-2-2',
            'rank' => 1,
            'status' => 1,
            'memo' => '<script>alert("memo")</script>クレーム対応履歴あり',
        ]);

        // 退会済み会員（検索の挙動確認用）
        Member::create([
            'name' => '退会 三郎',
            'name_kana' => 'タイカイ サブロウ',
            'email' => 'saburo@example.com',
            'phone' => '070-5555-6666',
            'gender' => 1,
            'birthday' => '1975-07-07',
            'postal_code' => '060-0001',
            'prefecture' => '北海道',
            'address' => '北1-1',
            'rank' => 1,
            'status' => 2,
            'memo' => '',
        ]);
    }
}
