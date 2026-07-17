<?php

namespace App\Services;

use App\Models\JobOffer;
use Illuminate\Support\Facades\Http;

class ChatbotService
{
    public function ask(string $message): string
    {
        $apiKey = config('services.openrouter.key');

        if (empty($apiKey)) {
            throw new \Exception('OPENROUTER_API_KEY introuvable.');
        }

        // Récupération des offres ouvertes
        $jobs = JobOffer::where('status', 'open')
            ->select(
                'title',
                'description',
                'requirements',
                'location',
                'contract_type',
                'salary_min',
                'salary_max',
                'deadline'
            )
            ->get();

        $jobsText = "";

        if ($jobs->isNotEmpty()) {

            foreach ($jobs as $job) {

                $jobsText .= "
Titre : {$job->title}
Description : {$job->description}
Compétences : {$job->requirements}
Localisation : {$job->location}
Contrat : {$job->contract_type}
Salaire : {$job->salary_min} - {$job->salary_max}
Date limite : {$job->deadline}

";
            }

        } else {

            $jobsText = "Aucune offre d'emploi ouverte actuellement.";

        }

        $systemPrompt = "
Tu es le chatbot officiel de DataXpress.

Tu aides uniquement les visiteurs concernant :

- DataXpress
- Les services
- Les employés
- Les carrières
- Les stages
- Les offres d'emploi
- Le recrutement
- Les contacts

Règles :

- Réponds toujours en français.
- Sois professionnel.
- Si la question concerne les offres d'emploi, utilise uniquement la liste ci-dessous.
- Si aucune offre n'existe, indique qu'il n'y a actuellement aucune offre ouverte.
- Si on demande comment postuler, indique d'aller sur la page Carrières.
- Si on demande les coordonnées, indique d'aller sur la page Contact.
- Si la question est hors sujet (football, cuisine, politique, etc.), réponds :

Je peux uniquement répondre aux questions concernant DataXpress, ses services, ses carrières et son recrutement.

Offres actuellement disponibles :

{$jobsText}
";

        $response = Http::timeout(30)
            ->retry(2, 1000)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])
            ->post('https://openrouter.ai/api/v1/chat/completions', [

                'model' => 'tencent/hy3:free',

                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $message,
                    ],
                ],

                'temperature' => 0.4,
                'max_tokens' => 500,
            ]);

       if (!$response->successful()) {

            return "Le chatbot est actuellement très sollicité.
            Veuillez réessayer dans quelques instants.";
        }

        return data_get(
            $response->json(),
            'choices.0.message.content',
            "Désolé, je n'ai pas pu générer une réponse."
        );
    }
}