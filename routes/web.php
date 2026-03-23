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

// ── Varian Produk ─────────────────────────────────────────────────
$router->get('/variants',                  'VariantController', 'index');
$router->post('/variants/type/store',      'VariantController', 'typeStore');
$router->post('/variants/type/update/{id}','VariantController', 'typeUpdate');
$router->post('/variants/type/delete/{id}','VariantController', 'typeDelete');
$router->get('/variants/api/types',        'VariantController', 'apiTypes');
$router->post('/variants/stock/update',       'VariantController', 'stockUpdate');


// ── Inventory / Stok Bahan ─────────────────────────────────────
// ── Stok Produk ─────────────────────────────────────────
$router->get('/product-stock',              'ProductStockController', 'index');
$router->post('/product-stock/stock-in',    'ProductStockController', 'stockIn');
$router->post('/product-stock/adjust/{id}', 'ProductStockController', 'adjust');
$router->get('/product-stock/logs',         'ProductStockController', 'logs');

$router->get('/inventory',               'InventoryController', 'index');
$router->get('/inventory/create',        'InventoryController', 'create');
$router->post('/inventory/store',        'InventoryController', 'store');
$router->get('/inventory/edit/{id}',     'InventoryController', 'edit');
$router->post('/inventory/update/{id}',  'InventoryController', 'update');
$router->post('/inventory/delete/{id}',  'InventoryController', 'delete');
$router->post('/inventory/stock-in',     'InventoryController', 'stockIn');
$router->post('/inventory/adjust/{id}',  'InventoryController', 'adjust');
$router->get('/inventory/logs',          'InventoryController', 'logs');
$router->get('/inventory/api/list',      'InventoryController', 'apiList');

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
$router->get('/reports/ingredients',          'ReportController', 'ingredients');
$router->get('/reports/sales/export',         'ReportController', 'exportSales');
$router->get('/reports/ingredients/export',   'ReportController', 'exportIngredients');
$router->get('/reports/variants',              'ReportController', 'variants');

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
$router->post('/superadmin/stores/plan/{id}',  'SuperAdminController', 'storePlan');

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

// ── Push Notifications (PWA) ──────────────────────────────
$router->post('/push/subscribe',    'PushNotificationController', 'subscribe');
$router->post('/push/unsubscribe',  'PushNotificationController', 'unsubscribe');
$router->post('/push/send',         'PushNotificationController', 'sendNotification');

// ── API Routes ────────────────────────────────────────
$router->post('/api/auth/login', 'AuthApiController', 'login');
$router->get('/api/auth/me',     'AuthApiController', 'me');
$router->get('/api/dashboard', 'DashboardApiController', 'index');

$router->get('/api/orders',                  'OrderApiController', 'index');
$router->get('/api/orders/detail',           'OrderApiController', 'show');
$router->post('/api/orders/update-status',   'OrderApiController', 'updateStatus');

$router->get('/api/products',                    'ProductApiController', 'index');
$router->post('/api/products/toggle-available',  'ProductApiController', 'toggleAvailable');
$router->post('/api/products/update-stock',      'ProductApiController', 'updateStock');

$router->get('/api/reports', 'ReportApiController', 'index');

$router->post('/api/orders/store', 'OrderApiController', 'store');
$router->post('/api/orders/update-payment', 'OrderApiController', 'updatePaymentStatus');