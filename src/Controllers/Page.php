<?php

namespace Vita\Controllers;

use Http\Response;
use Vita\Template\FrontendRenderer;
use Vita\Page\PageReader;
use Vita\Page\InvalidPageException;

class Page
{
    private $response;
    private $frontendRenderer;
    private $pageReader;

    public function __construct(
        Response $response,
        FrontendRenderer $frontendRenderer,
        PageReader $pageReader
    ) {
        $this->response = $response;
        $this->frontendRenderer = $frontendRenderer;
        $this->pageReader  = $pageReader;
    }

    public function show($params)
    {
        $slug = $params['slug'];

        try {
            $data['content'] = $this->pageReader->readBySlug($slug);
        } catch (InvalidPageException $e) {
            $this->response->setStatusCode(404);
            return $this->response->setContent('404 - Page not found');
        }

        $html = $this->frontendRenderer->render('Page', $data);
        $this->response->setContent($html);
    }
}