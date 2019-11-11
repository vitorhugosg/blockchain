<?php

namespace App\Http\Controllers\V0\Blockchain;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BlockChain\Block;
use App\Libraries\LevChain\levCoinClass;
class BlockChainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $blocks = Block::paginate();
        foreach ($blocks as $key => $value) {
            $blocks[$key]->block = json_decode($value->block);
        }
        return $this->api_response($blocks);
    }

    public function teste(Request $request){
        $data = [
            'testando' => "Esse é um teste"
        ];
        $lev = new levCoinClass;
        $lev->createBlock($data, false);
        $block = Block::get()->toArray();
        foreach ($block as $key => $value) {
            $block[$key] = $this->ToUl($value);
        }
        
        return view('welcome', [
            'block' => $block
        ]);
    }

    public function ToUl($arr){
        $return = '<ul>';
        foreach ($arr as $item)
        {
            $return .= '<li>' . (is_array($item) ? $this->ToUl($item) : $item) . '</li>';
        }
        $return .= '</ul>';
        return $return;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lev = new levCoinClass();
        
        $block = $lev->createBlock($request->all());
        // $block['block'] = json_encode(json_decode($block['block'])); 
        return $this->api_response($block);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $hash
     * @return \Illuminate\Http\Response
     */
    public function show($hash)
    {
        $block =  Block::where('hash', $hash);
        return $this->api_response($block);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
}
