<?php

namespace Vita\System\Core;

class Utils
{
    public function __construct(){}

    /**
     * Funcao utilizada para mascarar informacoes
     * exemplo de uso: echo mask( '12345678912','###.###.###-##');
     * @param  String - campo a ser mascarado ex: 1234567890
     * @param  String - como deve ser mascarado. ex: (##) ####-####
     * @return String - texto devidamente mascadado
     */
    public function mask( $val, $mask )
    {
        $val = trim($val);
        if($val == null || $val == 'ND' || strlen($val)==0) return $val;
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++){
            if($mask[$i] == '#'):
                if(isset($val[$k])):
                    $maskared .= $val[$k++];
                endif;
            else:
                if(isset($mask[$i])):
                    $maskared .= $mask[$i];
                endif;
            endif;
        }
        return $maskared;
    }

    public function randomKey($length = 10)
    {
        $alphabets = range('A','Z');
        $numbers = range('0','9');
        // $additional_characters = range('a','z');//array('#','$','@');
        // $final_array = array_merge($alphabets,$numbers,$additional_characters);
        $final_array = array_merge($alphabets,$numbers);

        $password = '';

        while($length--) {
          $key = array_rand($final_array);
          $password .= $final_array[$key];
        }
        return $password;
    }

    public function amigavel($str)
    {
        $__amigavel = vita()->validate->replace_accents($str);
        $__amigavel = preg_replace('@\s+@', ' ', $__amigavel);
        $__amigavel = str_replace(" ", "_", $__amigavel);
        return $__amigavel;
    }

    /**
     *
     * DUMP VARIAVEIS
     *
     * @param array|string|object $param - aceita array ou string
     * @param string $filename - nome opicional para o arquivo
     * @todo  - Corrigir essa funcao
     */
    function dump( $param, $filename = 'dump' )
    {
        // <!-- determina o tipo de parametro recebido
        //  caso seja array ...
        if( is_array( $param ) )
        {
           $data = array();
            foreach ($param as $c => $v) {
                $data[] = $c . ': ' . $v;
            }

            $output = implode("\n", $data);
        }
        // caso seja string ...
        if( is_string($param) ){
            $output = $param; // se enviar um xml ou string entao grava ele no arquivo
        }
        // caso seja objeto ...
        if(is_object($param)){
            // converte obj para array fazendo cast
            // opcionalmente poderia usar
            /*  if( is_object( $object ) )
                {
                    $object = get_object_vars( $object );
                }
                return array_map( 'objectToArray', $object );
            */
            $array = (array) $param;
            $data = array();
            foreach ( $array as $i => $v  ):
                $data[] = $i . ":" . $v;
            endforeach;
            $output = implode( "\n", $data );
        }
        // -->

        vita()->log->write( $output );
    }

    function str_insert($insertstring, $intostring, $offset)
    {
       $part1 = substr($intostring, 0, $offset);
       $part2 = substr($intostring, $offset);

       $part1 = $part1 . $insertstring;
       $whole = $part1 . $part2;
       return $whole;
    }

    function get_cliente_ip(){
        $ip = "indefinido";
        if (!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}