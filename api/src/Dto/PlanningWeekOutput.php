<?php

namespace App\Dto;

final class PlanningWeekOutput
{
    public function __construct(
        public array $week = []
    ) {}
}
