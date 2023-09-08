<?php

namespace App\Models\Traits;

use App\Services\KeyCaseConverterService;

/**
 * Allows models to serialize as camel case attributes.
 */
trait CamelCaseSerializable
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        return (new KeyCaseConverterService())->convert(KeyCaseConverterService::CASE_CAMEL, $array);
    }
}
