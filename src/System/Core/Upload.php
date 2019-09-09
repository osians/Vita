<?php

namespace Vita\System\Core;

/**
 * TODO - colocar uma funcao para tornar o nome da imagem amigavel
 */

/**
 * Classe responsavel pelo upload de arquivos para
 * o sistema
 **/
class Upload
{
    var $destination    = '';
    var $filename       = '';
    var $overrideFile   = FALSE;
    var $randomFileName = FALSE; // se TRUE cria nome randomico para arquivo
    var $maxsize        = '2097152'; // bytes 2MB
    var $max_imgWidth   = '1600';   // parametros para imagens
    var $max_imgHeight  = '900';    // parametro para imagens
    var $allowedExts    = array(
        "txt","csv","htm","html","xml","css","doc","xls","xlsx",
		"rtf","ppt","pdf","swf","flv","avi","wmv","mov",
		"jpg","jpeg","gif","png");
    var $printErrors    = FALSE;
    var $errors         = array();

    /**
     * Guarda dados da imagem para retornar apos o upload concluido
     *
     * [file_name]    => picture.jpg
     * [file_type]    => image/jpeg
     * [file_folder]    => /path/to/your/upload/
     * [full_folder]    => /path/to/your/upload/jpg.jpg
     * [raw_name]     => picture
     * [orig_name]    => picture.jpg
     * [client_name]  => picture
     * [file_ext]     => .jpg
     * [file_size]    => 22.2
     * [is_image]     => 1
     * [image_width]  => 800
     * [image_height] => 600
     * [image_type]   => jpeg
     * [image_size_str] => width="800" height="200"
     *
     * @var array
     */
    var $data           = array();


    // --------------------------------------------------------------
    public function __construct( $config = array() ){
        if(count($config) > 0)
            $this->init($config);
    }

    // --------------------------------------------------------------
    public function init( $config = array() )
    {
        if(count($config)>0)
        {
            foreach($config as $key => $value)
            {
                if($key == 'allowedExts'){
                    if(is_array($value)):
                       $this->$key = $value;
                    else:
                       $this->$key = array($value);
                    endif;
                }else{
                    $this->$key = $value;
                }
            }
        }
    }

    // --------------------------------------------------------------
    public function __get($name){
        return $this->$name;
    }
    // --------------------------------------------------------------
    public function __set($name,$value){
        $this->$name = $value;
    }
    /// ------------------------------------------------------------------------
    /**
     * retorna dados sobre o upload recem concluido
     */
    public function get_data(){
        return $this->data;
    }

    private function isImage($img){
      return (bool)getimagesize($img);
    }

    // -------------------------------------------------------------------------
    protected function set_data($file)
    {
        // se nao e`array nnada a fazer
        if(!is_array($file)) return;
        $ext = '.' . @end(explode('.',strtolower($file['name'])));
        // setando dados para retorno ao final do upload
        $this->data['file_name'] = $this->filename.$ext;
        $this->data['file_type'] = $file['type'];
        $this->data['file_folder'] = $this->destination;
        $this->data['full_folder'] = $this->destination.$this->filename.$ext;
        $this->data['raw_name']  = $this->filename; #@reset(explode('.',strtolower($file['name'])));
        $this->data['orig_name'] = $file['name'];
        $this->data['client_name'] = $this->filename;
        $this->data['file_ext']  = $ext;
        $this->data['file_size'] = $file['size'];
        $this->data['is_image']  = null;
        $this->data['image_width'] = null;
        $this->data['image_height'] = null;
        $this->data['image_type'] = null;
        $this->data['image_size_str'] = null;
        if( $this->isImage( $file["tmp_name"] ) ):
            $this->data['is_image'] = 1;
            $tamanhos = getimagesize( $file["tmp_name"] );
            $this->data['image_width'] = $tamanhos[0];
            $this->data['image_height'] = $tamanhos[1];
            $this->data['image_type'] = @end(explode('/',$file['type']));
            $this->data['image_size_str'] = "width='{$tamanhos[0]}' height='{$tamanhos[1]}'";
        endif;
    }

    // --------------------------------------------------------------
    /**
     * realiza upload do arquivo
     * @param file|array $file - vindo normalmente do form
     * @return array[destination,filename,ext,fullpath] || false em caso de erro
     */
    public function do_upload($file)
    {
        if($this->is_validfile($file))
        {
            // registrando nome original do arquivo
            if(empty($this->filename))
                $this->filename = @reset( explode('.', $file['name']) );

            // obtendo a extensao do arquivo
            $ext = '.' . @end(explode('.',strtolower($file['name'])));

            // se setado para usar um nome randomico, entao ..
            if($this->randomFileName){
                $this->filename = vita()->utils->randomKey(32);
            }
            else{
                $this->filename = vita()->utils->amigavel( $this->filename );
            }

            // verifica se arquivo ja existe
            if(file_exists($this->destination.$this->filename.$ext))
            {
                // verifica se deve sobrescrever o arquivo
                if($this->overrideFile){
                    // tenta excluir o arquivo atual
                    unlink($this->destination.$this->filename.$ext);
                }
                else{
                    // gera um prefixo randomico para evtar problemas com o nome
                    $tmp = $this->filename;
                    $this->filename = vita()->utils->randomKey(8) .'-'.$tmp;
                }
            }
            // setando possiveis dados para retorno
            $this->set_data($file);

            if(!file_exists($file['tmp_name'])){
                throw new SysException( "Arquivo não existe" );
            }

            // tentando mover arquivo para destino ...
            if(!move_uploaded_file($file['tmp_name'],$this->destination.$this->filename.$ext))
                throw new SysException(
                    'Problemas com as permissões para o diretório "'.$this->destination . 
                    '" foram encontrados. Impossivel gravar o arquivo "'.$file['tmp_name'].'"' );
        }
        else
        {
            foreach($this->errors as $error)
                $__erros__ .= "Erro - " . $error . "\n";

            throw new SysException($__erros__, 1);
            return false;
        }
        $this->display_errors();
        return (count($this->errors) > 0) ? FALSE : array('destination' => $this->destination, 'filename' => $this->filename, 'ext' => $ext, 'fullpath' => $this->destination.$this->filename.$ext );
    }

