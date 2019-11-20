<?php

namespace Vita\Controllers;

use Http\Request;
use Http\Response;

class Homepage
{
    private $_request;
    private $_response;
    
    public function __construct(Request $request, Response $response) {
        $this->_request = $request;
        $this->_response = $response;
    }
    
    public function show()
    {
        $content = '<h1>Hello World</h1>';
        $content .= 'Hello ' . $this->_request->getParameter('name', 'stranger');
        $this->_response->setContent($content);
    }
}
