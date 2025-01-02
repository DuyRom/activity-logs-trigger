<?php

return [
    // List of tables to create triggers for
    'tables' => [
        // users,
        // posts,
    ],

    // Middleware groups to include the SetCurrentUserId middleware
    'middleware_groups' => [
        'web',
        // 'api', // Uncomment this line to include in the API group as well
    ],

     // Define primary keys for tables that have composite primary keys, default is ['id']
     'primary_keys' => [
        'model_has_roles' => ['model_id', 'role_id'],
        // Add other tables if needed
    ],

    'retain_days' => 365, // Number of days to retain activity logs
];
