<?php

namespace App\Http\Controllers;

use App\Models\Eraport;
use Illuminate\Support\Facades\Storage;

class EraportVerifyController extends Controller
{
    public function __invoke(string $token)
    {
        $eraport = Eraport::query()
            ->where('verify_token', $token)
            ->firstOrFail();

        $snap = is_array($eraport->snapshot_json)
            ? $eraport->snapshot_json
            : (json_decode($eraport->snapshot_json, true) ?: []);

        $isValid = ($eraport->status === 'PUBLISHED') || !empty($eraport->published_at);

        $pdfUrl = null;
        if (!empty($eraport->pdf_path) && Storage::disk('public')->exists($eraport->pdf_path)) {
            $pdfUrl = Storage::disk('public')->url($eraport->pdf_path);
        }

        // OPTIONAL: kalau kolom ini ada
        $certificateUrl = null;
        if (!empty($eraport->certificate_path) && Storage::disk('public')->exists($eraport->certificate_path)) {
            $certificateUrl = Storage::disk('public')->url($eraport->certificate_path);
        }

        return view('public.eraport.verify', compact(
            'eraport', 'snap', 'isValid', 'pdfUrl', 'certificateUrl'
        ));
    }
}
