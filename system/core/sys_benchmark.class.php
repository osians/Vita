<?php

namespace Framework\Vita\Core ;

/**
 * Classe simples apenas para contar o tempo
 * de processamento da pagina. Pode ser usada para contar tempo entre
 * processos e funcoes
 *
 */
class Benchmark
{
	// @var microtime - momento exato em que esta classe foi instanciada
	private static $start_time ;

	// @var bool - indica se classe foi instanciada
	private static $initialized = false;

	/**
	 * Inicializa a classe e seus mecanismos estaticos
	 * @return void
	 */
	public static function init()
	{
        if(self::$initialized) return;
		self::$start_time = microtime(true);
        self::$initialized = true;
	}

	/**
	 * Retorna o tempo decorrido ate o momento atual
	 *
	 * @return float
	 */
	public static function elapsed_time()
	{
		$end_time = microtime(true);
		return $end_time - self::$start_time;
	}

	/**
	 * Dados dos tempos diferentes, retornar a diferenca entre eles
	 * @param  microtime $start - tempo inicial
	 * @param  microtime $end  - tempo final
	 * @return microtime - total tempo decorrido
	 */
	public static function elapsed($start, $end){
		return $end - $start;
	}

	/**
	 * Retorna o tempo agora/atual
	 *
	 * @return float
	 */
	public static function get_time(){
		return microtime(true);
	}
}

# inicializando ...
Benchmark::init();