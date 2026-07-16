{{--
    Overlay receipt for the pre-printed uniform sales booklet (9.5 x 12.5 cm).
    Prints ONLY the values — labels, table rules and the receipt number are
    already on the paper. All positions come from config/booklet-receipt.php.

    $preview = true renders in-browser with the booklet photo behind the
    values and a dashed box around each field, for calibrating coordinates.
--}}
@php
    $ox = (float) $layout['offset_x'];
    $oy = (float) $layout['offset_y'];

    // A field's `y` is the printed blank line; lift the text block so it
    // sits on top of that line rather than through it.
    $lift = 3.4;

    $place = function (float $x, float $lineY, float $w) use ($ox, $oy, $lift): string {
        return sprintf('left:%.2fmm;top:%.2fmm;width:%.2fmm;', $x + $ox, $lineY - $lift + $oy, $w);
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booklet Receipt — Sale #{{ $order->id }}</title>
<style>
    @page { margin: 0; }
    html, body { margin: 0; padding: 0; }
    body {
        font-family: 'DejaVu Sans Mono', monospace;
        font-size: {{ $layout['font_size'] }}pt;
        color: #1a1a1a;
    }
    {{-- No height on the sheet in PDF mode: the paper size already caps the
         page, and a full-height div makes DomPDF spill a blank second page. --}}
    .sheet { position: relative; width: 90mm; }
    .f { position: absolute; line-height: 1.1; white-space: nowrap; overflow: hidden; }
    .c { text-align: center; }
    .r { text-align: right; }
    @if ($preview)
    body { background: #52525b; padding: 24px; }
    .sheet {
        height: 130mm;
        background: #fff url('{{ asset('images/receipt-booklet-guide.png') }}') no-repeat;
        background-size: 100% 100%;
        transform: scale(2);
        transform-origin: top left;
        box-shadow: 0 2px 12px rgba(0,0,0,.4);
    }
    .f { outline: 0.3mm dashed rgba(220, 38, 38, .75); background: rgba(255, 235, 59, .25); }
    .preview-note {
        position: fixed; right: 16px; top: 16px; width: 240px;
        font: 12px/1.5 system-ui, sans-serif; color: #fff; background: #18181b;
        padding: 12px 14px; border-radius: 8px; opacity: .92;
    }
    .preview-note code { color: #86efac; }
    @endif
</style>
</head>
<body>
    @if ($preview)
        <div class="preview-note">
            Calibration preview — the photo behind the values is the booklet
            leaf at exact size (2&times; zoom). Nudge everything with
            <code>?dx=</code> / <code>?dy=</code> (mm), or edit
            <code>config/booklet-receipt.php</code> per field.
            Drop <code>preview=1</code> to get the printable PDF.
        </div>
    @endif

    <div class="sheet">
        <div class="f" style="{{ $place($layout['date']['x'], $layout['date']['y'], $layout['date']['w']) }}">{{ $receipt['date'] }}</div>

        @if ($receipt['sold_to'] !== '')
            <div class="f" style="{{ $place($layout['sold_to']['x'], $layout['sold_to']['y'], $layout['sold_to']['w']) }}">{{ $receipt['sold_to'] }}</div>
        @endif

        @if ($receipt['address'] !== '')
            <div class="f" style="{{ $place($layout['address']['x'], $layout['address']['y'], $layout['address']['w']) }}">{{ $receipt['address'] }}</div>
        @endif

        @foreach ($receipt['lines'] as $i => $line)
            @php $lineY = $layout['rows']['first_y'] + $layout['rows']['pitch'] * $i; @endphp
            @foreach ($layout['columns'] as $key => $col)
                <div
                    class="f {{ $col['align'] === 'center' ? 'c' : ($col['align'] === 'right' ? 'r' : '') }}"
                    style="{{ $place($col['x'], $lineY, $col['w']) }}"
                >{{ $line[$key] }}</div>
            @endforeach
        @endforeach

        <div class="f r" style="{{ $place($layout['total']['x'], $layout['total']['y'], $layout['total']['w']) }}">{{ $receipt['total'] }}</div>
    </div>
</body>
</html>
