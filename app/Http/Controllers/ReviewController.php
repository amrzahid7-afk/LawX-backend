<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\CaseModel;
use App\Models\LegalCase; // غير الاسم حسب مشروعك
use App\Models\Review;
use App\Services\RatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    protected $ratingService;

    // حقن الـ Service في الـ Controller (Dependency Injection)
    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * تخزين تقييم جديد لقضية مغلقة
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        // get validated data 
        $validaedData = $request->validated();
        $case = CaseModel::find($validaedData['case_id']);
        // validate permissions 
        if (Gate::denies('give_Rate', [Review::class, $case])) {
            return sendError("Unauthorized , You do not have permission to give a rating for this case.", [], 403);
        }
        // call service 
        $review = $this->ratingService->createReview($request->validated(), $case);
        // sedn resposne
        return sendResponse('تم إضافة التقييم بنجاح، شكراً لمشاركتك رأيك.', 'Success Add Review', 201);
    }
}
