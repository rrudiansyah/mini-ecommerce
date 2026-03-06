<?php

class DemoController extends Controller
{
    // Data dummy per niche
    private array $dummyData = [
        'coffee' => [
            'store' => [
                'id' => 0, 'name' => 'Kopi Nusantara', 'niche' => 'coffee',
                'slug' => '_demo_coffee', 'phone' => '081234567890',
                'address' => 'Jl. Sudirman No. 12, Jakarta', 'is_active' => 1,
                'description' => 'Kopi pilihan dari seluruh nusantara',
                'theme_color' => '#c9a96e', 'logo' => null,
            ],
            'categories' => [
                ['id' => 1, 'name' => 'Kopi Panas', 'icon' => '☕'],
                ['id' => 2, 'name' => 'Kopi Dingin', 'icon' => '🧊'],
                ['id' => 3, 'name' => 'Non Kopi', 'icon' => '🍵'],
            ],
            'products' => [
                ['id' => 1, 'name' => 'Americano', 'price' => 25000, 'category_id' => 1, 'description' => 'Espresso dengan air panas', 'image' => null, 'stock' => 99],
                ['id' => 2, 'name' => 'Cappuccino', 'price' => 32000, 'category_id' => 1, 'description' => 'Espresso, susu, dan foam', 'image' => null, 'stock' => 99],
                ['id' => 3, 'name' => 'Latte', 'price' => 35000, 'category_id' => 1, 'description' => 'Espresso dengan susu steamed', 'image' => null, 'stock' => 99],
                ['id' => 4, 'name' => 'Es Kopi Susu', 'price' => 28000, 'category_id' => 2, 'description' => 'Kopi susu segar dingin', 'image' => null, 'stock' => 99],
                ['id' => 5, 'name' => 'Cold Brew', 'price' => 38000, 'category_id' => 2, 'description' => 'Kopi seduh dingin 12 jam', 'image' => null, 'stock' => 99],
                ['id' => 6, 'name' => 'Matcha Latte', 'price' => 35000, 'category_id' => 3, 'description' => 'Matcha premium dari Jepang', 'image' => null, 'stock' => 99],
                ['id' => 7, 'name' => 'Teh Tarik', 'price' => 22000, 'category_id' => 3, 'description' => 'Teh manis creamy khas Malaysia', 'image' => null, 'stock' => 99],
            ],
        ],
        'laundry' => [
            'store' => [
                'id' => 0, 'name' => 'Clean & Fresh Laundry', 'niche' => 'laundry',
                'slug' => '_demo_laundry', 'phone' => '082345678901',
                'address' => 'Jl. Melati No. 5, Bandung', 'is_active' => 1,
                'description' => 'Laundry kiloan & satuan terpercaya',
                'theme_color' => '#2e86de', 'logo' => null,
            ],
            'categories' => [
                ['id' => 1, 'name' => 'Cuci Kiloan', 'icon' => '⚖️'],
                ['id' => 2, 'name' => 'Cuci Satuan', 'icon' => '👔'],
                ['id' => 3, 'name' => 'Express', 'icon' => '⚡'],
            ],
            'products' => [
                ['id' => 1, 'name' => 'Cuci + Setrika (Regular)', 'price' => 7000, 'category_id' => 1, 'description' => 'Per kg, selesai 2-3 hari', 'image' => null, 'stock' => 99],
                ['id' => 2, 'name' => 'Cuci Kering (Regular)', 'price' => 5000, 'category_id' => 1, 'description' => 'Per kg, tanpa setrika', 'image' => null, 'stock' => 99],
                ['id' => 3, 'name' => 'Sepatu Sneakers', 'price' => 35000, 'category_id' => 2, 'description' => 'Cuci bersih & deodorize', 'image' => null, 'stock' => 99],
                ['id' => 4, 'name' => 'Jaket', 'price' => 25000, 'category_id' => 2, 'description' => 'Cuci + setrika jaket', 'image' => null, 'stock' => 99],
                ['id' => 5, 'name' => 'Bed Cover', 'price' => 45000, 'category_id' => 2, 'description' => 'Cuci bersih bed cover besar', 'image' => null, 'stock' => 99],
                ['id' => 6, 'name' => 'Express 6 Jam', 'price' => 12000, 'category_id' => 3, 'description' => 'Per kg, selesai 6 jam', 'image' => null, 'stock' => 99],
                ['id' => 7, 'name' => 'Express 3 Jam', 'price' => 18000, 'category_id' => 3, 'description' => 'Per kg, selesai 3 jam', 'image' => null, 'stock' => 99],
            ],
        ],
        'barbershop' => [
            'store' => [
                'id' => 0, 'name' => 'Rapi Barbershop', 'niche' => 'barbershop',
                'slug' => '_demo_barbershop', 'phone' => '083456789012',
                'address' => 'Jl. Pemuda No. 88, Surabaya', 'is_active' => 1,
                'description' => 'Barbershop & salon pria profesional',
                'theme_color' => '#3a7d44', 'logo' => null,
            ],
            'categories' => [
                ['id' => 1, 'name' => 'Potong Rambut', 'icon' => '✂️'],
                ['id' => 2, 'name' => 'Perawatan', 'icon' => '💆'],
                ['id' => 3, 'name' => 'Paket Hemat', 'icon' => '🎁'],
            ],
            'products' => [
                ['id' => 1, 'name' => 'Potong Rambut Reguler', 'price' => 35000, 'category_id' => 1, 'description' => 'Potong rambut + cuci', 'image' => null, 'stock' => 99],
                ['id' => 2, 'name' => 'Fade / Undercut', 'price' => 50000, 'category_id' => 1, 'description' => 'Teknik fade modern', 'image' => null, 'stock' => 99],
                ['id' => 3, 'name' => 'Cukur Jenggot', 'price' => 25000, 'category_id' => 1, 'description' => 'Shaping & trimming jenggot', 'image' => null, 'stock' => 99],
                ['id' => 4, 'name' => 'Creambath', 'price' => 75000, 'category_id' => 2, 'description' => 'Perawatan rambut intensif', 'image' => null, 'stock' => 99],
                ['id' => 5, 'name' => 'Hair Mask', 'price' => 60000, 'category_id' => 2, 'description' => 'Masker rambut premium', 'image' => null, 'stock' => 99],
                ['id' => 6, 'name' => 'Paket Pria Lengkap', 'price' => 100000, 'category_id' => 3, 'description' => 'Potong + jenggot + creambath', 'image' => null, 'stock' => 99],
                ['id' => 7, 'name' => 'Paket Anak', 'price' => 45000, 'category_id' => 3, 'description' => 'Potong + cuci + styling', 'image' => null, 'stock' => 99],
            ],
        ],
        'restaurant' => [
            'store' => [
                'id' => 0, 'name' => 'Warung Segar', 'niche' => 'restaurant',
                'slug' => '_demo_restaurant', 'phone' => '084567890123',
                'address' => 'Jl. Raya Bogor No. 100, Bogor', 'is_active' => 1,
                'description' => 'Masakan rumahan segar setiap hari',
                'theme_color' => '#2d6a4f', 'logo' => null,
            ],
            'categories' => [
                ['id' => 1, 'name' => 'Makanan Utama', 'icon' => '🍛'],
                ['id' => 2, 'name' => 'Lauk Pauk', 'icon' => '🍗'],
                ['id' => 3, 'name' => 'Minuman', 'icon' => '🥤'],
            ],
            'products' => [
                ['id' => 1, 'name' => 'Nasi Goreng Spesial', 'price' => 25000, 'category_id' => 1, 'description' => 'Nasi goreng telur + ayam', 'image' => null, 'stock' => 99],
                ['id' => 2, 'name' => 'Ayam Penyet', 'price' => 30000, 'category_id' => 1, 'description' => 'Ayam goreng sambal penyet', 'image' => null, 'stock' => 99],
                ['id' => 3, 'name' => 'Soto Ayam', 'price' => 22000, 'category_id' => 1, 'description' => 'Soto bening khas Jawa', 'image' => null, 'stock' => 99],
                ['id' => 4, 'name' => 'Tempe Goreng', 'price' => 8000, 'category_id' => 2, 'description' => 'Tempe goreng kriuk', 'image' => null, 'stock' => 99],
                ['id' => 5, 'name' => 'Ayam Bakar', 'price' => 35000, 'category_id' => 2, 'description' => 'Ayam bakar bumbu kecap', 'image' => null, 'stock' => 99],
                ['id' => 6, 'name' => 'Es Teh Manis', 'price' => 8000, 'category_id' => 3, 'description' => 'Teh manis dingin segar', 'image' => null, 'stock' => 99],
                ['id' => 7, 'name' => 'Jus Alpukat', 'price' => 18000, 'category_id' => 3, 'description' => 'Jus alpukat susu coklat', 'image' => null, 'stock' => 99],
            ],
        ],
        'fashion' => [
            'store' => [
                'id' => 0, 'name' => 'Green Thrift Store', 'niche' => 'fashion',
                'slug' => '_demo_fashion', 'phone' => '085678901234',
                'address' => 'Jl. Dago No. 45, Bandung', 'is_active' => 1,
                'description' => 'Thrift & fashion pilihan berkualitas',
                'theme_color' => '#3a7d44', 'logo' => null,
            ],
            'categories' => [
                ['id' => 1, 'name' => 'Atasan', 'icon' => '👕'],
                ['id' => 2, 'name' => 'Bawahan', 'icon' => '👖'],
                ['id' => 3, 'name' => 'Aksesoris', 'icon' => '👜'],
            ],
            'products' => [
                ['id' => 1, 'name' => 'Kaos Oversize Vintage', 'price' => 75000, 'category_id' => 1, 'description' => 'Kaos vintage all size', 'image' => null, 'stock' => 99],
                ['id' => 2, 'name' => 'Kemeja Flannel', 'price' => 120000, 'category_id' => 1, 'description' => 'Flannel kotak-kotak premium', 'image' => null, 'stock' => 99],
                ['id' => 3, 'name' => 'Jaket Denim', 'price' => 250000, 'category_id' => 1, 'description' => 'Jaket denim second branded', 'image' => null, 'stock' => 99],
                ['id' => 4, 'name' => 'Celana Jeans Slim', 'price' => 150000, 'category_id' => 2, 'description' => 'Jeans slim fit second', 'image' => null, 'stock' => 99],
                ['id' => 5, 'name' => 'Rok Mini Plaid', 'price' => 95000, 'category_id' => 2, 'description' => 'Rok kotak-kotak estetik', 'image' => null, 'stock' => 99],
                ['id' => 6, 'name' => 'Tote Bag Canvas', 'price' => 65000, 'category_id' => 3, 'description' => 'Totebag canvas eco friendly', 'image' => null, 'stock' => 99],
                ['id' => 7, 'name' => 'Topi Bucket', 'price' => 55000, 'category_id' => 3, 'description' => 'Bucket hat aesthetic', 'image' => null, 'stock' => 99],
            ],
        ],
        'bakery' => [
            'store' => [
                'id' => 0, 'name' => 'Sweet Corner Bakery', 'niche' => 'bakery',
                'slug' => '_demo_bakery', 'phone' => '086789012345',
                'address' => 'Jl. Kenanga No. 7, Yogyakarta', 'is_active' => 1,
                'description' => 'Kue & roti fresh setiap pagi',
                'theme_color' => '#3a7d44', 'logo' => null,
            ],
            'categories' => [
                ['id' => 1, 'name' => 'Kue Basah', 'icon' => '🍰'],
                ['id' => 2, 'name' => 'Roti & Pastry', 'icon' => '🥐'],
                ['id' => 3, 'name' => 'Minuman', 'icon' => '☕'],
            ],
            'products' => [
                ['id' => 1, 'name' => 'Lapis Legit', 'price' => 180000, 'category_id' => 1, 'description' => 'Lapis legit original loyang 22cm', 'image' => null, 'stock' => 99],
                ['id' => 2, 'name' => 'Brownies Kukus', 'price' => 65000, 'category_id' => 1, 'description' => 'Brownies lembut coklat premium', 'image' => null, 'stock' => 99],
                ['id' => 3, 'name' => 'Bolu Pandan', 'price' => 55000, 'category_id' => 1, 'description' => 'Bolu pandan harum & lembut', 'image' => null, 'stock' => 99],
                ['id' => 4, 'name' => 'Croissant Butter', 'price' => 22000, 'category_id' => 2, 'description' => 'Croissant renyah isi butter', 'image' => null, 'stock' => 99],
                ['id' => 5, 'name' => 'Roti Sobek Keju', 'price' => 45000, 'category_id' => 2, 'description' => 'Roti sobek isi keju lumer', 'image' => null, 'stock' => 99],
                ['id' => 6, 'name' => 'Cinnamon Roll', 'price' => 28000, 'category_id' => 2, 'description' => 'Cinnamon roll icing lembut', 'image' => null, 'stock' => 99],
                ['id' => 7, 'name' => 'Kopi Susu Hangat', 'price' => 18000, 'category_id' => 3, 'description' => 'Kopi susu hangat original', 'image' => null, 'stock' => 99],
            ],
        ],
    ];

