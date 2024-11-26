<?php

namespace Database\Factories;

use App\Enums\MessageStatus;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid'         => Str::uuid()->toString(),
            'phone_number' => '+9055' . $this->faker->numberBetween(10000000, 99999999),
            'content'      => $this->faker->sentence(),
            'status'       => MessageStatus::PENDING->value,
            'sent_at'      => null,
        ];
    }
}
