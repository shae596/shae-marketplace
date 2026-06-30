<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'MULEMBWE NGUBA SHARONE',
            'email' => 'sharonemulembweng@gmail.com',
            'password' => 'password',
            'phone' => '+243900000001',
            'role' => UserRole::Admin,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $gestionnaire = User::create([
            'name' => 'Gestionnaire SHAE',
            'email' => 'gestionnaire@exemple.com',
            'password' => 'password',
            'phone' => '+243900000002',
            'role' => UserRole::Gestionnaire,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $client = User::create([
            'name' => 'Client SHAE',
            'email' => 'client@exemple.com',
            'password' => 'password',
            'phone' => '+243900000004',
            'role' => UserRole::Client,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $categories = collect([
            ['name' => 'Électronique', 'description' => 'Smartphones, casques et accessoires tech'],
            ['name' => 'Mode', 'description' => 'Vêtements, chaussures et accessoires'],
            ['name' => 'Maison', 'description' => 'Décoration, cuisine et bricolage'],
            ['name' => 'Alimentation', 'description' => 'Produits alimentaires locaux'],
            ['name' => 'Beauté', 'description' => 'Soins, parfums et cosmétiques'],
        ])->mapWithKeys(function ($cat) {
            $category = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
            ]);

            return [$cat['name'] => $category];
        });

        $productsData = [
            ['name' => 'Smartphone Android', 'category' => 'Électronique', 'price' => 350.00, 'stock' => 15],
            ['name' => 'Casque Bluetooth', 'category' => 'Électronique', 'price' => 45.00, 'stock' => 30],
            ['name' => 'Coque silicone smartphone', 'category' => 'Électronique', 'price' => 6.00, 'stock' => 150],
            ['name' => 'T-shirt coton femme', 'category' => 'Mode', 'price' => 12.50, 'stock' => 100],
            ['name' => 'Jean slim femme', 'category' => 'Mode', 'price' => 28.00, 'stock' => 50],
            ['name' => 'Basket running femme', 'category' => 'Mode', 'price' => 55.00, 'stock' => 35],
            ['name' => 'Lampe de bureau LED', 'category' => 'Maison', 'price' => 18.00, 'stock' => 25],
            ['name' => 'Set casseroles inox', 'category' => 'Maison', 'price' => 55.00, 'stock' => 20],
            ['name' => 'Huile de palme bio 1L', 'category' => 'Alimentation', 'price' => 4.50, 'stock' => 200],
            ['name' => 'Riz local premium 5kg', 'category' => 'Alimentation', 'price' => 8.00, 'stock' => 150],
            ['name' => 'Crème hydratante visage', 'category' => 'Beauté', 'price' => 9.50, 'stock' => 120],
            ['name' => 'Parfum eau de toilette 50ml', 'category' => 'Beauté', 'price' => 24.00, 'stock' => 60],
            ['name' => 'Shampooing nourrissant', 'category' => 'Beauté', 'price' => 7.50, 'stock' => 90],
        ];

        $products = collect($productsData)->map(function ($data, $index) use ($gestionnaire, $categories) {
            return Product::create([
                'user_id' => $gestionnaire->id,
                'category_id' => $categories[$data['category']]->id,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']).'-'.($index + 1),
                'description' => $data['name'].' — produit de qualité disponible sur SHAE.',
                'price' => $data['price'],
                'stock' => $data['stock'],
                'status' => 'approved',
            ]);
        });

        $order = Order::create([
            'reference' => 'SHAE-'.strtoupper(Str::random(8)),
            'user_id' => $client->id,
            'total' => 395.00,
            'status' => 'paid',
            'shipping_address' => '12 Avenue du Commerce, Kinshasa',
            'shipping_phone' => '+243900000004',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $products[0]->id,
            'quantity' => 1,
            'unit_price' => 350.00,
            'subtotal' => 350.00,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $products[1]->id,
            'quantity' => 1,
            'unit_price' => 45.00,
            'subtotal' => 45.00,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'user_id' => $client->id,
            'amount' => 395.00,
            'provider' => 'labpay',
            'reference' => 'PAY-'.strtoupper(Str::random(10)),
            'phone' => '+243900000004',
            'status' => 'success',
            'paid_at' => now()->subDay(),
        ]);

        unset($admin, $gestionnaire, $client);
    }
}