    private array $themeMap = [
        'coffee'     => 'themes/coffee/layout',
        'laundry'    => 'themes/laundry/layout',
        'barbershop' => 'themes/barbershop/layout',
        'restaurant' => 'themes/restaurant/layout',
        'fashion'    => 'themes/fashion/layout',
        'bakery'     => 'themes/bakery/layout',
    ];

    private array $nicheLabels = [
        'coffee'     => ['icon' => '☕', 'label' => 'Coffee Shop',  'desc' => 'Tema gelap elegan untuk kedai kopi'],
        'laundry'    => ['icon' => '🧺', 'label' => 'Laundry',      'desc' => 'Tema bersih segar untuk usaha laundry'],
        'barbershop' => ['icon' => '✂️', 'label' => 'Barbershop',   'desc' => 'Tema natural hijau untuk barbershop & salon'],
        'restaurant' => ['icon' => '🍛', 'label' => 'Restoran',     'desc' => 'Tema natural segar untuk warung & restoran'],
        'fashion'    => ['icon' => '👗', 'label' => 'Fashion',      'desc' => 'Tema organic untuk fashion & thrift store'],
        'bakery'     => ['icon' => '🍰', 'label' => 'Bakery',       'desc' => 'Tema earthy hangat untuk bakery & kue'],
    ];