    // --------------------------------------------------------------
    public function delete($filename){
        if(file_exists($filename)){
            if(!unlink($filename))
             throw new SysException('Problemas ao deletar arquivo.', 0);
        }
        else{
            $this->errors[] = 'Arquivo não encontrado. Impossivel deletar : ' . $filename;
        }
        return (count($this->errors) > 0) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------
    protected function is_validfile($file)
    {
        // verificando erro durante upload
        if( @!$file['file']['error'] === UPLOAD_ERR_OK )
            $this->errors[] = $this->file_upload_error( $file['file']['error'] );

        // verificando se uma pasta destino esta setada
        if(!isset($this->destination)||empty($this->destination))
            $this->errors[] = "Caminho para registro do arquivo não foi setado.";

        // verficando se destino da imagem tem permissao de escrita
        if( !$this->es_writable( $this->destination ) ){
            $this->errors[] = "Não é possivel escrever na pasta " .
            $this->destination.";";
        }
        // verifica se arquivo exist
        if(empty($file['name']))
            $this->errors[] = 'Arquivo não encontrado.';

        // checando por extensoes permitidas
        if ( $file['tmp_name'] > '' ){
            if ( !in_array( @end( explode( ".", strtolower( $file['name'] ) ) ), $this->allowedExts ) ) {
                $this->errors[] = "A extensao para este arquivo nao foi permitida.";
            }
        }else{
            $this->sn_error[] = "Arquivo parece ser invalido.";
        }

        // checando tamanho do arquivo se nao excede o permitido
        if($file['size'][0] > $this->maxsize)
            $this->errors[] = 'Tamanho maximo do arquivo excedido. Limite : ' . $this->maxsize . ' bytes.';

        // caso arquivo seja uma imagem obtem e compara largura e altura
        #$ext = end(explode('.',  strtolower($file['name'])));
        if( strrpos( $file['type'], "image" ) )
        {
            $tamanhos = getimagesize( $file["tmp_name"] );
            if( $tamanhos[0] > $this->max_imgWidth ){
                $this->errors[] = "Largura da imagem não deve ultrapassar " . $this->sn_conf["sn_width"] . " pixels";
            }
            if( $tamanhos[1] > $this->max_imgHeight ){
                $this->errors[] = "Altura da imagem não deve ultrapassar " . $this->sn_conf["sn_heigth"] . " pixels";
            }
        }

        return (count($this->errors) > 0) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------
    /**
     * lista erros encontrados durante upload
     * @return void
     **/
    public function display_errors()
    {
        // apresenta os erros apenas se, os mesmos foram encontrados e
        // se o usuario setou a flag printErrors como true
        if( count( $this->errors ) > 0 && $this->printErrors == TRUE )
        {
            echo "Erro(s) durante upload : <br />";
            foreach($this->errors as $error)
            {
                echo "Erro - " . $error . "<br />";
            }
        }
    }

    // --------------------------------------------------------------
    protected function getExtension($file)
    {
        $filepath = $file['name'][0];
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        return $ext;
    }

    // função original retirada de http://php.net/manual/en/function.is-writable.php
    // autor : Nils Kuebler
    // modificado em 05/04/2010 por Sans'
    private function es_writable( $dir )
    {
       if( !$folder = @opendir( $dir ) ) return false;
       while( $file = readdir( $folder ) )
        if( $file != '.' && $file != '..' && ( !is_writable( $dir."/".$file ) || (is_dir($dir."/".$file) && !es_writable($dir."/".$file))))
        {
            closedir( $dir );
            return false;
        }
        @closedir( $dir );
        return true;
    }

    // ---------------------------------------------------------------------
    private function file_upload_error( $error_cod )
    {
        switch ( $error_cod )
        {
            case UPLOAD_ERR_INI_SIZE:
                return 'O arquivo enviado excede a diretiva upload_max_filesize do arquivo php.ini;';
            case UPLOAD_ERR_FORM_SIZE:
                return 'O arquivo excede a diretiva MAX_FILE_SIZE que foi especificada no formulario HTML;';
            case UPLOAD_ERR_PARTIAL:
                return 'Arquivo parcialmente enviado;';
            case UPLOAD_ERR_NO_FILE:
                return 'Nenhum arquivo enviado;';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Pasta temporaria desconhecida;';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Falha ao escrever arquivo no disco;';
            case UPLOAD_ERR_EXTENSION:
                return 'Erro na extensão do arquivo;';
            default:
                return 'Erro de upload desconhecido;';
        }
    }


}