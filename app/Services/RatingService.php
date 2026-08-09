<?php

namespace App\Services;

use App\Jobs\FilterReviewWithAiJob;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;

class RatingService
{
    public function createReview(array $validatedData, $case): void
    {
        $averageRating = (
            $validatedData['professionalism_rating'] +
            $validatedData['communication_rating'] +
            $validatedData['result_rating']
        ) / 3;

        $finalRating = round($averageRating, 1);

        $reviewData = [
            'lawyer_id'             => $case->lead_lawyer_id,
            'client_id'             => auth('sanctum')->user()->id,
            'case_id'               => $case->id,
            'professionalism_rating' => $validatedData['professionalism_rating'],
            'communication_rating'  => $validatedData['communication_rating'],
            'result_rating'         => $validatedData['result_rating'],
            'rating'                => $finalRating,
            'comment'               => $validatedData['comment'] ?? null,
            'is_hidden'             => true
        ];
        $review = Review::create($reviewData);
        // activate Filter for new review 
        FilterReviewWithAiJob::dispatch($review);

        $this->refreshLawyerRatingCache($case->lead_lawyer_id);

        // return $review;
    }


    private function refreshLawyerRatingCache(string $lawyerId): void
    {
        $stats = Review::where('lawyer_id', $lawyerId)
            ->where('is_hidden', false)
            ->selectRaw('
                AVG(rating) as overall_rating,
                AVG(professionalism_rating) as avg_professionalism,
                AVG(communication_rating) as avg_communication,
                AVG(result_rating) as avg_result,
                COUNT(*) as total_reviews
            ')
            ->first();

        // تجهيز البيانات في مصفوفة
        $cachedData = [
            'overall'        => round($stats->overall_rating, 1),
            'professionalism' => round($stats->avg_professionalism, 1),
            'communication'  => round($stats->avg_communication, 1),
            'result'         => round($stats->avg_result, 1),
            'total'          => (int) $stats->total_reviews,
        ];

        // حفظ الباقة في الكاش لمدة شهر (أو يمكنك جعلها بدون وقت محدد حتى يتم التحديث يدوياً)
        Cache::put("lawyer_ratings_stats_{$lawyerId}", $cachedData, now()->addMonths(1));
    }
}
