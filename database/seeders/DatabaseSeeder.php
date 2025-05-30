<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $products = [
        [
            'title' => 'Ковбаса Імператорська',
            'description' => 'Куряча. Першого сорту',
            'image' => 'imperator.jpg',
            'price' => 25,
            'termin' => 25,
            'quantity' => 10,
        ],
        [
            'title' => 'Ковбаса Брауншвейгска',
            'description' => 'Яловичина. Вищого сорту',
            'image' => 'grand.jpg',
            'price' => 45,
            'termin' => 15,
            'quantity' => 20,
        ],
        [
            'title' => 'Ковбаса Гранд-Філе',
            'description' => 'Куряча. Вищого сорту',
            'image' => 'braun.jpg',
            'price' => 35,
            'termin' => 12,
            'quantity' => 18,
        ],
        [
            'title' => 'Сосиски',
            'description' => 'Курячі. Вищого сорту',
            'image' => 'sosiski.jpg',
            'price' => 32,
            'termin' => 17,
            'quantity' => 23,
        ],
        [
            'title' => 'Сосиски Улюблені',
            'description' => 'Свинина. Першого сорту',
            'image' => 'sosiski.sv.jpg',
            'price' => 30,
            'termin' => 25,
            'quantity' => 10,
        ],
        [
            'title' => 'Сарделькі Смачні',
            'description' => 'Свинина. Вищого сорту',
            'image' => 'sardelki.jpg',
            'price' => 33,
            'termin' => 20,
            'quantity' => 8,
        ],

    ];

        foreach ($products as $product) {
            DB::insert(" INSERT INTO `products`(`title`, `description`, `image`, `price`, `termin`, `quantity`) VALUES
                                                                ('{$product['title']}', '{$product['description']}', '{$product['image']}',
                                                                 '{$product['price']}', '{$product['termin']}', '{$product['quantity']}')");
        }

        $clients = [
            [
                'client_name' => 'ФОП Иванов',
                'name' => 'Иванов И.И.',
                'email' => 'ivanov@gmail.com',
                'tel' => '+380986703535',
                'address' => 'м.Одеса, вул. Канатна 150'
            ],
            [
                'client_name' => 'ФОП Симоненко',
                'name' => 'Симоненко А.И.',
                'email' => 'simonenko@gmail.com',
                'tel' => '+380976333535',
                'address' => 'Одеська обл., м.Маяки, вул. Шевченка 45'
            ],
            [
                'client_name' => 'ФОП Семенченко',
                'name' => 'Семенченко И.М.',
                'email' => 'semenchenko@gmail.com',
                'tel' => '+380976333999',
                'address' => 'м.Одеса, вул. ген.Петрова 75'
            ],
            [
                'client_name' => 'ФОП Євтушенко',
                'name' => 'Євтушенко М.О.',
                'email' => 'evtushenko@gmail.com',
                'tel' => '+380976333999',
                'address' => 'м.Одеса, вул. ген.Петрова 75'
            ],
        ];

        $users = [
            [
                'name' => 'Иванов И.И.',
                'email' => 'user@gmail.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            ],
            [
                'name' => 'Симоненко А.И.',
                'email' => 'user2@gmail.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            ],
            [
                'name' => 'Семенченко И.М.',
                'email' => 'user3@gmail.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            ],
            [
                'name' => 'Євтушенко М.О.',
                'email' => 'user4@gmail.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            ],
        ];

        foreach ($users as $client) {
            DB::insert(" INSERT INTO `users`( `name`, `email`, `password`)
                        VALUES ('{$client['name']}', '{$client['email']}', '{$client['password']}')");
        }

        $n = 1;
        foreach ($clients as $client) {
            DB::insert(" INSERT INTO `clients`(`client_name`, `name`, `email`, `tel`, `address`, `user_id`)
                        VALUES ('{$client['client_name']}', '{$client['name']}', '{$client['email']}', '{$client['tel']}',
                                '{$client['address']}', $n)");
            $n++;
        }
    }
}
