<?php
// app/Console/Commands/SyncExternalLaws.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Legislation;
use App\Jobs\GenerateEmbeddingJob;

class SyncExternalLaws extends Command
{
    protected $signature = 'laws:sync-external';
    protected $description = 'مزامنة القوانين من External API';

    public function handle()
    {
        $this->info('بدء مزامنة القوانين من المصدر الخارجي...');

        try {
            $response = Http::withToken(config('services.laws_api.token'))
                ->timeout(60)
                ->get('https://api.external-laws.gov/v1/legislations');

            if (!$response->successful()) {
                $this->error('فشل الاتصال بالـ API: ' . $response->status());
                return 1;
            }

            $laws = $response->json()['data'] ?? [];
            $this->info("تم جلب " . count($laws) . " قانون");

            $bar = $this->output->createProgressBar(count($laws));
            $bar->start();

            foreach ($laws as $law) {
                $legislation = Legislation::updateOrCreate(
                    ['law_number' => $law['number']],
                    [
                        'title' => $law['title'],
                        'content' => $law['content'],
                        'status' => $law['status'] ?? 'active',
                        'source' => 'external_api',
                        'effective_date' => $law['effective_date'] ?? null,
                    ]
                );

                // توليد الـ Embedding في الخلفية
                GenerateEmbeddingJob::dispatch($legislation);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('✅ تمت المزامنة بنجاح!');

            return 0;
        } catch (\Exception $e) {
            $this->error('حدث خطأ: ' . $e->getMessage());
            return 1;
        }
    }
}
