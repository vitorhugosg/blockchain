<?php

namespace App\Models\BlockChain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Block extends Model
{
    //

    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'hash',  'block_type_id', 'intercept', 'previewsHash', 'block' ];
}
