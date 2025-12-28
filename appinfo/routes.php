<?php
return [
    'routes' => [
        // Public (Dashboard) Routes
        ['name' => 'link#index', 'url' => '/api/v1/links', 'verb' => 'GET'],
        ['name' => 'link#getIcon', 'url' => '/api/v1/links/{id}/icon', 'verb' => 'GET'],

        // Admin Link Routes
        ['name' => 'link#adminIndex', 'url' => '/api/v1/admin/links', 'verb' => 'GET'],
        ['name' => 'link#create', 'url' => '/api/v1/admin/links', 'verb' => 'POST'],
        ['name' => 'link#exportLinks', 'url' => '/api/v1/admin/links/export', 'verb' => 'GET'],
        ['name' => 'link#importLinks', 'url' => '/api/v1/admin/links/import', 'verb' => 'POST'],
        ['name' => 'link#updateOrder', 'url' => '/api/v1/admin/links/order', 'verb' => 'PUT'],
        ['name' => 'link#update', 'url' => '/api/v1/admin/links/{id}', 'verb' => 'PUT'],
        ['name' => 'link#delete', 'url' => '/api/v1/admin/links/{id}', 'verb' => 'DELETE'],
        ['name' => 'link#uploadIcon', 'url' => '/api/v1/admin/links/{id}/icon', 'verb' => 'POST'],
        ['name' => 'link#deleteIcon', 'url' => '/api/v1/admin/links/{id}/icon', 'verb' => 'DELETE'],
        ['name' => 'link#getGroups', 'url' => '/api/v1/admin/groups', 'verb' => 'GET'],

        // Settings Routes
        ['name' => 'settings#index', 'url' => '/api/v1/admin/settings', 'verb' => 'GET'],
        ['name' => 'settings#update', 'url' => '/api/v1/admin/settings', 'verb' => 'PUT'],

        // Admin Page
        ['name' => 'admin#index', 'url' => '/admin', 'verb' => 'GET'],
    ]
];
