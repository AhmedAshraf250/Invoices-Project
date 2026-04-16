<?php

return [
    'page' => [
        'title' => 'الفواتير',
        'breadcrumb' => 'قائمة الفواتير',
        'card_title' => 'جدول الفواتير',
        'card_description' => 'متابعة ومراجعة جميع الفواتير من مكان واحد.',
    ],

    'table' => [
        'id' => '#',
        'invoice_number' => 'رقم الفاتورة',
        'invoice_date' => 'تاريخ الفاتورة',
        'due_date' => 'تاريخ الاستحقاق',
        'product' => 'المنتج',
        'organization' => 'الجهة',
        'discount' => 'الخصم',
        'rate_vat' => 'نسبة الضريبة',
        'value_vat' => 'قيمة الضريبة',
        'total' => 'الإجمالي',
        'status' => 'الحالة',
        'note' => 'ملاحظات',
        'actions' => 'العمليات',
    ],

    'status' => [
        'paid' => 'مدفوعة',
        'partial' => 'مدفوعة جزئياً',
        'unpaid' => 'غير مدفوعة',
        'unknown' => 'غير معروفة',
    ],

    'messages' => [
        'empty' => 'لا توجد فواتير حتى الآن.',
    ],
];
