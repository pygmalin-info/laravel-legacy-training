<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members';

    // mass assign 用。あとで項目を足したりしているうちに、少し歯抜けになっている。
    protected $fillable = [
        'name',
        'email',
        'gender',
        'birthday',
        'postal_code',
        'prefecture',
        'address',
        'rank',
        'status',
        'memo',
        'avatar',
    ];

    // 表示用にフルの住所を組み立てる。Controller 側でも同じような組み立てをしている箇所がある。
    public function fullAddress()
    {
        return $this->prefecture . $this->address;
    }
}
