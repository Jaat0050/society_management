<?php

namespace App\Http\Controllers;

use App\Models\VisitorEntry;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VisitorQrController extends Controller
{
    public function show(string $token)
    {
        $entry = VisitorEntry::where('qr_token', $token)->firstOrFail();

        return QrCode::format('png')
            ->size(280)
            ->margin(2)
            ->generate($entry->approvalUrl());
    }
}
