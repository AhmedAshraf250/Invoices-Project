<?php

return [
    'page' => [
        'title' => 'Organizations',
        'settings' => 'Settings',
    ],

    'table' => [
        'id' => '#',
        'name' => 'Organization Name',
        'commission_rate' => 'Default Commission',
        'description' => 'Description',
        'actions' => 'Actions',
    ],

    'actions' => [
        'add' => 'Add Organization',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm' => 'Confirm',
        'close' => 'Close',
        'cancel' => 'Cancel',
    ],

    'form' => [
        'name' => 'Organization Name',
        'description' => 'Notes',
        'commission_rate' => 'Commission Rate (%)',
    ],

    'messages' => [
        'delete_confirmation' => 'Are you sure you want to delete this organization?',
        'created' => 'Organization created successfully.',
        'updated' => 'Organization updated successfully.',
        'deleted' => 'Organization deleted successfully.',
        'cannot_delete_with_dependencies' => 'This organization cannot be deleted because it has related products or invoices.',
    ],
];
