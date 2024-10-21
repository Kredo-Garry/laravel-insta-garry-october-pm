<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    private $category;

    public function __construct(Category $category){
        $this->category = $category;
    }


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Make sure that you don't hav any duplicate name of categories
         */
        $categories = [
            [
                'name'          => 'Machines',
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'name'          => 'Books',
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'name'          => 'Computer Programming',
                'created_at'    => now(),
                'updated_at'    => now()
            ]
        ];

        $this->category->insert($categories);
    }
}
