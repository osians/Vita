<?php

header('Content-Type: text/html; charset=utf-8');

/*************************************************************************
 * Classe usada com unica finalidade de determinar
 * tempo de carregamento desta pagina
 */
class Benchmark
{
	private static $start_time ;
	private static $initialized = false;
	public static function init()
	{
        if(self::$initialized) return;
		self::$start_time = microtime(true);
        self::$initialized = true;
	}
	public static function elapsed_time()
	{
		$end_time = microtime(true);
		return $end_time - self::$start_time;
	}
}

Benchmark::init();


/*************************************************************************
 * Cria um contexto utilizado pela classe de Manual
 */
class Context
{
	protected $root = null;
	protected $iter;
	protected $folders;
	protected $tree = array();

	public function __construct( $__document_root__ = null )
	{
		if(!is_null($__document_root__))
			$this->setroot($__document_root__);

		# criando recursividade...
		$this->iter = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$this->root, RecursiveDirectoryIterator::SKIP_DOTS
			),
			RecursiveIteratorIterator::SELF_FIRST,
			RecursiveIteratorIterator::CATCH_GET_CHILD /* Ignorar "Permission denied" */
		);
	}

	public function setroot($__document_root__ = null )
	{
		# verifica se uma pasta foi passada, na auxencia usa a atual
		if(!is_null($__document_root__))
		{
			$this->root = $__document_root__;
		}
		else
		{
			$this->root = getcwd();
		}
	}

	# funcao inicial...
	# utilizada para a finalidade deste sistema de docs ...
	public function init( $__document_root__ = null )
	{
		if(!is_null($__document_root__))
			$this->setroot($__document_root__);

		$this->parse_folders();
		$this->parse_files();
	}

	public function parse_folders()
	{
		$this->folders = array($this->root);

		# obtendo todas as pastas ...
		foreach($this->iter as $path => $dir)
			if($dir->isDir())
				$this->folders[] = $path;

		return $this->folders;
	}

	public function parse_files()
	{
		if(empty($this->folders))
			$this->parse_folders();

		# obtendo todos os arquivos .vmn
		$this->tree = array();

		# eliminando diretorios que nao contenham arquivos vmn
		foreach ($this->folders as $path):
			$files = glob( $path."/*.vmn" );
			if(!empty($files))
				$this->tree[] = array(
					'directory' => $path,
					'files' => $files
				);
		endforeach;

		return $this->tree;
	}

	public function getFiles()
	{
		if(empty($this->tree))
			$this->parse_files();
		return $this->tree;
	}

	public function getFolders(){return $this->folders;}
} // <!--// class Context


/*************************************************************************
 * Classe Responsavel por gerar um Manual do Sistema
 *
 */
class Manual
{
	private static $tag_prefix = 'manual';
	private static $quebra_linha = '<br>';
	# @var string - conteudo em formato texto a ser analisado afim de encontrar documentacao
	private static $content;
	# @var bool - por ser classe statica, informa se a mesma ja foi iniciada ou nao
	private static $iniciado = false ;
	# @var array - guarada a estrutura atual analizada ou a ultima estrutura analisada
	private static $estrutura = array();
	#@var array - guarda todas as estruturas encontradas nos arquivos analizados
	private static $wrapper = array();
	#@var array - guarda todas as categorias encontradas nos arquivos analizados
	private static $categories = array();
	# @var string - arquivo sendo analisado no momento
	private static $curr_file = null;
	# @var string - se setado compila apenas manual de uma data categoria
	private static $compile_only;
	private static $curr_label ;
	private static $context;

	/**
	 * elementos reconhecidos como labels a serem
	 * tratados com formatacao diferente
	 *
	 * @var array
	 */
	private static $labels = array
	(
		'title',
		'filename',
		'localization',
		'category',
		'description',
		'usage',
	);

	/**
	 * Seta um novo conteudo a ser analisado
	 *
	 * @param  [type] $__conteudo__ [description]
	 * @return [type]               [description]
	 */
	protected static function init( $__conteudo__ = null )
	{
		if(!null == $__conteudo__)
			self::$content = $__conteudo__;

		if(self::$iniciado) return;
		self::$iniciado = true;
	}

