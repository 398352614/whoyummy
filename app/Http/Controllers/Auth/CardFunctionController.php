<?php


namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\DB;

class CardFunctionController
{
    //洗牌
    public function wash(){
        DB::table('card')->update(['card_belong'=>'1']);
    }

    //抽卡（参数：玩家ID）
    public function draw($player_id)
    {
        $cardamount =DB::table('card')->count();
        DB::table('card')->where('id',rand(1,$cardamount))->update(['card_belong'=>$player_id]);
    }

}
