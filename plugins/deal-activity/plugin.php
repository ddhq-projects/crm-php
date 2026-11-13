<?php

declare(strict_types=1);

return [
    'slug' => 'deal-activity',
    'name' => 'Журнал активности',
    'version' => '1.0.0',
    'description' => 'Записывает создание клиентов и сделок, смену этапов и показывает события на панели.',
    'author' => 'LiteCRM',
    'class' => \DealActivity\DealActivityPlugin::class,
    'class_file' => 'DealActivityPlugin.php',
    'default_enabled' => true,
];
