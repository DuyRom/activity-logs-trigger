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

    'retain_days' => 365, // Number of days to retain activity logs
];
