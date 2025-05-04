<?php

namespace Database\Factories\Entity;

use App\Models\User\Client;
use App\Models\Entity\Entity;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Entity::class;

    protected $addresses = [
        "Brīvības iela 12",
        "Raiņa bulvāris 45",
        "Lielā iela 3",
        "Dzirnavu iela 78",
        "Krišjāņa Barona iela 34",
        "Elizabetes iela 56",
        "Terbatas iela 23",
        "Gertrūdes iela 67",
        "Stabu iela 89",
        "Avotu iela 11",
        "Tallinas iela 22",
        "Maskavas iela 33",
        "Krasta iela 44",
        "Valdemāra iela 55",
        "Ģertrūdes iela 66",
        "Lāčplēša iela 77",
        "Kr. Barona iela 88",
        "Skolas iela 99",
        "Pērses iela 10",
        "Kalnciema iela 20"
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isIndividual = fake()->boolean(30);
        $entityType = $isIndividual ? 'individual' : fake()->randomElement(['legal_entity',]); // si => State Institution
        $chanceOfGettingTrue = fake()->boolean(80); // 80% chance to generate true
        if ($entityType === 'legal_entity') {
            // Retrieve the array keys from the configuration
            $legalForms = array_keys(config('static_data.legal_forms.legal_entity'));
            $legal_form = $chanceOfGettingTrue ? 'llc' : fake()->randomElement(
                array_diff($legalForms, ['llc']) // Filter out 'llc' from the array
            );
            // } elseif ($entityType === 'si') {
            //     $legal_form = [
            //         'State Institution', // Valsts iestāde
            //         'Municipal Institution', // Pašvaldības iestāde
            //     ];
        } elseif ($entityType === 'individual') {
            $legal_form = fake()->randomElement(
                array_keys(config('static_data.legal_forms.individual'))
            );
        }

        $name = $entityType === 'legal_entity' ? fake()->company() : null;
        $gender = fake()->randomElement(['male', 'female']);
        $firstName = $entityType === 'individual' ? fake()->firstName($gender) : null;
        $lastName = $entityType === 'individual' ? fake()->lastName($gender) : null;
        $idNumber = fake()->unique()->numerify('###########');
        $country = fake()->randomElement(['LV', 'LT', 'EE']);
        $vatNumber = $chanceOfGettingTrue ? $country . $idNumber : null;
        $country = $country === 'LV' ? 'Latvija' : ($country === 'LT' ? 'Lietuva' : 'Igaunija');
        $entityableType = fake()->randomElement(['user', 'client']);

        return [
            'entityable_id' => $entityableType === 'user' ?
                User::inRandomOrder()->first()->id ?? User::factory() :
                Client::inRandomOrder()->first()->id ?? Client::factory(),
            'entityable_type' => $entityableType,
            'entity_type' => $entityType,
            'legal_form' => $legal_form,
            'name' => $name,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'id_number' => $idNumber,
            'vat_number' => $vatNumber,
            // 'address' => substr(fake()->streetName, 9),
            'address' => fake()->randomElement($this->addresses),
            'city' => fake()->city,
            'country' => $country,
            'postal_code' => fake()->postcode,
            'note' => fake()->optional(75)->sentence,
        ];
    }
}
