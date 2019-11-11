<?php

namespace App\Models\BlockChain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BlockType extends Model
{
	use SoftDeletes;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'transaction_id',  'status', 'value', 'dt_pay', 'verify_pay', 'dt_verify' ];


    
}
