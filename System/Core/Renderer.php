<?php

namespace Vita\Core;

class Renderer implements RendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $_renderer;

    public function __construct(\Twig_Environment $renderer)
	{
		$this->_renderer = $renderer;
	}

    /**
     * @param $template
     * @param array $data
     * @throws \Exception
     */
	public function render($template, $data = [])
	{
        try {

            $viewFolder = \Vita\Vita::getInstance()->getViewFolder();
        	$this->_normalizeFilename($template);
            $view = $viewFolder . $template;

            if (!file_exists($view)) {
                throw new \Exception(
                    "O arquivo '{$template}' nao foi encontrado em '
                    {$viewFolder}'. Proceda com a criacao do mesmo
                     para corrigir o problema."
                );
            }

            $data = array_merge($data, \Vita\Vita::getInstance()->getGlobalVars());
            print $this->_renderer->render($template, $data);
        } 
        catch (Exception $e) {
            throw new \Exception($e->getMessage());
        } catch (\Twig_Error_Loader $e) {
        } catch (\Twig_Error_Runtime $e) {
        } catch (\Twig_Error_Syntax $e) {
        }
        exit(0);
	}

	private function _normalizeFilename(&$template)
	{
        if (!strpos($template, '.twig')) {
            $template .= '.twig';
        }
	}
}
