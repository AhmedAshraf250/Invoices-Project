<?php

return [
    'page' => [
        'title' => 'المنظمات',
        'settings' => 'الإعدادات',
    ],

    'table' => [
        'id' => '#',
        'name' => 'اسم المنظمة',
        'description' => 'الوصف',
        'actions' => 'العمليات',
    ],

    'actions' => [
        'add' => 'إضافة منظمة',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'confirm' => 'تأكيد',
        'close' => 'إغلاق',
        'cancel' => 'إلغاء',
    ],

    'form' => [
        'name' => 'اسم المنظمة',
        'description' => 'ملاحظات',
    ],

    'messages' => [
        'delete_confirmation' => 'هل أنت متأكد من عملية الحذف؟',
        'created' => 'تمت إضافة المنظمة بنجاح.',
        'updated' => 'تم تحديث المنظمة بنجاح.',
        'deleted' => 'تم حذف المنظمة بنجاح.',
        'cannot_delete_with_dependencies' => 'لا يمكن حذف هذه المنظمة لوجود منتجات أو فواتير مرتبطة بها.',
    ],
];
