<?php

namespace App\Services;

use App\Exceptions\TranscriptionException;
use Flac;
use Google\ApiCore\ValidationException;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\SpeechClient;
use Illuminate\Support\Facades\Storage;

class GoogleClient
{
    private SpeechClient $client;
    private RecognitionConfig $config;
    private RecognitionAudio $audio;
    private Flac $flac;

    public function __construct()
    {
        try {
            $this->client = new SpeechClient([
                'credentials' => Storage::path('google-key.json')
            ]);
        } catch (ValidationException) {
            throw new TranscriptionException('Unable to authenticate with Google');
        }

        $this->audio = new RecognitionAudio();
    }

    public function transcribe(string $file): string
    {
        $this->setupAudioClient($file);

        try {
            $operation = $this->client->longRunningRecognize($this->config, $this->audio);
            $operation->pollUntilComplete();
        } catch (\Exception $e) {
            $this->client->close();
            throw new TranscriptionException($e->getMessage());
        }

        if ($operation->operationSucceeded()) {
            $response = $operation->getResult();

            foreach ($response->getResults() as $result) {
                $alternatives = $result->getAlternatives();
                $mostLikely = $alternatives[0];
                $transcript = $mostLikely->getTranscript();
            }
        } else {
            $this->client->close();
            throw new TranscriptionException('Google failed to transcribe the file');
        }

        $this->client->close();

        return $transcript;
    }

    private function setupAudioClient(string $file): void
    {
        try {
            $flac = new Flac($file);
        } catch (\ErrorException) {
            throw new TranscriptionException('Unable to read the file');
        }

        $this->config = new RecognitionConfig();
        $this->config->setEncoding(RecognitionConfig\AudioEncoding::FLAC);
        $this->config->setSampleRateHertz($flac->streamSampleRate);
        $this->config->setAudioChannelCount($flac->streamChannels);
        $this->config->setLanguageCode('en_US');

        $this->audio->setContent(file_get_contents($file));
    }
}
