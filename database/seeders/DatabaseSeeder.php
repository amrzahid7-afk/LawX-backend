<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,              // أدوار (يدوي - مرجعي)
            LegalCategoriesSeeder::class,   // تخصصات (يدوي - مرجعي)
            UserSeeder::class,              // syndicate بس
        ]);
    }
    // public function run(): void
    // {
    //     // 1. البيانات المرجعية الأساسية (لا تعتمد على شيء)
    //     $this->call([
    //         RoleSeeder::class,                  // إذا كنت تستخدمه
    //         // LegalCategoriesSeeder::class,
    //         // LegislationsTableSeeder::class,
    //         DocumentsTableSeeder::class,

    //         SmartTemplatesTableSeeder::class,
    //     ]);

    //     // 2. المستخدمون والمحامون والمكاتب
    //     $this->call([
    //         UsersTableSeeder::class,
    //         LawyerProfilesTableSeeder::class,
    //         OfficesTableSeeder::class,
    //         UserLegalCategoriesTableSeeder::class,
    //     ]);

    //     // 3. القضايا والطلبات القانونية (تعتمد على المستخدمين)
    //     $this->call([
    //         CasesTableSeeder::class,
    //         CaseClientsTableSeeder::class,
    //         CaseTeamsTableSeeder::class,
    //         CasePaymentMilestonesTableSeeder::class,
    //         OpponentsTableSeeder::class,
    //         LegalRequestsTableSeeder::class,
    //         ProposalsTableSeeder::class,
    //     ]);

    //     // 4. المهام والمصاريف (تعتمد على القضايا)
    //     $this->call([
    //         TasksTableSeeder::class,
    //         ExpensesTableSeeder::class,
    //     ]);

    //     // 5. الفواتير والمدفوعات والرواتب
    //     $this->call([
    //         InvoicesTableSeeder::class,
    //         PaymentsTableSeeder::class,
    //         SalariesTableSeeder::class,
    //     ]);

    //     // 6. المستندات وطلبات التوقيع (تعتمد على القضايا والفواتير والطلبات)
    //     $this->call([
    //         SignatureRequestsTableSeeder::class,
    //         SignersTableSeeder::class,
    //     ]);

    //     // 7. التقييمات والإشعارات والجلسات والرموز
    //     $this->call([
    //         ReviewsTableSeeder::class,
    //         NotificationsTableSeeder::class,
    //         SessionsTableSeeder::class,
    //         PersonalAccessTokensTableSeeder::class,
    //     ]);
    // }
}
