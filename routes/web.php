<?php
// ROUTES — Mini E-Commerce Builder

$router->get('/',                         'DashboardController', 'index');
$router->get('/dashboard',               'DashboardController', 'index');

$router->get('/products',                'ProductController',   'index');
$router->get('/products/create',         'ProductController',   'create');
$router->post('/products/store',         'ProductController',   'store');
$router->get('/products/edit/{id}',      'ProductController',   'edit');
$router->post('/products/update/{id}',   'ProductController',   'update');
$router->post('/products/delete/{id}',   'ProductController',   'delete');

$router->get('/categories',              'CategoryController',  'index');
$router->post('/categories/store',       'CategoryController',  'store');
$router->post('/categories/update/{id}', 'CategoryController',  'update');
$router->post('/categories/delete/{id}', 'CategoryController',  'delete');

$router->get('/orders',                  'OrderController',     'index');
$router->get('/orders/create',           'OrderController',     'create');
$router->post('/orders/store',           'OrderController',     'store');
$router->get('/orders/export/{format}',  'OrderController',     'export');
$router->post('/orders/record-payment/{id}', 'OrderController',  'recordPayment');
$router->get('/orders/print-invoice/{id}', 'OrderController',   'printInvoice');
$router->get('/orders/print-receipt/{id}', 'OrderController',   'printReceipt');
$router->get('/orders/count-pending',      'OrderController',   'countPending');
$router->get('/offline',                   'OrderController',   'offlinePage');
$router->get('/orders/{id}',             'OrderController',     'show');
$router->post('/orders/update-status/{id}', 'OrderController',  'updateStatus');

$router->get('/reports',                 'ReportController',    'index');
$router->get('/reports/sales',           'ReportController',    'sales');

$router->get('/settings',                       'SettingController',   'index');
$router->get('/settings/roles',                 'SettingController',   'rolesIndex');
$router->get('/settings/roles/create',          'SettingController',   'roleCreate');
$router->post('/settings/roles/store',          'SettingController',   'roleStore');
$router->get('/settings/roles/edit/{id}',       'SettingController',   'roleEdit');
$router->post('/settings/roles/update/{id}',    'SettingController',   'roleUpdate');
$router->post('/settings/roles/delete/{id}',    'SettingController',   'roleDelete');
$router->get('/settings/roles/{id}/permissions',  'SettingController',   'rolePermissions');
$router->post('/settings/roles/{id}/permissions', 'SettingController',   'updateRolePermissions');

$router->get('/users',                  'UserController',      'index');
$router->get('/users/create',           'UserController',      'create');
$router->post('/users/store',           'UserController',      'store');
$router->get('/users/edit/{id}',        'UserController',      'edit');
$router->post('/users/update/{id}',     'UserController',      'update');
$router->post('/users/delete/{id}',     'UserController',      'delete');

$router->get('/login',                  'AuthController',      'loginForm');
$router->post('/login',                 'AuthController',      'login');
$router->get('/logout',                 'AuthController',      'logout');

// ── Super Admin ───────────────────────────────────────
$router->get('/superadmin/login',              'SuperAuthController',  'loginForm');
$router->post('/superadmin/login',             'SuperAuthController',  'login');
$router->get('/superadmin/logout',             'SuperAuthController',  'logout');
$router->get('/superadmin/dashboard',          'SuperAdminController', 'dashboard');
$router->get('/superadmin/stores',             'SuperAdminController', 'stores');
$router->get('/superadmin/stores/create',      'SuperAdminController', 'storeCreate');
$router->post('/superadmin/stores/store',      'SuperAdminController', 'storeStore');
$router->get('/superadmin/stores/toggle/{id}', 'SuperAdminController', 'storeToggle');
$router->post('/superadmin/stores/delete/{id}','SuperAdminController', 'storeDelete');

// ── Halaman Publik Toko (multi-tenant via slug) ───────
$router->get('/toko/{slug}',                   'StorePageController',  'show');
$router->post('/toko/{slug}/order',            'StorePageController',  'order');

// ── Super Admin — Settings ────────────────────────────
$router->get('/superadmin/settings',                                    'SuperAdminSettingController', 'index');
$router->post('/superadmin/settings/system',                            'SuperAdminSettingController', 'saveSystem');
$router->get('/superadmin/settings/store/{id}',                         'SuperAdminSettingController', 'storeEdit');
$router->post('/superadmin/settings/store/{id}/save',                   'SuperAdminSettingController', 'storeSave');
$router->get('/superadmin/settings/store/{id}/roles',                   'SuperAdminSettingController', 'storeRoles');
$router->post('/superadmin/settings/roles/{id}/permission',             'SuperAdminSettingController', 'togglePermission');

// ── Demo Mode ─────────────────────────────────────────────
$router->get('/demo',              'DemoController', 'index');
$router->get('/demo/{niche}',      'DemoController', 'preview');
$router->post('/demo/{niche}/order','DemoController', 'demoOrder');
