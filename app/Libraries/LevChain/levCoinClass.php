<?php 
namespace App\Libraries\LevChain;

use App\Models\BlockChain\Block;
use App\Models\BlockChain\BlockType;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
//CLASSE DE BLOCKCHAIN PARA SEGURANÇA DE DADOS
// DESENVOLVIDO POR : LevanteLab

class levCoinClass{
	

	



	//FUNÇÃO DE CRIAÇÃO
	public function createBlock($data, $verifyAll = false){

		$previewsHash = $this->getPreviousHash();

		//pegando tipo 
		if(!$previewsHash){
		//se for o primeiro bloco
			$previewsHash = 0;
			$block_type_id = 1;
		}else{
		//segundo em diante
			$block_type_id = 2;
		}
		//setando primeira tentativa
		$hashgerado = $this->generateHash($data);
		$data['previewsHash'] = $previewsHash;
		$data['hash'] = $hashgerado['hash'];
		$data = $this->natkrsort($data);
		$block = [
			'block' => json_encode($data),
			'previewsHash' => $previewsHash,
			'block_type_id' => $block_type_id,
			'hash' => $hashgerado['hash'],
			'intercept' => $hashgerado['intercept']
		];
		//verificando se o block é válido
		$result = $this->isValid($block['hash'], $data);
		while ($result == false) {
			//caso hash não seja válido
			$hashgerado = $this->generateHash($data);
			//setando intercept para verificação
			
			$block['hash'] = $hashgerado['hash'];
			
			$block['intercept'] = $hashgerado['intercept'];
			$data['hash'] = $hashgerado['hash'];
			$data = $this->natkrsort($data);
			$block['block'] = json_encode($data);
			//gere até gerar um hash válido
			$result = $this->isValid($block['hash'], $data);
		}

		
		$newblock = Block::create($block);
		if ($verifyAll) {

			if ($previewsHash !== 0) {
				$validateAll = $this->validateAllBlocks($block);
				
			}
		}


		return $block;



	}


	//formula geradora de hash
	public function generateHash($data, $intercept = null){
		//se for criar hash para verificação
		if (empty($intercept)) {
			$radom_int = random_int(env('BLOCKCHAIN_MIN_NUMBER'),env('BLOCKCHAIN_MAX_NUMBER'));
			$time = rand(0,9999);
			$return = [
				'hash' => md5($radom_int . $time . env('BLOCKCHAIN_DIVISOR') . serialize($data)),
				'intercept' => $radom_int . $time
			];
		}else {
			//se for para testar hash de criação
			$data = json_decode($data['block'],true);
			$data = $this->natkrsort($data);
			

			$return = [
				'hash' => md5($intercept . env('BLOCKCHAIN_DIVISOR') . serialize($data)),
				'intercept' => $intercept
			];

		}
		
		return $return;

	}

	//validação do hash é realmente certo 
	public function isValid($hash, $data, $intercept = null, $checkTRUE = false){
		$error = false;
		//pega o tamanho do blockchain rules pra verificar se é verdadeiro
		$variavel = substr($hash, 0, strlen(env('BLOCKCHAIN_RULES')));
		//verifca se os dois primeiros numeros são iguais
		if($variavel != env('BLOCKCHAIN_RULES')){
			$error = true;

		}
		
		//se tem intercept verificar diferente
		if($countBlocks = Block::count() > 1 && isset($data['previewsHash'])){
			$previewsHash = Block::where('hash', $data['previewsHash'])->first();
			
			if (!$previewsHash) {

				$error = true;
			}
		}
		

		if($checkTRUE){
			
			//AGORA ESTOU AQUI FAZENDO FORÇA PARA COLOCAR
			$resultadoCreateHash = $this->generateHash($data, $intercept);
			
			if ($resultadoCreateHash['hash'] != $hash) {
				

				$error = true;
			}

		}


		
		return !$error;

	}
	//checkIsValid se já existe e é válido
	public function checkIsValidExist($hash){
		
		if ($block = Block::where('hash', $hash)->first()) {
			$block = $block->attributesToArray();

			// $block['block'] = json_decode($block['block'], true);
			//tirando os times adicionado pelo banco de dados
			unset($block['deleted_at']);
			unset($block['created_at']);
			unset($block['updated_at']);
			
			return $this->isValid($block['hash'], $block, $block['intercept'], true);
		}else{
			return false;
		}

	}

	//função que valida todos os blocks
	public function validateAllBlocks(){
		$error = false;
		$quantity = 0; 
		$blocksComProblemas = [];
		//pegando array do ultimo hash
		$preview = Block::where('hash',$this->getPreviousHash())->first();
		
		if ($result = $this->verifyHashLast($preview)) {
			$quantity++;
			while ($result) {
				$quantity++;
				if (!$result = $this->verifyHashLast($preview)) {
					break;
				}
				if (!$this->checkIsValidExist($preview->hash)) {
					Log::critical('VERIFICAR HASHS, tem algum com problema no caminho :::: HASH:'. $preview->hash);
					$error = true;
					$blocksComProblemas = [];
				}
				
				
				//pegando ultimo preview
				if ($this->verifyHashLast($preview)) {
					$preview = $this->getBlockHash($preview->previewsHash);

				}


			}
			$return = [
				'quant' => $quantity,
				'error' => $error
			];
			return $return;
		}else{
			return [
				'quant' => $quantity,
				'error' => $error
			];
		}
		
		
	}

	//verifica se o ultimo bloco é diferente do 0
	public function verifyHashLast($preview){
		if (isset($preview->previewsHash) && !empty($preview->previewsHash)) {
			return true;
		}else{
			return false;
		}
		
	}


	//pega o hash aterior
	public function getPreviousHash(){
		$preview = Block::orderBy('id', 'DESC')->first();

		if ($preview) {
			
			return $preview->hash;
		}else{
			return false;
		}
	} 
	
	//pega um bloco especifico por hash
	public function getBlockHash($hash){
		return Block::where('hash', $hash)->first();
	}

	public function natkrsort($array)
	{
		$keys = array_keys($array);
		natsort($keys);

		foreach ($keys as $k)
		{
			$new_array[$k] = $array[$k];
		}

		$new_array = array_reverse($new_array, true);

		return $new_array;
	}
}