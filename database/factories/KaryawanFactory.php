<?php

namespace Database\Factories;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Karyawan>
 */
class KaryawanFactory extends Factory
{
    protected $model = Karyawan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => fake()->unique()->numerify('EMP####'),
            'nm_lengkap' => fake()->name(),
            'tgl_masuk' => fake()->date(),
            'id_jabatan' => 1,
            'id_departemen' => 1,
            'id_subdepartemen' => null,
            'id_divisi' => null,
        ];
    }
}
