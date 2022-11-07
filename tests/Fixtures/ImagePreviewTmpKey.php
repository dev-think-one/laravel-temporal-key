<?php

namespace TemporalKey\Tests\Fixtures;

use TemporalKey\Manager\TmpKey;

class ImagePreviewTmpKey extends TmpKey
{
    public static string $type             = 'image-preview';
    public static int $defaultValidSeconds = 60 * 60;
    public static int $defaultUsageMax     = 2;
}
