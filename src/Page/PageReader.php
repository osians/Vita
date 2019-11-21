<?php

namespace Vita\Page;

interface PageReader
{
    public function readBySlug($slug);
}