<?php

namespace App\Ai\Agents;

use App\Ai\Tools\RetrievePreviousTranscripts;
use App\Models\History;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class DocumentAnalyzerAgent implements Agent, HasStructuredOutput
{
    use Promptable;
    public function __construct() {}

    public function instructions(): Stringable|string
    {
        return 'أنت مساعد قانوني.

اقرأ الملف بالكامل.

استخرج النص كما هو.

لا تضف أي معلومات.

بعدها أنشئ ملخصًا.

ثم استخرج الكلمات المفتاحية.';
    }
    // public function messages(): iterable
    // {
    //     return History::where('user_id', $this->user->id)
    //         ->latest()
    //         ->limit(50)
    //         ->get()
    //         ->reverse()
    //         ->map(function ($message) {
    //             return new Message($message->role, $message->content);
    //         })->all();
    // }
    // public function tools(): iterable
    // {
    //     return [
    //         new RetrievePreviousTranscripts,
    //     ];
    // }

    public function schema(JsonSchema $schema): array
    {
        return [
            'full_text' => $schema->string()->required(),
            'summary' => $schema->string()->required(),
            'keywords' => $schema->array()->required(),
        ];
    }
}
