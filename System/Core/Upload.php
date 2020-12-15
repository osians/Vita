<?php 

namespace System\Core;

/**
 * Classe responsavel pelo upload de arquivos
 * @todo - colocar uma funcao para tornar o nome da imagem amigavel
 **/
class Upload
{
    protected $_fileDestinationFolder = '';
    var $filename       = '';
    var $overrideFile   = FALSE;
    var $randomFileName = FALSE; // se TRUE cria nome randomico para arquivo
    protected $_maxsize = '2097152'; // bytes 2MB
    var $max_imgWidth   = '1600';   // parametros para imagens
    var $max_imgHeight  = '900';    // parametro para imagens
    var $allowedExts    = array(
        "txt","csv","htm","html","xml","css","doc","xls","xlsx",
		"rtf","ppt","pdf","swf","flv","avi","wmv","mov",
		"jpg","jpeg","gif","png");
    var $printErrors    = FALSE;
    
    /**
     * Error List on Upload
     *
     * @var type 
     */
    protected $_error = array();

    /**
     * Guarda dados da imagem para retornar apos o upload concluido
     *
     * [file_name]    => picture.jpg
     * [file_type]    => image/jpeg
     * [file_folder]  => /path/to/your/upload/
     * [full_folder]  => /path/to/your/upload/jpg.jpg
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
    protected $_imageInfo = array();

    /**
     * Construct
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        if (count($config) > 0) {
            $this->init($config);
        }
    }

    /**
     * Initialize Instance
     *
     * @param array $config
     *
     * @return $this
     */
    public function init($config = array())
    {
        if (count($config) == 0) {
            return $this;
        }
        
        foreach ($config as $key => $value) {
            if ($key == 'allowedExts') {
                $this->$key = (is_array($value)) ? $value : array($value);
                continue;
            }
            $this->$key = $value;
        }
        
        return $this;
    }
    
    /**
     * retorna dados sobre o upload recem concluido
     * 
     * @return array
     */
    public function getImageInfo()
    {
        return $this->_imageInfo;
    }

    /**
     * Check if File is imagem
     *
     * @param string $filename
     *
     * @return bool
     */
    private function isImage($filename)
    {
        return (bool)getimagesize($filename);
    }

    protected function setData($file)
    {
        if (!is_array($file)) {
            return;
        }

        $ext = '.' . @end(explode('.',strtolower($file['name'])));

        $this->_imageInfo['file_name'] = $this->filename.$ext;
        $this->_imageInfo['file_type'] = $file['type'];
        $this->_imageInfo['file_folder'] = $this->_fileDestinationFolder;
        $this->_imageInfo['full_folder'] = $this->_fileDestinationFolder.$this->filename.$ext;
        $this->_imageInfo['raw_name']  = $this->filename; #@reset(explode('.',strtolower($file['name'])));
        $this->_imageInfo['orig_name'] = $file['name'];
        $this->_imageInfo['client_name'] = $this->filename;
        $this->_imageInfo['file_ext']  = $ext;
        $this->_imageInfo['file_size'] = $file['size'];
        $this->_imageInfo['is_image']  = null;
        $this->_imageInfo['image_width'] = null;
        $this->_imageInfo['image_height'] = null;
        $this->_imageInfo['image_type'] = null;
        $this->_imageInfo['image_size_str'] = null;
        
        if ($this->isImage($file["tmp_name"])) {
            $this->_imageInfo['is_image'] = 1;
            $tamanhos = getimagesize( $file["tmp_name"] );
            $this->_imageInfo['image_width'] = $tamanhos[0];
            $this->_imageInfo['image_height'] = $tamanhos[1];
            $this->_imageInfo['image_type'] = @end(explode('/',$file['type']));
            $this->_imageInfo['image_size_str'] = "width='{$tamanhos[0]}' height='{$tamanhos[1]}'";
        }
    }

