<?php

/*
| Лише те, що відрізняється від стандартних налаштувань пакета inertia-laravel
| (решта підтягується з vendor/inertiajs/inertia-laravel/config/inertia.php).
*/

return [

    // Компоненти сторінок лежать у resources/js/Pages (з великої літери) — у Linux регістр важливий
    'pages' => [
        'ensure_pages_exist' => false,
        'paths' => [resource_path('js/Pages')],
        'extensions' => ['vue', 'js'],
    ],

    'testing' => [
        'ensure_pages_exist' => true,
    ],

];
