<?php

return [
    'page' => [
        'title' => 'Products',
        'settings' => 'Settings',
    ],

    'table' => [
        'id' => '#',
        'name' => 'Product Name',
        'organization' => 'Organization',
        'commission_rate' => 'Product Commission',
        'description' => 'Description',
        'actions' => 'Actions',
    ],

    'actions' => [
        'add' => 'Add Product',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm' => 'Confirm',
        'close' => 'Close',
        'cancel' => 'Cancel',
    ],

    'form' => [
        'name' => 'Product Name',
        'organization' => 'Organization',
        'description' => 'Notes',
        'commission_rate' => 'Product Commission Rate (%)',
        'inherit_from_organization' => 'Use organization default commission',
        'select_organization' => 'Select organization',
    ],

    'messages' => [
        'empty' => 'No products available yet.',
        'delete_confirmation' => 'Are you sure you want to delete this product?',
        'created' => 'Product created successfully.',
        'updated' => 'Product updated successfully.',
        'deleted' => 'Product deleted successfully.',
    ],
];
