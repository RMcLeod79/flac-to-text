<?php

namespace App\Http\Controllers;

use App\Exceptions\ValidationException;
use App\Http\Validators\UploadValidator;
use Illuminate\Http\Request;

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
    }
}
