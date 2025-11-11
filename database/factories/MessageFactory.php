<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\MessageType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
  protected $model = Message::class;

  public function definition(): array
  {
    $hasAttachment = fake()->boolean(25);
    $extension = fake()->randomElement([
      'pdf',
      'docx',
      'xlsx',
      'pptx',
      'jpg',
      'png',
      'zip',
      'txt',
    ]);

    $attachmentName = $hasAttachment
      ? Str::limit(
        fake()->unique()->words(3, true) . '.' . $extension,
        255,
        ''
      )
      : null;

    return [
      'message_type_id' => MessageType::query()->inRandomOrder()->value('id')
        ?? MessageType::factory(),
      'content' => fake()->paragraphs(fake()->numberBetween(2, 4), true),
      'attachment_path' => $hasAttachment
        ? 'attachments/' . fake()->uuid() . '.' . $extension
        : null,
      'attachment_name' => $attachmentName,
      'is_read' => fake()->boolean(60),
    ];
  }
}
