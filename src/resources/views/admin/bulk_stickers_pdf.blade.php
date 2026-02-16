<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            src: url('{{ public_path("fonts/THSarabunNew.ttf") }}') format('truetype');
            font-weight: bold; font-style: normal;
        }
        @page { size: A4; margin: 0.3cm 1.5cm; }
        body { font-family: 'THSarabunNew', sans-serif; margin: 0; padding: 0; }
        .sticker-wrapper { position: absolute; width: 8.0cm; height: 4.6cm; }
        .bg-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; }
        .no-text { position: absolute; left: 2.15cm; top: 1.15cm; font-size: 24pt; font-weight: bold; color: #000; line-height: 1; }
        .qr-box { position: absolute; right: 0.6cm; top: 1.35cm; width: 2.6cm; text-align: center; }
        .qr-img { width: 2.6cm; height: 2.6cm; }
        .footer-url { position: absolute; left: 1.05cm; bottom: 0.2cm; font-size: 10pt; font-weight: bold; color: #000; line-height: 1; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    @php 
        $selectedBg = isset($bg_number) ? $bg_number : '1';
        $imagePath = public_path('images/sticker_bg' . $selectedBg . '.jpg');
        $base64Image = '';
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $base64Image = 'data:image/jpeg;base64,' . $imageData;
        }
        $chunks = array_chunk($stickers, 12);
    @endphp

    @foreach($chunks as $pageItems)
        <div style="position: relative; width: 100%; height: 100%;">
            @foreach($pageItems as $index => $item)
                @php
                    $col = $index % 2; $row = floor($index / 2);
                    $left = $col * 8.3; $top = $row * 4.9;
                    $formattedNo = str_pad($item['number'], 4, '0', STR_PAD_LEFT);
                    
                    // ✅ สยบตัวแดงถาวรด้วยการพ่น Tag เปิด
                    $openDiv = '<div class="sticker-wrapper" style="position: absolute; width: 8.0cm; height: 4.6cm; left: '.$left.'cm; top: '.$top.'cm;">';
                @endphp

                {!! $openDiv !!}
                    @if($base64Image) <img src="{{ $base64Image }}" class="bg-img"> @endif
                    <div class="no-text">{{ $formattedNo }}</div>
                    <div class="qr-box">
                        <img src="{{ $item['qrcode'] }}" class="qr-img">
                    </div>
                    <div class="footer-url">project.cpe.rmuti.ac.th</div>
                </div>
            @endforeach
        </div>
        @if (!$loop->last) <div class="page-break"></div> @endif
    @endforeach
</body>
</html>