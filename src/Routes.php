<?php

return [
    ['GET', '/', ['Vita\Controllers\Homepage', 'show']],
    ['GET', '/{slug}', ['Vita\Controllers\Page', 'show']],
];
