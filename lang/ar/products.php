<?php

return [
    'page' => [
        'title' => 'المنتجات',
        'settings' => 'الإعدادات',
    ],

    'table' => [
        'id' => '#',
        'name' => 'اسم المنتج',
        'organization' => 'الجهة',
        'commission_rate' => 'عمولة المنتج',
        'description' => 'الوصف',
        'actions' => 'العمليات',
    ],

    'actions' => [
        'add' => 'إضافة منتج',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'confirm' => 'تأكيد',
        'close' => 'إغلاق',
        'cancel' => 'إلغاء',
    ],

    'form' => [
        'name' => 'اسم المنتج',
        'organization' => 'الجهة',
        'description' => 'ملاحظات',
        'commission_rate' => 'نسبة عمولة المنتج (%)',
        'inherit_from_organization' => 'يتبع عمولة المنظمة',
        'select_organization' => 'اختر الجهة',
    ],

    'messages' => [
        'empty' => 'لا توجد منتجات حتى الآن.',
        'delete_confirmation' => 'هل أنت متأكد من عملية حذف المنتج؟',
        'created' => 'تمت إضافة المنتج بنجاح.',
        'updated' => 'تم تحديث المنتج بنجاح.',
        'deleted' => 'تم حذف المنتج بنجاح.',
    ],
];
