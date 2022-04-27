<?php

namespace App\Http\Controllers;

use App\Exceptions\TranscriptionException;
use App\Exceptions\ValidationException;
use App\Http\Validators\UploadValidator;
use App\Models\Transcription;
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
        $time = date('Y-m-d H:i:s');
        try {
            $this->validator->validateUpload($request->json('content'));
        } catch (ValidationException $e) {
            return response($e->getMessage(), 400);
        }

        $filename = $request->json('filename') . $time . '.flac';
        Storage::put($filename, base64_decode($request->json('content')));
        $transcription = Transcription::create([
            'filename' => $filename,
            'submitted' => $time,
            'status' => 'Started',
        ]);

        try {
            $client = new GoogleClient();
            $transcription->transcription = $client->transcribe(Storage::path('test.flac'));
            $transcription->status = 'Complete';
            $transcription->save();
        } catch (TranscriptionException $e) {
            $transcription->status = 'Failed';
            $transcription->error = $e->getMessage();
            $transcription->save();
            return response($e->getMessage(), 502);
        }

        return response($transcription->id, 203);
    }
}
