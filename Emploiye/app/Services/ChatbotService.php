<?php

namespace App\Services;

use App\Models\JobOffer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ChatbotService
{
    public function ask(string $message): string
    {
        $apiKey = config('services.openrouter.key');

        if (empty($apiKey)) {
            throw new \Exception('OPENROUTER_API_KEY introuvable.');
        }

        // Offres d'emploi en cache pendant 5 minutes
        $jobs = Cache::remember('chatbot_open_jobs', 300, function () {
            return JobOffer::where('status', 'open')
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
        });

        $jobsText = "";

        if ($jobs->count()) {
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

            Ton rôle est d'aider les visiteurs du site.

            Tu réponds UNIQUEMENT aux questions concernant :

            - DataXpress
            - L'entreprise
            - Les services
            - Les employés
            - Les carrières
            - Les stages
            - Les offres d'emploi
            - Le recrutement
            - Les contacts

            Informations importantes :

            - Si le visiteur demande les offres d'emploi, utilise UNIQUEMENT la liste ci-dessous.
            - Si aucune offre n'existe, indique qu'il n'y a actuellement aucune offre ouverte.
            - Si le visiteur demande comment postuler, indique qu'il doit consulter la page Carrières.
            - Si le visiteur demande les coordonnées, invite-le à consulter la page Contact.
            - Si la question est hors sujet (football, cuisine, politique, jeux vidéo...), réponds uniquement :

            Je peux uniquement répondre aux questions concernant DataXpress, les carrières et le recrutement.

            Réponds toujours en français.

            Voici les offres actuellement disponibles :

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
            ]);

        if (!$response->successful()) {
            throw new \Exception($response->body());
        }

        return data_get(
            $response->json(),
            'choices.0.message.content',
            "Désolé, je n'ai pas pu générer une réponse."
        );
    }
}