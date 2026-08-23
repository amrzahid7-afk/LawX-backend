<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class BadWordsDetectorAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function __construct() {}

    public function instructions(): Stringable|string
    {
        return 'أنت مساعد قانوني متخصص.

مهمتك: اقرأ المستند بالكامل، ثم أرجع النتيجة بصيغة JSON فقط بالحقول التالية:
- full_text: النص الكامل المستخرج من المستند كما هو
- summary: ملخص واضح للمستند
- keywords: مصفوفة من الكلمات المفتاحية

لا تضف أي نص خارج بنية الـ JSON المطلوبة.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'is_offensive' => $schema->boolean()->required(),
            'reason'       => $schema->string(),
        ];
    }
}
