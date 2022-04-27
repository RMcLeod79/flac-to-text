<?php

namespace App\Services;

use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\SpeechClient;
use Illuminate\Support\Facades\Storage;

class GoogleClient
{
    private SpeechClient $client;
    private RecognitionConfig $config;
    private RecognitionAudio $audio;

    public function __construct()
    {
        $this->client = new SpeechClient([
            'credentials' => Storage::path('google-key.json')
        ]);

        $this->audio = new RecognitionAudio();
    }

    public function transcribe(string $file): string
    {
        $this->setupAudioClient($file);

        $operation = $this->client->longRunningRecognize($this->config, $this->audio);
        $operation->pollUntilComplete();

        if ($operation->operationSucceeded()) {
            $response = $operation->getResult();

            foreach ($response->getResults() as $result) {
                $alternatives = $result->getAlternatives();
                $mostLikely = $alternatives[0];
                $transcript = $mostLikely->getTranscript();
            }
        } else {
            $transcript = 'failed to transcribe';
        }

        $this->client->close();

        return $transcript;
    }

    private function setupAudioClient(string $file): void
    {
        $this->config = new RecognitionConfig();
        $this->config->setEncoding(RecognitionConfig\AudioEncoding::FLAC);
        $this->config->setSampleRateHertz(44100);
        $this->config->setAudioChannelCount(2);
        $this->config->setLanguageCode('en_US');

        $this->audio->setContent(file_get_contents($file));
    }
}
