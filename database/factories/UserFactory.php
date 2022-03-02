<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\User::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->firstName." ".$this->faker->lastName,
            'uuid' => Str::uuid(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$M6gjUTgFzX9E3/xyU1.YV.wpG37FrpJaiWMM9JmJZzvos3We2nnKG', //12345678
            'phone_number' => $this->faker->phoneNumber
        ];
    }
}
