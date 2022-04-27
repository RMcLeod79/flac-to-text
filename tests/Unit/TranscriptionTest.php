<?php

namespace Tests\Unit;

use App\Exceptions\TranscriptionException;
use App\Services\GoogleClient;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TranscriptionTest extends TestCase
{
    /** @test */
    public function an_exception_is_thrown_if_authentication_fails()
    {
        Storage::move('google-key.json', 'tmp.json');
        $this->expectException(TranscriptionException::class);
        $client = new GoogleClient();
    }

    /** @test */
    public function an_exception_is_thrown_if_transcription_fails()
    {
        Storage::put('fake.flac', 'test');
        $this->expectException(TranscriptionException::class);
        $client = new GoogleClient();
        $client->transcribe(Storage::path('fake.flac'));
    }

    /** @test */
    public function a_string_is_returned_if_successful()
    {
        $client = new GoogleClient();
        $text = $client->transcribe(Storage::path('test.flac'));
        $this->assertIsString($text);
    }

    protected function tearDown(): void
    {
        if (Storage::exists('tmp.json')) {
            Storage::move('tmp.json', 'google-key.json');
        }

        if (Storage::exists('fake.flac')) {
            Storage::delete('fake.flac');
        }
    }
}
