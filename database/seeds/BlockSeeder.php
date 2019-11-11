<?php

use Illuminate\Database\Seeder;
use App\Models\BlockChain\Block;
use App\Libraries\LevChain\levCoinClass;
use Carbon\Carbon;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$data = [
    		'block_type_id' => 1,
    		'description' => 'Esse é o primeiro bloco criado para o blockchain Creddent'
    	];
    	$lev = new levCoinClass;
    	$lev->createBlock($data, true);
    }
}
