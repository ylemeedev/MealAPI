<?php

namespace App\Enum;

enum Preference: string
{
    case VEGETARIAN = 'vegetarian'; // Végétarien
    case VEGAN = 'vegan'; // Vegan
    case PESCATARIAN = 'pescatarian'; // Pesco-végétarien

    case NO_PORK = 'no_pork'; // Sans porc
    case NO_BEEF = 'no_beef'; // Sans bœuf
    case NO_CHICKEN = 'no_chicken'; // Sans poulet
    case NO_FISH = 'no_fish'; // Sans poisson

    case NO_ALCOHOL = 'no_alcohol'; // Sans alcool
    case NO_LACTOSE = 'no_lactose'; // Sans lactose
    case NO_GLUTEN = 'no_gluten'; // Sans gluten
    case NO_EGGS = 'no_eggs'; // Sans œufs

    case HIGH_PROTEIN = 'high_protein'; // Riche en protéines
    case LOW_CARB = 'low_carb'; // Faible en glucides
    case LOW_CALORIE = 'low_calorie'; // Faible en calories
    case BALANCED = 'balanced'; // Équilibré

    case QUICK_MEALS = 'quick_meals'; // Repas rapides
    case BATCH_COOKING = 'batch_cooking'; // Cuisine en lot (meal prep)

    case COMFORT_FOOD = 'comfort_food'; // Cuisine réconfortante
    case HEALTHY = 'healthy'; // Cuisine saine
    case GOURMET = 'gourmet'; // Cuisine élaborée

    case SEASONAL = 'seasonal'; // Produits de saison
}