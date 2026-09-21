<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Record;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'module_id'  => null,
            'type'       => $this->faker->randomElement(Record::TYPES),
            'title'      => $this->faker->sentence(5),
            'content'    => $this->faker->paragraphs(2, true),
            'source'     => 'manual',
            'git_branch' => null,
            'git_commit' => null,
            'created_by' => null,
        ];
    }
}
