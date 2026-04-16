<?php

return [
    'page' => [
        'title' => 'Organizations',
        'settings' => 'Settings',
    ],

    'table' => [
        'id' => '#',
        'name' => 'Organization Name',
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
    ],

    'messages' => [
        'delete_confirmation' => 'Are you sure you want to delete this organization?',
        'created' => 'Organization created successfully.',
        'updated' => 'Organization updated successfully.',
        'deleted' => 'Organization deleted successfully.',
        'cannot_delete_with_dependencies' => 'This organization cannot be deleted because it has related products or invoices.',
    ],
];
