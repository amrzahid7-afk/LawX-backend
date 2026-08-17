<?php

namespace App\Http\Controllers;

use App\Services\OfficeManagerFinancialService;
use App\Services\LawyerFinancialService;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class FinancialController extends Controller
{
    // لا يوجد أي constructor هنا


    public function getFinancialReport(Request $request)
    {
        try {
            $validated_data = Validator::make($request->all(), [
                'from' => 'required|date',
                'to' => 'required|date|after_or_equal:from'
            ], [
                'from.required' => 'حقل "من" مطلوب.',
                'from.date' => 'حقل "من" يجب أن يكون تاريخًا صالحًا.',
                'to.required' => 'حقل "إلى" مطلوب.',
                'to.date' => 'حقل "إلى" يجب أن يكون تاريخًا صالحًا.',
                'to.after_or_equal' => 'حقل "إلى" يجب أن يكون بعد أو يساوي حقل "من".',
            ]);

            if ($validated_data->fails()) {
                return sendError('بيانات المدخلات غير صحيحة', $validated_data->errors()->toArray(), 422);
            }

            if (Gate::denies('view-financial-report')) {
                return sendError('Unauthorized: You do not have permission to view financial reports.', [], 403);
            }

            // نحدد الخدمة هنا، في وقت متأخر، بعد التأكد من هوية المستخدم بواسطة الميدل وير
            $user = $request->user();

            if ($user->lawyerProfile && $user->lawyerProfile->ownedOffice) {
                $service = new OfficeManagerFinancialService();
            } else {
                $service = new LawyerFinancialService();
            }

            $report = $service->getFinancialReport($user, $request->from, $request->to);

            if (isset($report['error'])) {
                return sendError($report['error'], [], 403);
            }

            return sendResponse($report, 'Financial report retrieved successfully', 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }

    public function getMyMonthlySalary(Request $request, $month, $year)
    {
        try {
            if (Gate::denies('view-salary-slip')) {
                return sendError('Unauthorized: Only lawyers can view salary slips.', [], 403);
            }
            $salary = Salary::where('lawyer_id', $request->user()->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();
            if (!$salary) {
                return sendError('No salary record found for this period', [], 404);
            }
            return sendResponse([
                'month' => $salary->month,
                'year' => $salary->year,
                'base_salary' => $salary->base_salary,
                'total_approved_expenses' => $salary->total_approved_expenses,
                'net_salary' => $salary->net_salary,
                'status' => $salary->status,
            ], 'Salary slip retrieved successfully', 200);
        } catch (\Throwable $th) {
            return sendError('Internal Server Error', ['error' => $th->getMessage()], 500);
        }
    }
}
