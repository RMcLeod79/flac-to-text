<?php

namespace App\Http\Validators;

use App\Exceptions\ValidationException;

class UploadValidator
{
    /**
     * @throws ValidationException
     */
    public function validateUpload(?string $data): void
    {
        if (! $this->validateNotEmpty($data)) {
            throw new ValidationException('No content provided');
        }

        if (! $this->validateBase64($data)) {
            throw new ValidationException('Improperly encoded Base64');
        }

        if (! $this->validateMime($data)) {
            throw new ValidationException('File is not a valid flac file');
        }
    }

    private function validateNotEmpty(?string $data): bool
    {
        return ! empty($data);
    }

    private function validateBase64(string $data): bool
    {
        return (base64_encode(base64_decode($data, true))) === $data;
    }

    private function validateMime(string $data): bool
    {
        $decoded = base64_decode($data);
        $fh = finfo_open();
        return finfo_buffer($fh, $decoded, FILEINFO_MIME_TYPE) === 'audio/flac';
    }
}
