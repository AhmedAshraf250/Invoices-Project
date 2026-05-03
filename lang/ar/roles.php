<?php

return [
    'page' => [
        'title' => 'إدارة الأدوار',
        'subtitle' => 'إنشاء الأدوار وربطها بالصلاحيات المناسبة.',
        'details' => 'عرض الدور',
    ],
    'table' => [
        'name' => 'اسم الدور',
        'system_name' => 'الاسم التقني',
        'users_count' => 'عدد المستخدمين',
        'permissions_count' => 'عدد الصلاحيات',
        'actions' => 'الإجراءات',
    ],
    'form' => [
        'name' => 'الاسم التقني',
        'display_name_ar' => 'الاسم العربي',
        'display_name_en' => 'الاسم الإنجليزي',
        'permissions' => 'صلاحيات الدور',
    ],
    'actions' => [
        'add' => 'إضافة دور',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'show' => 'عرض',
        'back' => 'العودة إلى الأدوار',
        'view_permissions' => 'عرض الصلاحيات',
        'view_users' => 'عرض المستخدمين',
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
    ],
    'messages' => [
        'created' => 'تم إنشاء الدور بنجاح.',
        'updated' => 'تم تحديث الدور بنجاح.',
        'deleted' => 'تم حذف الدور بنجاح.',
        'cannot_delete_assigned_role' => 'لا يمكن حذف دور مرتبط بمستخدمين.',
        'super_admin_locked' => 'دور السوبر-أدمن محمي من التعديل والحذف.',
    ],
    'details' => [
        'overview' => 'معلومات الدور',
        'quick_actions' => 'إجراءات سريعة',
        'assigned_users' => 'المستخدمون المرتبطون',
        'permissions' => 'صلاحيات الدور',
        'empty_permissions' => 'لا توجد صلاحيات مرتبطة بهذا الدور.',
        'users_empty' => 'لا يوجد مستخدمون مرتبطون بهذا الدور.',
    ],
];
