<?php

use Illuminate\Database\Seeder;
use App\Models\BlockChain\BlockType;


class BlockTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
        	[
        		'name' => 'primary',
        		'description' => 'Esse é o primeiro bloco da sequência.'
        	],
        	[
        		'name' => 'normal',
        		'description' => 'Bloco normal de sequência.'
        	]
        ];
        foreach ($data as $key => $value) {
        	BlockType::create($value);
        }
        
    }
}