    /**
     * realiza upload do arquivo
     *
     * @param file|array $file - vindo normalmente do form
     *
     * @return array[destination,filename,ext,fullpath] || false em caso de erro
     */
    public function doUpload($file)
    {
        if ($this->isValidFile($file)) {
            
            if (empty($this->filename)) {
                $this->filename = @reset(explode('.', $file['name']));
            }

            $ext = '.' . @end(explode('.', strtolower($file['name'])));
            
            if ($this->randomFileName){
                $this->filename = vita()->utils->randomKey(32);
            } else {
                $this->filename = vita()->utils->amigavel($this->filename);
            }

            // verifica se arquivo ja existe
            if(file_exists($this->_fileDestinationFolder.$this->filename.$ext))
            {
                // verifica se deve sobrescrever o arquivo
                if($this->overrideFile){
                    // tenta excluir o arquivo atual
                    unlink($this->_fileDestinationFolder.$this->filename.$ext);
                }
                else{
                    // gera um prefixo randomico para evtar problemas com o nome
                    $tmp = $this->filename;
                    $this->filename = vita()->utils->randomKey(8) .'-'.$tmp;
                }
            }
            // setando possiveis dados para retorno
            $this->setData($file);

            if(!file_exists($file['tmp_name'])){
                throw new SYS_Exception( "Arquivo não existe" );
            }

            // tentando mover arquivo para destino ...
            if(!move_uploaded_file($file['tmp_name'],$this->_fileDestinationFolder.$this->filename.$ext))
                throw new SYS_Exception(
                    'Problemas com as permissões para o diretório "'.$this->_fileDestinationFolder . 
                    '" foram encontrados. Impossivel gravar o arquivo "'.$file['tmp_name'].'"' );
        }

        else {
            foreach ($this->_error as $error) {
                $erros .= "Erro - " . $error . "\n";
            }

            throw new SYS_Exception($erros, 1);
            return false;
        }
        
        $this->getError();
        return (count($this->_error) > 0) ? FALSE : array('destination' => $this->_fileDestinationFolder, 'filename' => $this->filename, 'ext' => $ext, 'fullpath' => $this->_fileDestinationFolder.$this->filename.$ext );
    }

    /**
     * Remove File
     *
     * @param string $filename
     *
     * @return bool
     */
    public function delete($filename)
    {
        if (file_exists($filename) && (!unlink($filename))) {
            $this->setError('Problemas ao deletar arquivo.');
            return false;
        }

        return true;
    }

    /**
     * Check if File is Valid and has no errors
     *
     * @param String $file
     *
     * @return bool
     */
    protected function isValidFile($file)
    {
        // verificando erro durante upload
        if (@!$file['file']['error'] === UPLOAD_ERR_OK) {
            $this->setError($this->_getFileUploadError($file['file']['error']));
        }

        // verificando se uma pasta destino esta setada
        if (!isset($this->_fileDestinationFolder) || empty($this->_fileDestinationFolder)) {
            $this->setError("Caminho para registro do arquivo não foi setado.");
        }

        // verficando se destino da imagem tem permissao de escrita
        if (!$this->isWritable($this->_fileDestinationFolder)) {
            $this->setError("Não é possivel escrever na pasta '{$this->_fileDestinationFolder}'");
        }

        // verifica se arquivo exist
        if (empty($file['name'])) {
            $this->setError('Arquivo não encontrado');
        }

        // arquivo foi carregado?
        if ($file['tmp_name']  == '') {
            $this->seterror("Arquivo parece ser invalido.");
        }
        
        // checando por extensoes permitidas
        if ($file['tmp_name'] > '' ) {
            if (!in_array(end(explode(".", strtolower($file['name']))), $this->allowedExts)) {
                $this->setError("A extensao para este arquivo nao é permitida.");
            }
        }

        // checando tamanho do arquivo se nao excede o permitido
        if ($file['size'][0] > $this->_maxsize) {
            $this->setError("Tamanho maximo do arquivo excedido. Limite: {$this->_maxsize} bytes.");
        }

        // caso arquivo seja uma imagem obtem e compara largura e altura
        if (strrpos( $file['type'], "image")) {
            $tamanhos = getimagesize($file["tmp_name"]);
            if ($tamanhos[0] > $this->max_imgWidth) {
                $this->setError("Largura da imagem não deve ultrapassar {$this->max_imgWidth} pixels");
            }
            
            if ($tamanhos[1] > $this->max_imgHeight) {
                $this->setError("Altura da imagem não deve ultrapassar {$this->max_imgHeight} pixels");
            }
        }

        return count($this->getError()) == 0;
    }

    /**
     * Add Error to Array List
     *
     * @param string $error
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->_error[] = $error;
        return $this;
    }
    
    /**
     * Retorna lista de erros encontrados durante upload
     *
     * @return Array
     **/
    public function getError()
    {
        return $this->_error;
    }

    /**
     * returns file extension
     *
     * @param string $file
     *
     * @return string
     */
    protected function _getFileExtension($file)
    {
        $filepath = $file['name'][0];
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        return $ext;
    }

    /**
     * função original retirada de http://php.net/manual/en/function.is-writable.php
     * autor : Nils Kuebler
     * modificado em 05/04/2010 por Sans'
     * 
     * @param string $dir
     * 
     * @return boolean
     */
    private function isWritable($dir)
    {
        if (!$folder = @opendir($dir)) {
            return false;
        }

        while ($file = readdir($folder)) {
            if ($file != '.' && $file != '..' && (!is_writable($dir."/".$file) || (is_dir($dir."/".$file) && !$isWritable($dir."/".$file)))) {
                closedir($dir);
                return false;
            }
        }
        
        @closedir($dir);
        return true;
    }

    /**
     * Return Error Text from Error Upload Code
     *
     * @param integer $errorCode
     *
     * @return string
     */
    private function _getFileUploadError($errorCode)
    {
        switch ($errorCode)
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
    
    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
