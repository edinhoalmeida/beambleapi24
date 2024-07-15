<?php

namespace OpenAI\Enums\Moderations;

use OpenAI\Enums\BaseEnum;

class Category extends BaseEnum
{
    const Hate = 'hate';
    const HateThreatening = 'hate/threatening';
    const SelfHarm = 'self-harm';
    const Sexual = 'sexual';
    const SexualMinors = 'sexual/minors';
    const Violence = 'violence';
    const ViolenceGraphic = 'violence/graphic';
}
