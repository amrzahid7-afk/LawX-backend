<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Legal_request;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        // نحضر قضية عشوائية موجودة
        $case = \App\Models\CaseModel::inRandomOrder()->first();

        // إذا لم توجد قضايا، ننشئ واحدة مؤقتاً (لمنع الخطأ في الفاكتوري)
        if (!$case) {
            $case = \App\Models\CaseModel::factory()->create();
        }

        return [
            'id' => Str::uuid(),
            'lawyer_id' => $case->lawer_id, // المحامي المسؤول عن القضية
            'client_id' => $case->clients()->first()->user_id, // أحد العملاء الموجودين في القضية
            'case_id' => $case->id, // القضية
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->paragraph,
        ];
    }
}
