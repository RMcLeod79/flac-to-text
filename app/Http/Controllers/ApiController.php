<?php

namespace App\Http\Controllers;

use App\Exceptions\TranscriptionException;
use App\Exceptions\ValidationException;
use App\Http\Validators\UploadValidator;
use App\Services\GoogleClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    public function __construct(private UploadValidator $validator)
    {
    }

    public function upload(Request $request): Response
    {
        try {
            $this->validator->validateUpload($request->json('content'));
        } catch (ValidationException $e) {
            return response($e->getMessage(), 400);
        }

        Storage::put('test.flac', base64_decode($request->json('content')));

        try {
            $client = new GoogleClient();
            $transcription = $client->transcribe(Storage::path('test.flac'));
        } catch (TranscriptionException $e) {
            return response($e->getMessage(), 502);
        }

        return response($transcription);
    }
}
