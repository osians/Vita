<?php 

namespace System\Core;

class Utils
{
    /**
     * Funcao utilizada para mascarar informacoes
     * exemplo de uso: echo mask( '12345678912','###.###.###-##');
     *
     * @param  String - campo a ser mascarado ex: 1234567890
     * @param  String - como deve ser mascarado. ex: (##) ####-####
     *
     * @return String - texto mascadado
     */
    public function mask($val, $mask)
    {
        $val = trim($val);
        if($val == null || $val == 'ND' || strlen($val) == 0) {
            return $val;
        }
        
        $maskared = '';
        $k = 0;
        
        for ($i = 0; $i <= strlen($mask)-1; $i++) {
            if($mask[$i] == '#') {
                if (isset($val[$k])) {
                    $maskared .= $val[$k++];
                }
                continue;
            }
            
            if (isset($mask[$i])) {
                $maskared .= $mask[$i];
            }
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

    /**
     * Creates a Friendly String
     *
     * @param string $str
     * @param string $separador
     *
     * @return String
     */
    public function friendlyString($str, $separador = "_")
    {
        $friendly = vita()->sanitize->substituirAcentos(trim($str));
        $friendly = preg_replace('@\s+@', ' ', $friendly);
        $friendly = str_replace(array(',','.'), '', $friendly);
        return str_replace(' ', $separador, $friendly);
    }

    /**
     *
     * DUMP VARIAVEIS
     *
     * @param array|string|object $param - aceita array ou string
     * @param string $filename - nome opicional para o arquivo
     * @todo  - Corrigir essa funcao
     */
    function dump($param, $filename = 'dump')
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

    /**
     * Insert a String inside another String. Once you give a Offset
     *
     * @param string  $insertString
     * @param string  $intoString
     * @param integer $offset
     * 
     * @return string
     */
    function strInsert($insertString, $intoString, $offset)
    {
       return substr($intoString, 0, $offset) . $insertString . substr($intoString, $offset);
    }

    /**
     * Get Client IP
     *
     * @return string - IP Format
     */
    function getClienteIp()
    {
        $ip = "undefined";

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }
}
