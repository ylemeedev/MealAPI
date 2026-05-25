<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class MealPlanController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $client
    ) {}

    #[Route('/api/meal-plan', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $diet = $data['diet'] ?? 'balanced';

        $prompt = $this->buildPrompt($diet);

        $response = $this->client->request(
            'POST',
            'https://api.groq.com/openai/v1/chat/completions',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_ENV['GROQ_API_KEY'],
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'llama-3.1-8b-instant',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ]
                    ],
                    'temperature' => 0.4,
                    'max_tokens' => 3000,
                ]
            ]
        );

        $content = $response->toArray();


        $raw = $content['choices'][0]['message']['content'] ?? '';

        if (!$raw) {
            throw new \Exception("Empty response");
        }

        $raw = trim(str_replace(['```json', '```'], '', $raw));

        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON from AI");
        }

        return new JsonResponse($data);
    }

    private function buildPrompt(string $diet): string
    {
        return "Tu es un chef cuisinier spécialisé en planification de repas.

Génère un menu pour 7 jours (monday à sunday).

Contraintes :
- 3 repas par jour : breakfast, lunch, dinner
- chaque repas peut contenir : starter, main, dessert
- ces champs sont optionnels (peuvent être absents)
- ne jamais mettre de valeur vide ou null
- plats simples, réalistes et variés
- régime : $diet

IMPORTANT :
- retourner UNIQUEMENT un JSON valide
- aucun texte, aucune explication, aucun markdown
- toute la sortie doit être en français
- les clés des jours doivent être en anglais (monday à sunday)
- tous les noms de plats doivent être en français

Pour chaque plat :

- name_fr : nom du plat en français (affichage utilisateur)
- spoonacular_query : requête courte en anglais compatible Spoonacular

Règles spoonacular_query :
- uniquement anglais
- sans accents
- sans caractères spéciaux
- pas de mots régionaux
- formulation simple type recherche API
- si doute, garder le nom du plat simplifié

Exemples :
- \"Pâtes au pesto\" → \"pasta pesto\"
- \"Soupe de potiron\" → \"pumpkin soup\"
- \"Ratatouille provençale\" → \"ratatouille\"
- \"Quiche de légumes\" → \"vegetable quiche\"

Format exact attendu :

{
  \"monday\": {
    \"breakfast\": {
      \"starter\": \"string\",
      \"main\": \"string\",
      \"dessert\": \"string\"
    },
    \"lunch\": {
      \"starter\": \"string\",
      \"main\": \"string\",
      \"dessert\": \"string\"
    },
    \"dinner\": {
      \"starter\": \"string\",
      \"main\": \"string\",
      \"dessert\": \"string\"
    }
  }
}";
    }
}
