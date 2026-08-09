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
        return 'أنت مراقب محتوى لمنصة قانونية.
مهمتك كشف الشتائم والسباب والإهانات والكلمات البذيئة بأي شكل，
حتى لو كانت ملتوية أو مكتوبة برموز أو حروف متقطّعة أو لهجة عامية.
احكم فقط على النص المُعطى، ولا تضف أي شرح خارج المخطّط المطلوب.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'is_offensive' => $schema->boolean()->required(),
            'reason'       => $schema->string(),
        ];
    }
}
