<?php

namespace Database\Factories;

use App\Models\Record;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RecordFileFactory extends Factory
{
    public function definition(): array
    {
        $ext = $this->faker->randomElement(['jpg', 'pdf', 'xlsx', 'txt']);
        $uuid = Str::uuid()->toString();

        return [
            'record_id'     => Record::factory(),
            'original_name' => $this->faker->word() . '.' . $ext,
            'display_name'  => $this->faker->words(3, true),
            'note'          => $this->faker->optional()->sentence(),
            'storage_path'  => "records/2026/09/{$uuid}.{$ext}",
            'mime_type'     => 'application/octet-stream',
            'extension'     => $ext,
            'file_size'     => $this->faker->numberBetween(1000, 1000000),
            'sort_order'    => $this->faker->numberBetween(0, 10),
            'uploaded_by'   => null,
        ];
    }
}
