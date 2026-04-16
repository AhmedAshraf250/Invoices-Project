<?php

return [
    'page' => [
        'title' => 'Invoices',
        'breadcrumb' => 'Invoices List',
        'card_title' => 'Invoices Table',
        'card_description' => 'Track and review all invoices in one place.',
    ],

    'table' => [
        'id' => '#',
        'invoice_number' => 'Invoice Number',
        'invoice_date' => 'Invoice Date',
        'due_date' => 'Due Date',
        'product' => 'Product',
        'organization' => 'Organization',
        'discount' => 'Discount',
        'rate_vat' => 'Tax Rate',
        'value_vat' => 'Tax Value',
        'total' => 'Total',
        'status' => 'Status',
        'note' => 'Notes',
        'actions' => 'Actions',
    ],

    'status' => [
        'paid' => 'Paid',
        'partial' => 'Partially Paid',
        'unpaid' => 'Unpaid',
        'unknown' => 'Unknown',
    ],

    'messages' => [
        'empty' => 'No invoices available yet.',
    ],
];
