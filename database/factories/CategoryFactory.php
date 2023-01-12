<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition()
    {
        $name = fake()->name();

        return [
            'parent_category_id' => fake()->randomNumber(),
            'name' => $name,
            'alias' => Str::slug($name),
            'description' => fake()->text(),
            'status' => 1
        ];
    }
}