	/**
	 * Converte contexto recebido para uma pagina html
	 * contendo 2 colunas, sendo elas leftbar como categorias,
	 * main content como Descricao do Documento
	 *
	 * @param  [type] $context      [description]
	 * @param  [type] $__category__ [description]
	 * @return [type]               [description]
	 */
	public static function parse( $context = null, $__category__ = null )
	{
		if(is_null($context))
			self::$context = new Context();
		else
			self::$context = $context;
		if($__category__ != null)
			self::$compile_only = trim($__category__);

		# lendo arquivos php ...
		foreach(self::$context->getFiles() as $r)
		{
			foreach($r['files'] as $file)
			{
				self::$curr_file = $file;
				$__content__ = file_get_contents($file);
				self::init($__content__);
				$wrapper = self::gerar_estrutura(self::$content);
				if(!empty($wrapper))
					self::$wrapper[] = $wrapper;
			}
		}

		# gera o html final
		return self::compile();
	}

	protected static function gerar_estrutura($content)
	{
		// self::$categories = null ;
		self::$estrutura = null ;
		// self::$categories = array();
		self::$estrutura = array();

		preg_match("'<".self::$tag_prefix.">(.*?)</".self::$tag_prefix.">'si", $content, $matches);

		if(empty($matches))
			return self::$estrutura;

		list(, $pre_match) = $matches;

		// verificando o que foi encontrado...
		if(strlen($pre_match)>0):

			# usando quebra de linha como divisor para criacao de array
			$data = explode("\n", $pre_match);

			foreach ($data as $value)
			{
				$value = trim($value);

				# procurando label reconhecido com documentacao...
				$__pos__ = strpos( $value, ":" );

				# primeira palavra encontrada, e' um label?
				$label = strtolower(trim(substr($value,0,$__pos__))) ;

				# verificando se encontramos um label...
				$__curr_line_has_label__ = in_array( $label, self::$labels ) ;

				# se temos um label encontrado na primeira palavra e linha...
				if($__curr_line_has_label__ === true)
				{
					self::$curr_label = $label;

					#obtendo conteudo da linha atual, excluindo o label e o delimitador...
					$text  = ltrim(ltrim(substr($value,($__pos__), strlen($value)),":")," ");

					# temos realmente um texto para ser associado ao label?
					if(!empty($text))
							self::$estrutura[self::$curr_label] = $text ;

					# label atual e' uma categoria?
					if(self::$curr_label == 'category'):

						if(!isset(self::$estrutura['categories']))
							self::$estrutura['categories'] = array();

						$_split_ = explode(",",$text);

						if(count($_split_)>1 )
						{
							foreach ($_split_ as $categoria):
								$categoria = trim($categoria);

								# se nao esta nas categorias globais, seta
								if(!in_array($categoria,self::$categories))
									self::$categories[] = $categoria;

								# se nao esta na categoria da estrutura... seta
								if(!in_array($categoria,self::$estrutura['categories']))
									self::$estrutura['categories'][] = $categoria;
							endforeach;
						}
						else
						{
							# seta globalmente ....
							if(!in_array($text,self::$categories))
								self::$categories[] = $text;

							# seta localmente ...
							if(!in_array($text,self::$estrutura['categories']))
								self::$estrutura['categories'][] = $text;
						}
					endif;

				}
				else # nao temos um label na linha atual...
				{
					$text  = $value;
					if(!empty($text) || (in_array(self::$curr_label,array('usage','description'))) ):
						if(!isset(self::$estrutura[self::$curr_label]))
							self::$estrutura[self::$curr_label] = "";
						else
							self::$estrutura[self::$curr_label] .= self::$quebra_linha;
						self::$estrutura[self::$curr_label] .= $text ;
					endif;
				}

			} // foreach

			# verificando se podemos incluir mais algumas informacoes
			$curr_file = str_replace('\\','/',self::$curr_file);

			if(!isset(self::$estrutura['localization']))
				self::$estrutura['localization'] = $curr_file;

			if(!isset(self::$estrutura['categories']) || empty(self::$estrutura['categories']))
				self::$estrutura['categories'] = array('Todos','Outros');

			if(!isset(self::$estrutura['filename']))
				self::$estrutura['filename'] = array_pop((array_slice(explode('/',$curr_file), -1)));

			if(!isset(self::$estrutura['filemtime']))
				self::$estrutura['filemtime'] = filemtime(self::$curr_file);

			if(!isset(self::$estrutura['autor']))
				self::$estrutura['autor'] = "Wanderlei Santana - <a class='_link' href='mailto:sans.pds@gmail.com?Subject=Contato%20Manual%20Classe' target=\"_top\">sans.pds@gmail.com</a>";

		endif;

		return self::$estrutura;
	}

	/**
	 * Centraliza a criacao de links HTML para as categorias 
	 * @param  string $__category__ - Categoria a ser criado um link HTML
	 * @return string               URL HTML 
	 */
	private static function linkCategory($__category__ = null)
	{
		return (!null == $__category__) ? "<a href='".$_SERVER['PHP_SELF']."?category=$__category__'>$__category__</a>" : '';
	}

