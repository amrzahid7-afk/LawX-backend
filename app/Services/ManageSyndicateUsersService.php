<?php

namespace App\Services;

use App\Models\LawerProfile;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManageSyndicateUsersService
{

    public function createLawyer(array $data)
    {
        $userId = auth('sanctum')->id();
        if (!$userId) {
            throw new \Exception('Unauthorized. Please log in first.', 401);
        }
        DB::beginTransaction();
        $lawer_profile = LawerProfile::create([
            'national_id'           => $data['national_id'],
            // 'national_Id_data'      => $data['national_id_data'],
            'full_name'             => $data['full_name'],
            'birth_date'            => $data['birth_date'],
            'birth_place'           => $data['birth_place'],
            'father_name'           => $data['father_name'],
            'mother_name'           => $data['mother_name'],
            'mother_family_name'    => $data['mother_family_name'],
            'civil_registry_number' => $data['civil_registry_number'],
            'syndicate_card_number' => $data['syndicate_card_number'],
            'syndicate_branch'      => $data['syndicate_branch'],
            'lawyer_state'          => $data['lawyer_state'],
            'created_by'            => $userId,
        ]);
        if ($data['lawyer_state'] === 'licensed') {
            Office::create([
                'name' => $data['full_name'] . "'s Office",
                'owner_lawyer_profile_id' => $lawer_profile->id,
                'status' => 'suspended',
            ]);
        }
        DB::commit();
        return true;
    }
    public function getLawyers($data, $myOfficeId = null)
    {
        $lawyers = LawerProfile::query()
            ->with('user', 'ownedOffice') // لا داعي لجلب employedOffices لأننا نستثنيهم
            ->when(isset($data['lawyer_state']), fn($query) => $query->where('lawyer_state', $data['lawyer_state']))
            ->when(isset($data['is_suspended']), fn($query) => $query->where('is_suspended', filter_var($data['is_suspended'], FILTER_VALIDATE_BOOLEAN)))
            ->when(isset($data['search_fullname']), fn($query) => $query->where('full_name', 'like', "%{$data['search_fullname']}%"))
            ->when(isset($data['search_syndicate_card_number']), fn($query) => $query->where('syndicate_card_number', 'like', "%{$data['search_syndicate_card_number']}%"))
            ->when(isset($data['search_national_id']), fn($query) => $query->where('national_id', 'like', "%{$data['search_national_id']}%"))
            // 1. استثناء المحامين الذين يعملون بالفعل في أي مكتب (حتى لو ليس مكتبك)
            ->whereNull('employed_in_office_id')
            // 2. استثناء المحامين الذين يملكون مكتباً "نشطاً" (لا يمكنك توظيف صاحب مكتب آخر)
            ->whereDoesntHave('ownedOffice', function ($query) {
                $query->where('status', 'active');
            })
            // 3. استثناء المحامين الذين لديهم دعوة "معلقة" لمكتبي أنا تحديداً
            ->when($myOfficeId, function ($query) use ($myOfficeId) {
                $query->whereDoesntHave('officeInvitations', function ($q) use ($myOfficeId) {
                    $q->where('office_id', $myOfficeId)
                        ->where('status', 'pending');
                });
            })
            ->latest()
            ->paginate($data['per_page'] ?? 20);

        return $lawyers;
    }
    // public function getLawyers($data)
    // {
    //     $lawyers = LawerProfile::query()
    //         ->with('user', 'ownedOffice', 'employedOffices')
    //         ->when(isset($data['lawyer_state']), fn($query) => $query->where('lawyer_state', $data['lawyer_state']))
    //         ->when(isset($data['is_suspended']), fn($query) => $query->where('is_suspended', filter_var($data['is_suspended'], FILTER_VALIDATE_BOOLEAN)))
    //         ->when(isset($data['search_fullname']), fn($query) => $query->where('full_name', 'like', "%{$data['search_fullname']}%"))
    //         ->when(isset($data['search_syndicate_card_number']), fn($query) => $query->where('syndicate_card_number', 'like', "%{$data['search_syndicate_card_number']}%"))
    //         ->when(isset($data['search_national_id']), fn($query) => $query->where('national_id', 'like', "%{$data['search_national_id']}%"))
    //         ->latest()
    //         ->paginate($data['per_page'] ?? 20);
    //     return $lawyers;
    // }
    public function toggleSuspension($lawyer_id): void
    {
        $lawyer = LawerProfile::findOrFail($lawyer_id);
        DB::beginTransaction();
        $lawyer->is_suspended = !$lawyer->is_suspended;
        $lawyer->save();
        DB::commit();
    }
    public function promoteTrainee($lawyer_id): void
    {
        $lawyer = LawerProfile::find($lawyer_id);

        if (! $lawyer) {
            throw new \Exception('المحامي الذي أدخلته غير موجود في النظام!', 404);
        }

        if ($lawyer->lawyer_state !== 'trainee') {
            throw new \Exception('لا يمكن ترقية هذا المحامي لأنه ليس في مرحلة التدريب حالياً (مستقل بالفعل).', 400);
        }

        if (! is_null($lawyer->trainee_approved_independent_at)) {
            throw new \Exception('تمت ترقية هذا المحامي مسبقاً.', 409);
        }

        if (now()->lt($lawyer->created_at->addYears(2))) {
            $targetDate = $lawyer->created_at->addYears(2);
            $diff = now()->diff($targetDate);
            $timeParts = [];

            if ($diff->y > 0) {
                $timeParts[] = $diff->y . ($diff->y == 1 ? ' سنة' : ($diff->y == 2 ? ' سنتين' : ' سنوات'));
            }
            if ($diff->m > 0) {
                $timeParts[] = $diff->m . ($diff->m == 1 ? ' شهر' : ($diff->m == 2 ? ' شهرين' : ' أشهر'));
            }
            if ($diff->d > 0) {
                $timeParts[] = $diff->d . ($diff->d == 1 ? ' يوم' : ($diff->d == 2 ? ' يومين' : ' أيام'));
            }

            $formattedRemainingTime = implode(' و ', $timeParts);
            throw new \Exception("لا يمكن ترقية المحامي؛ لم يكمل فترة التدريب القانونية (سنتين) بعد. متبقي: {$formattedRemainingTime}.", 422);
        }

        DB::beginTransaction();
        try {
            $lawyer->update([
                'lawyer_state' => 'independent',
                'trainee_approved_independent_at' => now(),
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th; // نترك الاستثناء غير المتوقع ليتعامل معه الـ Controller
        }
    }
    public function updateLawyer(string $lawyer_id, array $data): bool
    {
        $lawyer = LawerProfile::findOrFail($lawyer_id);
        DB::beginTransaction();
        try {
            if (isset($data['lawyer_state']) && $data['lawyer_state'] === 'licensed' && $lawyer->lawyer_state !== 'licensed') {
                if (!$lawyer->ownedOffice()->exists()) {
                    Office::create([
                        'name' => ($data['full_name'] ?? $lawyer->full_name) . "'s Office",
                        'owner_lawyer_profile_id' => $lawyer->id,
                        'status' => 'suspended',
                    ]);
                }
            }
            DB::table('lawyer_profiles')
                ->where('id', $lawyer_id)
                ->update(array_merge($data, ['updated_at' => now()]));
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function deleteLawyer(string $lawyer_id): bool
    {
        $lawyer = LawerProfile::find($lawyer_id);

        if (!$lawyer) {
            throw new \Exception('المحامي غير موجود بالنظام أو تم حذفه بالفعل!', 404);
        }

        DB::beginTransaction();
        try {
            // ستقوم بحذف المحامي حذفاً ناعماً (Soft Delete) بسبب استخدام التريت في الموديل
            $lawyer->delete();

            // اختياري: إذا أردت تعليق أو حذف المكتب المرتبط به تلقائياً عند حذفه
            if ($lawyer->ownedOffice) {
                $lawyer->ownedOffice()->update(['status' => 'suspended']);
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    // public function promoteTrainee($lawyer_id): void
    // {
    //     $lawyer = LawerProfile::find($lawyer_id);

    //     if (! $lawyer) {
    //         throw new \Exception('المحامي الذي أدخلته غير موجود في النظام!', 404);
    //     }

    //     if ($lawyer->lawyer_state !== 'trainee') {
    //         throw new \Exception('لا يمكن ترقية هذا المحامي لأنه ليس في مرحلة التدريب حالياً (مستقل بالفعل).', 400);
    //     }

    //     if (! is_null($lawyer->trainee_approved_independent_at)) {
    //         throw new \Exception('تمت ترقية هذا المحامي مسبقاً.', 409);
    //     }

    //     if (now()->lt($lawyer->created_at->addYears(2))) {
    //         $targetDate = $lawyer->created_at->addYears(2);
    //         $diff = now()->diff($targetDate);
    //         $timeParts = [];
    //         if ($diff->y > 0) {
    //             $timeParts[] = $diff->y . ($diff->y == 1 ? ' سنة' : ($diff->y == 2 ? ' سنتين' : ' سنوات'));
    //         }
    //         if ($diff->m > 0) {
    //             $timeParts[] = $diff->m . ($diff->m == 1 ? ' شهر' : ($diff->m == 2 ? ' شهرين' : ' أشهر'));
    //         }
    //         if ($diff->d > 0) {
    //             $timeParts[] = $diff->d . ($diff->d == 1 ? ' يوم' : ($diff->d == 2 ? ' يومين' : ' أيام'));
    //         }
    //         $formattedRemainingTime = implode(' و ', $timeParts);
    //         throw new \Exception("لا يمكن ترقية المحامي؛ لم يكمل فترة التدريب القانونية (سنتين) بعد. متبقي: {$formattedRemainingTime}.", 422);
    //     }
    //     DB::beginTransaction();
    //     $lawyer->update([
    //         'lawyer_state' => 'independent',
    //         'trainee_approved_independent_at' => now(),
    //     ]);
    //     DB::commit();
    // }
}
