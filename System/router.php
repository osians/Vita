<?php

class Router
{
    private $controller, $action, $folders = "";
    private $controller_folder = null;
    private $params = array();


    public function __construct(){
        $this->analisar();
    }

    private function analisar()
    {
        $__request = &$_GET['request'];
        $_split = explode( '/', trim( $__request, '/' ) );

        $__app_path = vita()->config->app_folder;
        if(!isset($__app_path) || empty($__app_path))
            throw new \Framework\Vita\Core\SysException( '
                Você não definiu a variável $_config[\'app_folder\']
                com a localização da sua Aplicação. ', 22 );

        $__controller_folder = vita()->config->controller_folder ;
        if(!isset($__controller_folder) || empty($__controller_folder))
            throw new \Framework\Vita\Core\SysException( '
                Você não definiu a variável $_config[\'controller_folder\']
                com a localização da pasta de Controle da sua Aplicação. ', 30 );

        $this->controller_folder = $__app_path.$__controller_folder . DIRECTORY_SEPARATOR;

        // verificando se primeiro indice e' uma pasta
        $__is_folder = $this->controller_folder . $_split[0];

        while( !empty($_split[0]) && is_dir( $__is_folder ) ):
            $this->folders .= $_split[0] . DIRECTORY_SEPARATOR;
            array_shift ( $_split );
            if(isset($_split[0]))
                $__is_folder .= DIRECTORY_SEPARATOR . $_split[0];
        endwhile;

        $this->controller = !empty($_split[0]) ? ucfirst($_split[0]) : 'Index';
        array_shift( $_split );
        $this->action = !empty($_split[0]) ? $_split[0] : 'index';
        array_shift( $_split );
        $this->params = $_split;
        unset($_split);

        # setando a URL sem parametros
        # algum processo pode precisar.
        vita()->request_uri = vita()->base_url.$__request.'/';
        vita()->request_uri_ca = vita()->base_url . $this->getController() . '/' . $this->getAction() . '/' ;
        vita()->request_controller = $this->getController();
        vita()->request_action = $this->getAction();
    }


    public function router(){

        $file = $this->controller_folder . $this->folders . strtolower($this->controller).'.php';

        if(is_readable($file) ){
            include $file;
            $__class__ = $this->controller;
        }
        else{
            $file2 = $this->controller_folder . 'error404.php';
            if(is_readable($file2)){
                include $file2;
            }else{
                throw new \Framework\Vita\Core\SysException(
                    "Você ainda não criou os arquivos '$file' e '$file2'.
                    Proceda com a criação dos mesmos para corrigir o problema.", 68
                );
            }
            $__class__ = 'Error404';
            $this->action = "index";
        }

        if(class_exists($__class__))
            $controller = new $__class__();
        else
            throw new \Framework\Vita\Core\SysException("A classe '$__class__' não existe dentro do arquivo '$file'", 77);


        if(is_callable(array($controller,$this->action))):
            $action = $this->action;
        else:
            // $action = 'index';
            throw new \Framework\Vita\Core\SysException("O método '$this->action' não existe dentro da classe '$__class__' no arquivo '$file'", 84);
        endif;

        call_user_func_array( array($controller,$action), $this->params );
    }

    public function getController(){ return $this->controller;}
    public function getAction(){return $this->action;}


}



