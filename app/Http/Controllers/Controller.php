<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Retorna uma resposta HTTP
     * @param [array] $data Array a ser retornado no formato JSON
     * @param [int] $code Codigo HTTP da resposta
     * @param [string] $message Mensagem da resposta
     * @return [response]
     */
    protected function api_response($data, $code = 200, $message = "OK"){
    	return response()->json($data)->setStatusCode($code, utf8_decode( $message ));
    }


        /**
     * Pagina os resultados de uma QUERY
     * @param [object] $query Query
     * @param [object] $request Objeto de request
     * @return [array]
     */
    protected function paginate_results($query, Request $request = null){

    	$page = 1;
    	$number_per_page = env("NUMBER_PER_PAGE_API_RESPONSE");

    	if( $request && $request->has('page') ){
    		$page = $request->input('page');
    	}

    	if( $request && $request->has('pagination.page') ){
    		$page = $request->pagination['page'];
    	}

    	if( $request && $request->has('pagination.number_per_page') ){
    		$number_per_page = $request->pagination['number_per_page'];
    	}      

    	if( $number_per_page == "0" || $number_per_page === 0 || !$number_per_page ){
    		$number_per_page = $query->count();
    	}

    	return $query->paginate($number_per_page, ['*'], 'page', $page);
    	
    }
    public function arrayToJson($data){
        return json_encode(json_decode($data, true));
    }
}
