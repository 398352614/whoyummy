<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
    {
        protected $table = 'card';
        const CREATED_AT = 'created_at';
        const UPDATED_AT = 'updated_at';
        protected $primaryKey ='id';
        protected $fillable =[
            'card_id',
            'card_name',
            'card_function',
            'card_text',
            'Column_value',
            'card_status',
            'card_belong'
    ];
    //
}
