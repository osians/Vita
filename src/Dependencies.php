<?php

$injector = new \Auryn\Injector;

$injector->alias('Http\Request', 'Http\HttpRequest');
$injector->share('Http\HttpRequest');
$injector->define('Http\HttpRequest', [
    ':get'     => $_GET,
    ':post'    => $_POST,
    ':cookies' => $_COOKIE,
    ':files'   => $_FILES,
    ':server'  => $_SERVER,
]);

$injector->alias('Http\Response', 'Http\HttpResponse');
$injector->share('Http\HttpResponse');

-- --------------------------------------------------------------------------===
    public function init_tpl_system($path)
    {

        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem( $path );

        # verificando se o modo de cache esta liberado no arquivo de config
        $cacheFolder = ($this->config->twig_cache_enable === true)
                     ? $this->config->system_path . 'cache' . DIRECTORY_SEPARATOR
                     : false;

        # instanciando o ambiente twig
        $this->twig = new \Twig_Environment($loader,
            array(
                'cache' => $cacheFolder,
                'debug' => $this->config->twig_debug_enable
            )
        );
    }
-- --------------------------------------------------------------------------===


$injector->delegate('Twig_Environment', function () {
    $loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/templates');
    $twig = new Twig_Environment($loader);
    return $twig;
});

$injector->alias('Vita\Template\Renderer', 'Vita\Template\TwigRenderer');
$injector->define('Vita\Page\FilePageReader', [
    ':pageFolder' => __DIR__ . '/../pages',
]);

$injector->alias('Vita\Template\FrontendRenderer', 'Vita\Template\FrontendTwigRenderer');
$injector->alias('Vita\Page\PageReader', 'Vita\Page\FilePageReader');
$injector->share('Vita\Page\FilePageReader');

$injector->alias('Vita\Menu\MenuReader', 'Vita\Menu\ArrayMenuReader');
$injector->share('Vita\Menu\ArrayMenuReader');

return $injector;
