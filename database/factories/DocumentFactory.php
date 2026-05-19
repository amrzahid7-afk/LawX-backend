<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    protected $customFolder = 'documents';

    public function folder($path)
    {
        $this->customFolder = $path;
        return $this;
    }
    public function definition(): array
    {
        $fileName = Str::uuid() . '.pdf';

        $folderPath = $this->customFolder;
        $fullPath = $folderPath . '/' . $fileName;
        $pdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R >>\nendobj\n4 0 obj\n<< /Length 44 >>\nstream\nBT /F1 12 Tf 100 700 Td (Test Document) Tj ET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000206 00000 n \ntrailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n311\n%%EOF";
        Storage::disk('public')->put($fullPath, $pdfContent);
        $uploader = User::inRandomOrder()->first();
        return [
            'id' => Str::uuid(),
            'case_id' => null,
            'file_path' => $fullPath,
            'type' => 'pdf',
            'uploaded_by' => $uploader ? $uploader->id : null,
            'legal_request_id' => null,
            'proposal_id' => null,
        ];
    }
}
