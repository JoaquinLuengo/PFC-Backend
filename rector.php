<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\FunctionLike\MixedTypeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->phpVersion(\Rector\Core\ValueObject\PhpVersion::PHP_81);
    $rectorConfig->rule(MixedTypeRector::class);
};
