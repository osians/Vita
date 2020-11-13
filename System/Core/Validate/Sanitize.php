<?php 

namespace Vita\Core\Validate;

/**
* Sanitarização de Dados
* ----------------------
*
* SANITIZAR - Remove dados potencialmente prejudicais
* ao sistema. Todos os métodos fazer uso dessa categoria.
*
* @package     Vita
* @author      wandeco sans - http://sooho.com.br - <sans.pds@gmail.com>
* @copyright   Copyleft (c) 2014
* @license
* @link
* @since       Version 201705112153 (última revisão)
*
* @todo - criar método para a validação de tempo. ex: 21:53:07
* @todo - criar método para a validar endereços IP
* @todo - criar método para a validação de URLs
*/
class Sanitize
{
	protected $_filterVarIsEnabled = false;

	public function __construct()
    {
		$this->_filterVarIsEnabled = function_exists('filter_var');
	}

    /**
     * MySQL Real Scape String
     * -----------------------
     *
     * Essa é uma implementação Generica do MySQL
     * real Scape String. toda informação que entrar
     * nesta classe passa obrigatóriamente antes por
     * esse método. Isso é feito automaticamente.
     *
     * @access protected
     * @link(MySQL, https://dev.mysql.com/doc/refman/5.7/en/mysql-real-escape-string.html)
     * @param  string
     * @return string
     */
    public function mres($value)
    {
        return str_replace(
            array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a"),
            array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z"),
            $value);
    }

    /**
     * XSS CLEAN
     * ----------
     * 
     * Realiza uma limpeza XSS para evitar cross site scripting
     * 
     * @access public 
     * @param Mixed
     * @return Mixed
     */
    public function xssClean($data)
    {
    	// remove tudo entre tags <script><style>
    	$data = preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $data);

    	// se temos filtro do php a disposicao, usamos
    	if (!$this->_filterVarIsEnabled) {
            return $this->_xssCleanOldWay($data);
        }

		if (!is_array($data)) {
	    	return filter_var($data, FILTER_SANITIZE_STRING);
        }

        foreach ($data as $k => $v) {
            $data[$k] = filter_var($v, FILTER_SANITIZE_STRING);
        }

