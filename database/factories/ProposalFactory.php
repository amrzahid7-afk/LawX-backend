<?php

namespace Database\Factories;

use App\Enums\RoleSlug;
use App\Models\Document;
use App\Models\Legal_request;
use App\Models\Proposal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProposalFactory extends Factory
{
    protected $model = Proposal::class;

    public function definition(): array
    {
        $lawyer_role = Role::where('slug', RoleSlug::Lawyer)->first();
        $lawyer = User::where('role_id', $lawyer_role->id)->inRandomOrder()->first();

        if (!$lawyer) {
            $lawyer = User::factory()->create(['role_id' => $lawyer_role->id]);
        }

        $legalRequest = Legal_request::inRandomOrder()->first();
        if (!$legalRequest) {
            $legalRequest = Legal_request::factory()->create();
        }

        return [
            'id' => Str::uuid(),
            'price' => $this->faker->randomFloat(2, 100, 5000),
            'estimated_days' => $this->faker->numberBetween(5, 60) . ' days',
            'message_for_client' => $this->faker->sentence(10),
            'valid_until' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'payment_terms' => '50% upfront, 50% on completion',
            'status' => $this->faker->randomElement(['rejected', 'accepted', 'pending']),
            'price_currency' => $this->faker->randomElement(['EGP', 'USD', 'SYP']),
            'request_id' => $legalRequest->id,
            'lawer_id' => $lawyer->id,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Proposal $proposal) {
            // استبدال count بحلقة for لضمان تطبيق المسار لكل ملف
            for ($i = 0; $i < rand(1, 3); $i++) {
                Document::factory()
                    ->folder('documents/proposals')
                    ->create([
                        'case_id' => null,
                        'legal_request_id' => null,
                        'proposal_id' => $proposal->id,
                        'uploaded_by' => $proposal->lawer_id,
                    ]);
            }
        });
    }
}
