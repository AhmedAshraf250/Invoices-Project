<?php

return [
    'page' => [
        'title' => 'Roles Management',
        'subtitle' => 'Create roles and attach the right permissions.',
        'details' => 'Role Details',
    ],
    'table' => [
        'name' => 'Role name',
        'system_name' => 'System name',
        'users_count' => 'Users',
        'permissions_count' => 'Permissions',
        'actions' => 'Actions',
    ],
    'form' => [
        'name' => 'System name',
        'display_name_ar' => 'Arabic name',
        'display_name_en' => 'English name',
        'permissions' => 'Role permissions',
    ],
    'actions' => [
        'add' => 'Add role',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'show' => 'View',
        'back' => 'Back to roles',
        'view_permissions' => 'View permissions',
        'view_users' => 'View users',
        'save' => 'Save',
        'cancel' => 'Cancel',
    ],
    'messages' => [
        'created' => 'Role created successfully.',
        'updated' => 'Role updated successfully.',
        'deleted' => 'Role deleted successfully.',
        'cannot_delete_assigned_role' => 'A role assigned to users cannot be deleted.',
        'super_admin_locked' => 'The super-admin role is protected from editing and deletion.',
    ],
    'details' => [
        'overview' => 'Role overview',
        'quick_actions' => 'Quick actions',
        'assigned_users' => 'Assigned users',
        'permissions' => 'Role permissions',
        'empty_permissions' => 'This role has no permissions assigned.',
        'users_empty' => 'No users are assigned to this role.',
    ],
];
