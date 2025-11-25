<?php

namespace Database\Factories;

use App\Models\Orangtua;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrangtuaFactory extends Factory
{
    protected $model = Orangtua::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name('male') . ' & ' . $this->faker->name('female'),
            'email' => $this->faker->unique()->safeEmail(),
            'no_wa' => $this->faker->unique()->e164PhoneNumber(),
            // Preferensi notif acak
            'preferensi_notif' => json_encode([
                'absensi' => $this->faker->boolean(), 
                'nilai' => $this->faker->boolean(),
                'pengumuman' => $this->faker->boolean(80),
            ]),
        ];
    }
}