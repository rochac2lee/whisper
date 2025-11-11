<?php

namespace Database\Factories;

use App\Models\MessageType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MessageType>
 */
class MessageTypeFactory extends Factory
{
  protected $model = MessageType::class;

  public function definition(): array
  {
    $name = fake()->unique()->words(2, true);

    return [
      'name' => Str::headline($name),
      'description' => fake()->sentence(8),
      'color' => fake()->hexColor(),
      'active' => true,
    ];
  }
}