    // ── Landing page demo ─────────────────────────────────────────
    public function index(): void
    {
        $this->view('layouts/blank', [
            'pageTitle' => 'Demo Tema — ' . APP_NAME,
            'content'   => 'demo/index',
            'niches'    => $this->nicheLabels,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    // ── Preview tema tertentu ─────────────────────────────────────
    public function preview(string $niche): void
    {
        if (!isset($this->dummyData[$niche])) {
            $this->redirect('demo');
            return;
        }

        $data  = $this->dummyData[$niche];
        $store = $data['store'];
        $products   = $data['products'];
        $categories = $data['categories'];

        // Kelompokkan produk per kategori
        $menu = [];
        foreach ($categories as $cat) {
            $menu[$cat['id']] = [
                'name'     => $cat['name'],
                'icon'     => $cat['icon'],
                'products' => [],
            ];
        }
        foreach ($products as $p) {
            if (isset($menu[$p['category_id']])) {
                $menu[$p['category_id']]['products'][] = $p;
            }
        }

        $theme = $this->themeMap[$niche] ?? 'themes/coffee/layout';

        // Render tema dengan flag demo=true
        $this->view($theme, [
            'store'      => $store,
            'products'   => $products,
            'categories' => $categories,
            'menu'       => $menu,
            'pageTitle'  => $store['name'],
            'isDemo'     => true,
            'demoNiche'  => $niche,
            'allNiches'  => $this->nicheLabels,
        ]);
    }

    // ── Handle order di demo mode (block, tampilkan pesan) ────────
    public function demoOrder(string $niche): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'demo'    => true,
            'message' => 'Ini adalah mode demo. Pesanan tidak diproses.',
        ]);
        exit;
    }
}
