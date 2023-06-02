<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebhookTelegram extends Controller
{
    public function configTelegram(Request $request)
    {
        return view('welcome');
    }

    public function getWebhook(Request $request)
    {
        Storage::disk('local')->put('file.txt', json_encode($request->all()));

        return 'hello';
    }
}
