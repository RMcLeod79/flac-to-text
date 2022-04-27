<?php

namespace App\Http\Controllers;

use App\Exceptions\ValidationException;
use App\Http\Validators\UploadValidator;
use App\Services\GoogleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    public function __construct(private UploadValidator $validator)
    {
    }

    public function upload(Request $request)
    {
        try {
            $this->validator->validateUpload($request->json('content'));
        } catch (ValidationException $e) {
            return response($e->getMessage(), 400);
        }

        Storage::put('test.flac', base64_decode($request->json('content')));
        $client = new GoogleClient();
        $transcription = $client->transcribe(Storage::path('test.flac'));

        return response($transcription);
    }
}
