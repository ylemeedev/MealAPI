<?php

namespace App\Enum;

enum RecipeType: string
{
    case STARTER = 'starter';
    case MAIN = 'main';
    case DESSERT = 'dessert';
}