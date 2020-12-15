<?php

namespace System\Core;

interface RendererInterface
{
    public function render($template, $data = []);
}
