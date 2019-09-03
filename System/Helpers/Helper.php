<?php

use \Vita\System\Vita ;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vita_forms.php';

# @function getInstance - obtem instancia do objeto principal do sistema
function vita(){return Vita::getInstance();}

# converter um array para um objeto
# @param array
function oo($_array_){return json_decode(json_encode($_array_),FALSE);}

# verifica se uma string esta em formato utf8
function is_utf8( $string ){return preg_match('!!u', $string)?true:false;}

if(!function_exists('strtoupperr')){
    function strtoupperr($in){
        if(!(is_string($in)||is_array($in))) return $in;
        if(is_string($in))
            return strtoupper($in);
        if(is_array($in)){
            return array_map('strtoupper',$in);
        }
    }
}

if(!function_exists('strtolowerr')):
    function strtolowerr($in){
        if(!is_string($in)||!is_array($in)) return $in;
        if(is_string($in))
            return strtolower($in);
        if(is_array($in)){
            array_map('strtolower',$in);
        }
    }
endif;


if ( ! function_exists('redirect'))
{
    /**
    * Redireciona para uma nova URL
    *
    * @param  string $__url__ - URL destino
    * @return void
    */
    function redirect($uri = '', $method = 'location', $http_response_code = 302)
    {
        switch($method)
        {
            case 'refresh':
                header("Refresh:0;url=".$uri);
            break;
            default :
                header("Location: ".$uri, TRUE, $http_response_code);
            break;
        }
        exit;
    }
}


if(! function_exists( 'uri' ))
{
    /**
     * Obtem um segmento da URL ou toda ela
     *
     * @param  int|null - se null retorna toda URL se numero retorna segmento
     * @return string
     */
    function uri( $__segment__ = null )
    {
        # obtendo algo como : http://nomedosite.com/arquivo_solicitado
        $__uri__ = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        # se foi solicitado um segmento da URI...
        if(isset($__segment__)&&is_numeric($__segment__))
        {
            $_uri_ = explode( "/", "$__uri__" );
            return isset($_uri_[$__segment__+1]) ? $_uri_[$__segment__+1] : '';
        }
        else
        { # retorna URI completa
            return trim( $__uri__ );
        }
    }
}


/**
 * É inteiro? Verifica se um valor String trata-se de um valor inteiro
 */
function __eint($s){return filter_var($s, FILTER_VALIDATE_INT) !== false;}
function __enum($s){return eint($s);}


/**
*
* Quando uma traducao é solicitada atraves do TWIG o mesmo
* chama a função abaixo e solicita a tradução de um dado
* trecho de texto.
* Ex, no frontend: {{ 'texto a ser traduzido' | vtrans }}
* vtrans - é um alias para a função "vita_twig_translate_filter"
*
* Esta funcao, verifica se o texto se encontra dentro do
* arquivo/catalogo de traducoes que por padrao se localiza em
* config/locale/, caso encontre, retorna a informacao traduzida,
* caso contrario a mesma string de entrada é retornada para a
* funcao que chamou.
*
* @param  string $val - texto a traduzir
* @return string      - string traduzida
*/
function vita_twig_translate_filter( $val = null)
{
    # esta ativado configuracao de traducao ?
    if(!vita()->config->twig_localization_enabled) return $val;

    # nosso parametro e' valido?
    if(is_null($val)||(!is_string($val))||empty($val)) return $val;


    # deixamos em lowercase por ser a parte a comparar
    $val = mb_strtolower(trim($val));

    # verifica se temos um catalogo carregado na memoria
    # isso evita que o sistema fique solicitando abertura e
    # fechamento de resource a todo momento que for traduzir
    # uma unica palavra.
    $catalogo = vita()->vita_translate_catalogo_arr;

    # se ainda nao temos um catalogo, carrega
    if(null == $catalogo):

        # arquivo alvo
        # tenta primeiro carregar arquivo da app que solicitou o vita
        $__locale_file = vita()->config->app_folder.vita()->config->twig_localization_locale_path.vita()->config->twig_localization_locale.'.yml';
        if(!file_exists($__locale_file)):
            $__locale_file = vita()->config->vita_path.vita()->config->twig_localization_locale_path.vita()->config->twig_localization_locale.'.yml';
            if(!file_exists($__locale_file))
                throw new Exception("Catalogo de traduções não foi encontrado em: '$__locale_file'", 1);
        endif;

        if (($handle = fopen($__locale_file, "r")))
        {
            $catalogo = array();
            while (($line = fgets($handle)) !== false):
                if(false === strpos($line, ":")) continue;
                list($text,$traducao) = explode(":", $line);
                $text = mb_strtolower(trim(str_replace(array('"',"'"), "", $text)));
                $traducao = trim(str_replace(array('"',"'"), "", $traducao));
                $catalogo[] = array('text'=>$text, 'traducao' => $traducao);
            endwhile;
            fclose($handle);
            vita()->vita_translate_catalogo_arr = $catalogo;

        }else throw new Exception("Não foi possível abrir o catalogo de traduções: '$__locale_file'", 1);

    endif;

    # percorre catalogo de traducoes a procura do texto solicitado
    foreach ($catalogo as $item)
        if($item['text'] == $val)
            return $item['traducao'];
    return $val;
}
