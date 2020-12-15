<?php

namespace System\Core;

/**
 * Classe simples apenas para contar o tempo
 * de processamento da pagina. Pode ser usada para contar tempo entre
 * processos e funcoes
 *
 * start() - Inicializa a contagem
 * elapsedTime() - retorna tempo decorrido ate o momento
 * elapsed($start, $end) - calcula tempo decorrido entre 2 microtimes
 * getMicrotime() - Pega microtime atual
 */
class Benchmark
{
    /**
     * @var microtime - momento exato em que esta classe foi instanciada
     */
    private static $_startTime;

    /**
     * @var bool - indica se classe foi instanciada
     */
    private static $_initialized = false;

    /**
     * Inicializa a classe e seus mecanismos estaticos
     *
     * @return void
     */
    public static function start()
    {
        if (self::$_initialized) {
            return;
        }

        self::$_startTime = microtime(true);
        self::$_initialized = true;
    }

    /**
     * Retorna o tempo decorrido ate o momento atual
     *
     * @return float
     */
    public static function elapsedTime()
    {
        $endTime = microtime(true);
        return $endTime - self::$_startTime;
    }

    /**
     * Dados dos tempos diferentes, retornar a diferenca entre eles
     *
     * @param  microtime $start - tempo inicial
     * @param  microtime $end  - tempo final
     *
     * @return microtime - total tempo decorrido
     */
    public static function elapsed($start, $end)
    {
        return $end - $start;
    }

    /**
     * Retorna o tempo agora/atual
     *
     * @return float
     */
    public static function getMicrotime()
    {
        return microtime(true);
    }
}
