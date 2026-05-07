<?php

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class CurrentUser
{
    public function __construct(
        public string $mode = 'owner' // owner | admin | none
    ) {}
}
