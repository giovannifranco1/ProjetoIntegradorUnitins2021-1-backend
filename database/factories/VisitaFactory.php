<?php

namespace Database\Factories;

use App\Models\Visita;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Visita::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'horario_estimado_visita' => $this->faker->dateTime('now' ,'+01 days'),
            'dia_visita' => $this->faker->date(),
            'id_tecnico',
            'id_propriedade',
            'motivo_visita' => $this->faker->name,
            'status' => $this->faker->name,
            'obeservacao'
        ];
    }
}
