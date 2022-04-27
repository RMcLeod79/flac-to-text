<?php

namespace App\Jobs;

use App\Exceptions\TranscriptionException;
use App\Models\Transcription;
use App\Services\GoogleClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class Transcribe implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private Transcription $transcription)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $client = new GoogleClient();
            $this->transcription->transcription = $client->transcribe(Storage::path($this->transcription->filename));
            $this->transcription->status = 'Complete';
            $this->transcription->save();
        } catch (TranscriptionException $e) {
            $this->transcription->status = 'Failed';
            $this->transcription->error = $e->getMessage();
            $this->transcription->save();
        }
    }
}
