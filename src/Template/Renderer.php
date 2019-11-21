<?php

namespace Vita\Template;

interface Renderer
{
    public function render($template, $data = []);
}