        return $data;
    }

    /**
     * XSS CLean OLD Way
     * -----------------
     * 
     * Ainda existem casos de pessoas usando versos do PHP 
     * inferior a 5. A locaweb é uma empresa com excelência 
     * em não atualizar o ambiente de seus clientes.
     * E em homenagem a locaweb foi criado esse método.
     * 
     * @param  Mixed $data 
     * @return Mixed
     */
    protected function _xssCleanOldWay($data)
    {
    	if (!is_array($data)) {
			$data = trim(htmlentities(strip_tags($data)));
			if (get_magic_quotes_gpc()) {
				$data = stripslashes($data);
            }
			return $this->mres($data);
        }

		foreach($data as $k => $v) {
			$data[$k] = trim(htmlentities(strip_tags($data)));
			if (get_magic_quotes_gpc()) {
				$data[$k] = stripslashes($data[$k]);
            }
			$data[$k] = $this->mres($data[$k]);
	    }

	    return $data;
	}

    /**
     * TORNAR SEGURO
     * -------------
     * 
     * Essa funcao tem a finalidade de evitar sql
     * injection e fazer um trim nas strings recebidas.
     * Note que ela remove também as tags HTML do texto.
     * Então, caso o seu formulario permita a inserção 
     * de informações com tags html, use o método html().
     *
     * @param type $value
     * @return type
     */
    public function makeSafe($data = null)
    {
        if ($data == null) {
            return null;
        }

        $data = $this->xssClean($data);
        $data = strip_tags($this->mres(trim($data)));
       	return $data;
    }

    /**
     * LIMPA UM TEXTO
     * --------------
     *
     * Remove tags html de um texto, torna ele seguro para
     * o uso e realiza trim na informaçõa retornada.
     * Note que esse metodo tambem permite digitos e simbolos 
     * visto que eles são compreendidos como texto.
     * 
     * @param  string - texto a ser sanitizado
     * @return string
     */
    public function texto($param)
    {
        return $this->makeSafe($param);
    }

    /**
     * PERMITE APENAS LETRAS
     * ---------------------
     * Remove tudo que não for letras de A a Z 
     * e espaços.
     * Ex: 
     *    input : The @#@$%..+_- quick Brown FOX %%¨¨.
     *    output: The quick Brown FOX
     * 
     * @param  string - string a ser sanitizada
     * @return string
     */
    public function textOnly($param = null)
    {
        return is_null($param) ? ''
            : preg_replace('/[^\s\p{L}]/u', '', $this->makeSafe($param));
    }

    /**
     * APENAS TEXTO SEM ESPAÇOS
     * ------------------------
     *
     * Recebe uma string e remove tentativas de SQL injection 
     * além de remover todos os espaços existentes em uma string.
     * 
     * @param  string - String com espaços
     * @return string
     */
    public function textOnlyNoSpaces($param)
    {
        $tmp = $this->makeSafe($param);
        return str_replace(' ', '', $tmp);
    }

    /**
     * NUMEROS
     * -------
     * 
     * Remove tudo que nao seja digito ou sinal de menos
     * em uma string. Remove tambem espaços entre os números.
     *
     * @param  mixed - string ou int
     * @return int
     */
    public function numbersOnly($param = null)
    {
        if ($param === null) {
            return null;
        }

        $retorno = preg_replace('/[^\d]+/', '', $param);
        return $retorno;
    }
    
    /**
     * Sanitize the string by removing illegal characters from emails.
     *
     * Usage: '<index>' => 'sanitize_email'
     *
     * @param string $value
     * @param array  $params
     *
     * @return string
     */
    public function email($value)
    {
        if( $this->_filterVarIsEnabled ) {
            return filter_var($value, FILTER_SANITIZE_EMAIL);
        }
        else {
            $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
    
            preg_match( "/^[^@]*@[^@]*\.[^@]*$/", $value ) ;
            return $value ;
        }
    }

    /**
     * TELEFONE 
     * --------
     * 
     * retorna apenas numeros sem letras, caracteres 
     * especiais ou pontos e tracos
     * 
     * @param string $telefone
     * @return type
     */
    public function phone($telefone)
    {
       return trim( preg_replace("/[^0-9]+/i", "", $telefone));
    }

    /**
     * DINHEIRO
     * --------
     * 
     * Valida a informação como número, porém, deixa os 
     * operadores virgula e ponto.
     *
     * @param string|int $number
     * @return string - monetario
     */
    public function money($numero)
    {
       return trim(preg_replace("/[^0-9.,]+/i", "", $numero));
    }

    /**
     * FORMATA DECIMAL 
     * ---------------
     * 
     * Faz a "normatizacao"(não validação) de um valor decimal 
     * normalmente para ser inserido no Database.
     * exemplo entrada: "R$1.00 0,a00" saida: "1000.00"
     * 
     * @access public
     * @param string - valor a ser convertido em decimal
     * @return decimal
     */
    public function decimal($valor = null, $precisao = null)
    {
        # remove o que nao for necessario
        $valor = preg_replace('/[^\d-,.]+/', '', $valor);

        # temos um dado para trabalhar ?
        if(empty($valor)||null==$valor) return 0.00;

        # temos uma precisao setada ?
        # default
        $precisao = (null != $precisao && is_numeric($precisao)) 
            ? ".".str_repeat("0", $precisao) : "";

        # procurando por um ponto...
        $ponto_pos = strpos($valor,".");
        # porcurando por virgula...
        $virgu_pos = strpos($valor,",");
        # se nao temos pontos nem virgulas, apenas numero ex: -1000 ou 
        # 1000 entao retornamos o valor
        if( $ponto_pos === false && $virgu_pos === false 
            && (is_numeric(preg_replace('/[^0-9]/s','',$valor))))
            return $this->numbersOnly($valor) . $precisao;

        # verifica se temos realmente um numero para trabalhar!
        if(!is_numeric(preg_replace('/[^0-9]/s','',$valor))) return 0.00;

        # input 12,345.67 output 12345.67
        if($ponto_pos > 0 && $virgu_pos > 0 && $ponto_pos > $virgu_pos)
            return str_replace(",", "", $valor);

        # input 1.12345,67 output 112345.67
        if($ponto_pos > 0 && $virgu_pos > 0 && $ponto_pos < $virgu_pos)
            return str_replace(",",".",str_replace(".","",$valor));

        # input 12345.67 output 12345.67
        if($ponto_pos > 0 && $virgu_pos === false)
            return $valor;

        # input 12,34567 output 12.34567
        if($ponto_pos === false && $virgu_pos > 0)
            return str_replace(",", ".", $valor);

        return 0.00;
    }

    /**
     * VALIDAÇÂO DE TEXTO HTML
     * -----------------------
     * 
     * Se você precisa sanitizar um texto que permite 
     * a inclusão de Tags HTML, use esse método para isso.
     * Pois os metodos texto() removem as tags HTML.
     * 
     * @param  string - Esperado um texto com tags html
     * @return string
     */
    public function html($param)
    {
        return $this->mres(trim($param));
    }

    /**
     * FORMATA DATA NO PADRÂO YYYY-MM-DD HH:II:SS
     * ------------------------------------------
     * 
     * Normatiza uma model para o padrao aceito Americano
     * Note que se a model for invalida a função retorna a
     * model default unix 1970-01-01 00:00:00
     *
     * 
     * @access public
     * @param  string - $val string com a model a ser checada
     * @return string - model no formato yyyy-mm-dd hh:ii:ss
     */
    public function data($data = null)
    {
        # para caso em que a normatizacao nao pode prosseguir devido
        # a informacoes incorretas, esta model sera retornada.
        $__default_date_err_norm = '1970-01-01 00:00:00';

        if(null == $data ) 
            return $__default_date_err_norm;

        $data = str_replace(array('/','.',',','\\'),'-',trim($data));

        $data = str_replace(' ', '', $data);
        if(strlen($data)>10):
            $part1 = substr($data, 0, 10);
            $part2 = substr($data, 10);
            $data = $part1 . ' ' . $part2;
        endif;

        # temos model e tempo ? dd-mm-yyyy hh:ii:ss
        if((strpos($data," ") > 0) ){
            list($data,$time) = explode(" ",$data);
            // @todo - aqui incluir código para normatizar o tempo
        }

        # filtrando apenas por caracteres validos
        $data = preg_replace("/[^0-9-]+/i","",$data);

        # tamanho da model e valido ?
        if (strlen($data) < 8)
            return $__default_date_err_norm;

        # verificando qual info e' ano, mes e dia
        $_arr = explode("-",$data);

        $ano = strlen($_arr[2]) == 4 ? 2 : (strlen($_arr[0]) == 4 ? 0 : null);
        $mes = ($_arr[1] > 12 && $_arr[1] < 31) ? ($ano == 0 ? 2: 0) : 1;
        $dia = ($_arr[1] > 12 && $_arr[1] < 31) ? 1 : ($ano == 0 ? 2 : 0);

        # verifica se ano, mes e dia estao dentro de faixas aceitas
        if(($_arr[$ano]>0)&&($_arr[$mes]>0&&$_arr[$mes]<=12)
            &&($_arr[$dia]>0&&$_arr[$dia]<=31))
            return "{$_arr[$ano]}-{$_arr[$mes]}-{$_arr[$dia]}".(isset($time)?" {$time}":"");
        return $__default_date_err_norm;
    }

    /**
     * SUBSTITUIR ACENTOS
     * ------------------
     * 
     * Substitui as letras com acento por uma letra correspondente
     * sem acento. Método útil para a criação de URL's amigaveis.
     *
     * @access public
     * @link http://myshadowself.com/coding/php-function-to-convert-accented-characters-to-their-non-accented-equivalant/
     * @param string - Sentença a ser motificada
     * @return string
     */
    public function substituirAcentos($str)
    {
        $a = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ','Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ','Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı','Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł','Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ō','ō','Ŏ','ŏ','Ő','ő','Œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','Š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','Ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ơ','ơ','Ư','ư','Ǎ','ǎ','Ǐ','ǐ','Ǒ','ǒ','Ǔ','ǔ','Ǖ','ǖ','Ǘ','ǘ','Ǚ','ǚ','Ǜ','ǜ','Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ','Ά','ά','Έ','έ','Ό','ό','Ώ','ώ','Ί','ί','ϊ','ΐ','Ύ','ύ','ϋ','ΰ','Ή','ή');
        $b = array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o','Α','α','Ε','ε','Ο','ο','Ω','ω','Ι','ι','ι','ι','Υ','υ','υ','υ','Η','η');
        return str_replace($a, $b, $str);
    }
}
