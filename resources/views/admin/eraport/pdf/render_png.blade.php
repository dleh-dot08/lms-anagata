<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body { margin: 0; padding: 0; }

        .page {
            position: relative;
            width: 210mm;
            height: 297mm;
            overflow: hidden;
            font-family: DejaVu Sans, sans-serif;
        }

        .bg {
            position: absolute;
            left: 0; top: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .field {
            position: absolute;
            z-index: 2;
            box-sizing: border-box;
            display: flex;
            padding: 4px 6px;
            overflow: hidden;
            color: #111;
            word-break: break-word;
        }

        .align-left   { justify-content: flex-start; text-align: left; }
        .align-center { justify-content: center; text-align: center; }
        .align-right  { justify-content: flex-end; text-align: right; }

        .valign-top    { align-items: flex-start; }
        .valign-middle { align-items: center; }
        .valign-bottom { align-items: flex-end; }

        .qr img { width: 100%; height: 100%; display:block; }
    </style>
</head>
<body>
@php
    $fields   = data_get($fieldMap, 'fields', []);
    $bindings = data_get($fieldMap, 'dataBindings', []);
    $bgBaseW  = (float) data_get($fieldMap, 'template.background.baseSize.width', 1414);
    $bgBaseH  = (float) data_get($fieldMap, 'template.background.baseSize.height', 2000);

    // helper ambil value dari payload sesuai dataBindings
    $getBoundValue = function(string $key) use ($bindings, $payload) {
        $path = $bindings[$key] ?? null;
        if (!$path) return '';
        $v = data_get($payload, $path);
        if (is_array($v) || is_object($v)) return json_encode($v, JSON_UNESCAPED_UNICODE);
        return (string)($v ?? '');
    };
@endphp

<div class="page">
    <img class="bg" src="{{ $backgroundDataUri }}" alt="bg">

    @foreach($fields as $f)
        @php
            $key   = $f['key'] ?? '';
            $type  = strtolower($f['type'] ?? 'text');
            $style = $f['style'] ?? [];

            // posisi pakai rectPct jika ada
            if (!empty($f['rectPct'])) {
                $x = ((float)($f['rectPct']['x'] ?? 0)) * 100;
                $y = ((float)($f['rectPct']['y'] ?? 0)) * 100;
                $w = ((float)($f['rectPct']['w'] ?? 0)) * 100;
                $h = ((float)($f['rectPct']['h'] ?? 0)) * 100;
            } else {
                $rx = (float) data_get($f, 'rect.x', 0);
                $ry = (float) data_get($f, 'rect.y', 0);
                $rw = (float) data_get($f, 'rect.w', 0);
                $rh = (float) data_get($f, 'rect.h', 0);

                $x = $bgBaseW > 0 ? ($rx / $bgBaseW) * 100 : 0;
                $y = $bgBaseH > 0 ? ($ry / $bgBaseH) * 100 : 0;
                $w = $bgBaseW > 0 ? ($rw / $bgBaseW) * 100 : 0;
                $h = $bgBaseH > 0 ? ($rh / $bgBaseH) * 100 : 0;
            }

            $align  = strtolower($style['align'] ?? 'left');
            $valign = strtolower($style['valign'] ?? 'top');

            $fontSize   = (int)($style['fontSize'] ?? 16);
            $fontWeight = (int)($style['fontWeight'] ?? 400);
            $lineHeight = (float)($style['lineHeight'] ?? 1.2);

            $value = $getBoundValue($key);

            if (!empty($f['format'])) {
                $value = str_replace('{{value}}', $value, $f['format']);
            }
        @endphp

        @if($type === 'qrcode')
            @php
                // ✅ SVG QR: tidak perlu imagick
                $qrDataUri = '';
                if (!empty($value)) {
                    try {
                        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                            ->size(240)
                            ->margin(0)
                            ->errorCorrection('M')
                            ->generate($value);

                        $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
                    } catch (\Throwable $e) {
                        \Log::error('QR gen failed', ['msg' => $e->getMessage(), 'verify_url' => $value]);
                        $qrDataUri = '';
                    }
                }
            @endphp

            @if($qrDataUri)
                <div class="field qr align-center valign-middle"
                    style="left:{{ $x }}%; top:{{ $y }}%; width:{{ $w }}%; height:{{ $h }}%; padding:0;">
                    <img src="{{ $qrDataUri }}" alt="qr">
                </div>
            @endif
        @else
            @php
                $html = $type === 'textarea' ? nl2br(e($value)) : e($value);
            @endphp

            <div class="field align-{{ $align }} valign-{{ $valign }}"
                style="
                    left:{{ $x }}%;
                    top:{{ $y }}%;
                    width:{{ $w }}%;
                    height:{{ $h }}%;
                    font-size:{{ $fontSize }}px;
                    font-weight:{{ $fontWeight }};
                    line-height:{{ $lineHeight }};
                ">
                {!! $html !!}
            </div>
        @endif
    @endforeach
</div>
</body>
</html>
