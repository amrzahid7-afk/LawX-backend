<?php

namespace Database\Factories;

use App\Enums\RoleSlug;
use App\Models\Document;
use App\Models\Legal_request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LegalRequestFactory extends Factory
{
    protected $model = Legal_request::class;

    public function definition(): array
    {
        $client_role = Role::where('slug', RoleSlug::Client)->first();
        $client = User::where('role_id', $client_role->id)->inRandomOrder()->first();

        if (!$client) {
            $client = User::factory()->create(['role_id' => $client_role->id]);
        }

        $type = $this->faker->randomElement(['private', 'public']);
        $assigned_lawyer_id = null;

        if ($type === 'private') {
            $lawyer_role = Role::where('slug', RoleSlug::Lawyer)->first();
            $lawyer = User::where('role_id', $lawyer_role->id)->inRandomOrder()->first();
            if ($lawyer) {
                $assigned_lawyer_id = $lawyer->id;
            }
        }

        return [
            'id' => Str::uuid(),
            'title_request' => $this->faker->sentence(3),
            'description_request' => $this->faker->paragraph(5),
            'location_request' => $this->faker->city() . ', ' . $this->faker->state(),
            'budget_min' => $this->faker->numberBetween(100, 500),
            'budget_max' => $this->faker->numberBetween(500, 1000),
            'type' => $type,
            'status' => $this->faker->randomElement(['open', 'close', 'in_progress']),
            'client_id' => $client->id,
            'assigned_lawyer_id' => $assigned_lawyer_id,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Legal_request $request) {
            // استبدال count بحلقة for لضمان تطبيق المسار لكل ملف
            for ($i = 0; $i < rand(1, 3); $i++) {
                Document::factory()
                    ->folder('documents/legal_requests')
                    ->create([
                        'case_id' => null,
                        'legal_request_id' => $request->id,
                        'proposal_id' => null,
                        'uploaded_by' => $request->client_id,
                    ]);
            }
        });
    }
}
