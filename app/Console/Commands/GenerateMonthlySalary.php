<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\LawerProfile; // تصحيح اسم الموديل
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateMonthlySalary extends Command
{
    protected $signature = 'salary:generate {--month= : رقم الشهر (1-12)} {--year= : السنة}';
    protected $description = 'حساب الرواتب الشهرية (الأساسي + المصروفات المعتمدة) وإنشاء كشوفات وفواتير الرواتب';

    public function handle()
    {
        $month = $this->option('month') ?? Carbon::now()->month;
        $year = $this->option('year') ?? Carbon::now()->year;

        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = (clone $startDate)->endOfDay(); // عدلنا endOfDay لتشمل اليوم الأخير بالكامل

        $this->info("=========================================");
        $this->info("Calculate the Salaries For Month : {$startDate->format('F Y')}");
        $this->info("=========================================");

        $lawyers = LawerProfile::with('user')
            ->whereNotNull('employed_in_office_id')
            ->whereNotNull('base_salary')
            ->get();
        // dd($lawyers->toArray());
        $generatedCount = 0;

        foreach ($lawyers as $profile) {
            $baseSalary = $profile->base_salary ?? 0;
            $totalExpenses = Expense::whereHas('task', function ($query) use ($profile) {
                $query->where('assigned_to', $profile->user_id);
            })
                ->where('status', 'approved')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->sum('amount');

            $netSalary = $baseSalary + $totalExpenses;
            // dd($netSalary);
            if ($netSalary <= 0) {
                $this->warn("Salary For :  {$profile->user->name}: IS 0 OR Negative, Skipping...");
                continue;
            }

            DB::transaction(function () use ($profile, $month, $year, $baseSalary, $totalExpenses, $netSalary, $startDate, $endDate, &$generatedCount) {

                $salaryRecord = Salary::create([
                    'lawyer_id' => $profile->user_id,
                    'month' => $month,
                    'year' => $year,
                    'base_salary' => $baseSalary,
                    'total_approved_expenses' => $totalExpenses,
                    'net_salary' => $netSalary,
                    'status' => 'generated',
                ]);
                $invoice = Invoice::create([
                    'case_id' => null,
                    'lawyer_id' =>   $profile->employedOffice->owner->user_id, //$profile->user_id, // $profile->employedOffice->owner->user_id,  // $profile->user_id,
                    'client_id' => null,
                    'total_amount' => $netSalary,
                    'status' => 'unpaid',
                    'due_date' => $endDate->copy()->addDays(5)->toDateString(),
                    'type' => 'salary',
                    'items' => json_encode([
                        ['name' => 'الراتب الأساسي', 'price' => (float) $baseSalary, 'quantity' => 1],
                        ['name' => 'مصروفات مهام معتمدة (' . $startDate->format('M') . ')', 'price' => (float) $totalExpenses, 'quantity' => 1],
                    ]),
                ]);
                $salaryRecord->update(['invoice_id' => $invoice->id]);
                $generatedCount++;

                $this->info(" Complete creatign Salary  For :  {$profile->user->name} - Salary IS  : \${$netSalary}");
            });
        }
        $this->info("=========================================");
        $this->info("Complete Mission Number Of Slaries Generated : {$generatedCount}");
        $this->info("=========================================");
        return 0;
    }
}
