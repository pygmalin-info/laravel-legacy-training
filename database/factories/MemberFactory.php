<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition()
    {
        $prefs = ['東京都', '神奈川県', '大阪府', '愛知県', '福岡県', '北海道', '埼玉県', '千葉県'];
        $seis = ['佐藤', '鈴木', '高橋', '田中', '伊藤', '渡辺', '山本', '中村', '小林', '加藤'];
        $meis = ['太郎', '花子', '一郎', '恵子', '健', '愛', '大輔', '美咲', '翔', 'さくら'];

        $sei = $this->faker->randomElement($seis);
        $mei = $this->faker->randomElement($meis);
        $name = $sei . ' ' . $mei;

        return [
            'name' => $name,
            'name_kana' => 'カナ',
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '090-' . $this->faker->numerify('####-####'),
            'gender' => $this->faker->numberBetween(1, 3),
            'birthday' => $this->faker->date('Y-m-d', '2005-01-01'),
            'postal_code' => $this->faker->numerify('###-####'),
            'prefecture' => $this->faker->randomElement($prefs),
            'address' => $this->faker->numerify('中央##-#-#'),
            'rank' => $this->faker->numberBetween(1, 3),
            'status' => $this->faker->randomElement([0, 1, 1, 1, 2]),
            'memo' => '',
        ];
    }
}