	protected static function compile()
	{
		# @var string - guarda conteudo da pagina final que sera retornado...
		$__html_output__ = "";

		# verifica se encontrou alguma coisa...
		if(empty(self::$wrapper)) return '';

		# tratando primeio a secao de categorias, lateral direita do SITE, vide CSS
		$__html_output__ .= "<div class='categories'><span class='logotipo'><img src='logo.png' /></span><h2>Categorias</h2><ul>";
		$__html_output__ .= "<li><a href='".$_SERVER['PHP_SELF']."'>Todas</a> </li>" ;
		foreach (self::$categories as $category)
		{
			$__html_output__ .= "<li>".self::linkCategory($category)."</li>" ;
		}
		$__html_output__ .= "</ul></div>";


		# tratando agora corpo do site.
		$__html_output__ .= "<div class='content'>";

		foreach( self::$wrapper as $estrutura ):

			$r = json_decode(json_encode($estrutura,FALSE));

			$compile_this_category = (self::$compile_only == null) ? true :
			(in_array(self::$compile_only,$r->categories) ? true : false);

			if($compile_this_category):
				$__html_output__.= "<div class='wrap'>";
				$__html_output__.= "<h1>".(isset($r->title)?$r->title:'Título indefinido')."</h1>";
				$__html_output__.= "<span class='data_e_time'>".date('d/m/Y H:i:s')."</span>";
				$__html_output__.= "<hr>";

				$__html_output__.= "<ul>";
					$__html_output__.= (isset($r->filename))     ? "<li><b>Arquivo :</b> $r->filename</li>" : "";
					$__html_output__.= (isset($r->localization)) ? "<li><b>Localização :</b> $r->localization</li>" : "";

					foreach ($r->categories as $v) $_categories_[] = self::linkCategory($v);

					$__html_output__.= (isset($r->categories))   ? "<li><b>Categoria :</b> ".implode($_categories_,', ')." </li>" : "";
					$__html_output__.= (isset($r->filemtime)) ? "<li><b>Última Modificação :</b> ".date("d/m/Y H:i:s", $r->filemtime)." </li>" : "";
					$__html_output__.= (isset($r->autor))    ? "<li><b>Autor :</b> ".$r->autor." </li>" : "";
				$__html_output__.= "</ul>";

				$__html_output__.= "<h3>Descrição</h3>";
				$__html_output__.= "<hr>";
				$__html_output__.= "<div class='description'>".(isset($r->description)?$r->description:'Descrição não fornecida.')."</div>";

				$__html_output__.= "<h3 style='margin-bottom:15px;'>Exemplo de Uso</h3>";
				$body = implode( "\n", explode('<br>',$r->usage) );
				$__html_output__.= "<div class='usage'><pre class=\"brush: php\">".$body."</pre></div>";

				$__html_output__.= "</div><!--// wrap -->";
				$__html_output__.= "<div class='clear'></div>";
			endif;

		endforeach;

		$__html_output__ .= "</div><!--//.content -->";
		$__html_output__.= "<div class='clear'></div>";
		return $__html_output__;
	}
}



# ----------------------------------------------------------------------------
#
# diretorio fonte a ser escaneado...

$_parent = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );
array_pop($_parent);
$__root__ = implode( DIRECTORY_SEPARATOR, $_parent );

$__category__ = isset($_REQUEST['category']) ? $_REQUEST['category'] : null;

$context = new Context( $__root__ );
$context->init();

# <!-- ------------------------------HTML-------------------------------------
echo "<html>";
	echo "<head>";
		echo "<title> Manual do Sistema </title>";
		echo '<script type="text/javascript" src="syntaxe/scripts/shCore.js"></script>';
		echo '<script type="text/javascript" src="syntaxe/scripts/shBrushPhp.js"></script>';
		echo '<link href="syntaxe/styles/shCore.css" rel="stylesheet" type="text/css" />';
		echo '<link href="syntaxe/styles/shThemeDefault.css" rel="stylesheet" type="text/css" />';

		echo '<link rel="stylesheet" type="text/css" href="manual.style.css">' ;
		echo '<script type="text/javascript">
		     SyntaxHighlighter.all() ;
		</script>';

	echo "</head>";
	echo "</body>";

		# iniciando sistema que gera manual com contexto recem criado...
		echo Manual::parse( $context , $__category__ );

		echo 'Compilado em ' . Benchmark::elapsed_time() . ' segundos';

	echo "</body>";
echo "</html>";
# <!-- //----------------------------HTML-------------------------------------

