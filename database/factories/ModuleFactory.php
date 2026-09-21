<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id'  => Project::factory(),
            'name'        => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'sort_order'  => $this->faker->numberBetween(0, 10),
            'status'      => 'active',
        ];
    }
}
