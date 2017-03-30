<?php if ( ! defined('ALLOWED')) exit('Acesso direto ao arquivo nao permitido.');

// Declaracao de alguns ENUMS Uteis para padronizacao
class sys_vita_config_visibilidade_enum{
    const PRIVADA   = 0;
    const PROTEGIDA = 1;
    const PUBLICA   = 2;
}

class InvalidVarNameException extends LogicException{}

class SYS_Config implements Vitalib
{
    private $mode = sys_vita_config_visibilidade_enum::PRIVADA;
    private $configs = array();

    function __construct($config = null){
        $this->setConfig($config);
    }

    /**
     * Transforma um Array em variaveis dentro desta classe
     * @param Array $config - Array contendo configuracoes
     */
    public function setConfig($config = null){
        if($config!=null && is_array($config))
            foreach ($config as $key => $value)
                $this->set($key,$value);
    }

    public function __get($name){
        return $this->get( $name );
    }

    public function __set($name,$value){
        $this->set( $name, $value );
    }

    public function get( $name ){
        $name = str_replace(' ','',strtolower(preg_replace("/[^a-zA-Z_-]+/", "", $name)));
        if(!isset($this->configs[$name]))
            throw new InvalidVarNameException("A configuração '{$name}' que você está tentando obter não existe. Ela não foi definida no arquivo 'config.php' nem adicionada de forma dinamica no sistema.");            
        return $this->configs[$name];
    }

    public function set( $name, $value ){
        # padroniza os nomes de variavels, apenas letras e underline
        $name = str_replace(' ','',strtolower(preg_replace("/[^a-zA-Z_-]+/", "", $name)));

        if( $this->mode == sys_vita_config_visibilidade_enum::PRIVADA ):
            $this->configs[$name] = $value;
        else:
            $this->$name = $value;
        endif;
    }

    /**
     * Torna publica as propriedades deste objeto
     * que podem ser acessadas via twig template.
     * Este metodo é executado automaticamente, no final
     * de todos os processos, quando o sistema for
     * compilar o template final.
     *
     * {{ vita.config.propriedade }}
     *
     * @return array()
     */
    public function publicar(){
        $this->mode = sys_vita_config_visibilidade_enum::PUBLICA;
        foreach($this->configs as $k => $v):
            $this->$k = $v;
        endforeach;
    }
}