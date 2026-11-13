<?php

declare(strict_types=1);

return [
    'slug' => 'deal-activity',
    'name' => 'Activity Log',
    'version' => '1.0.0',
    'description' => 'Records clients, contacts, users and case events on the dashboard.',
    'author' => 'CRM PHP',
    'class' => \DealActivity\DealActivityPlugin::class,
    'class_file' => 'DealActivityPlugin.php',
    'default_enabled' => true,
];
