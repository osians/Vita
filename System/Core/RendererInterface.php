<?php

namespace Vita\Core;

interface RendererInterface
{
    public function render($template, $data = []);
}
