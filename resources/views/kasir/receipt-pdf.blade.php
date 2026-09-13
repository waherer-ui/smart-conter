<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>
        Struk {{ $transaction->invoice_number }}
    </title>

    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 4mm;
            width: 72mm;

            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }

        .right {
            text-align: right;
        }

        .total {
            font-size: 12px;
            font-weight: bold;
        }

        .small {
            font-size: 9px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="center">

        <div class="bold">
            KASIRKU
        </div>

        <div>
            {{ $transaction->invoice_number }}
        </div>

        <div class="small">
            {{ $transaction->created_at->format('d/m/Y H:i') }}
        </div>

    </div>

    <div class="line"></div>

    {{-- ITEM --}}
    <table>

        @foreach ($transaction->items as $item)

            <tr>
                <td colspan="2">
                    {{ $item->product_name }}
                </td>
            </tr>

            <tr>

                <td>
                    {{ $item->quantity }}
                    ×
                    Rp {{ number_format($item->price, 0, ',', '.') }}
                </td>

                <td class="right">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>

            </tr>

        @endforeach

    </table>

    <div class="line"></div>

    {{-- RINGKASAN --}}
    <table>

        <tr>
            <td>Subtotal</td>

            <td class="right">
                Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}
            </td>
        </tr>

        @if ($transaction->discount > 0)

            <tr>
                <td>Diskon</td>

                <td class="right">
                    Rp {{ number_format($transaction->discount, 0, ',', '.') }}
                </td>
            </tr>

        @endif

        <tr class="total">

            <td>Total</td>

            <td class="right">
                Rp {{ number_format($transaction->total, 0, ',', '.') }}
            </td>

        </tr>

        <tr>
            <td>
                Bayar
            </td>

            <td class="right">
                Rp {{ number_format($transaction->paid, 0, ',', '.') }}
            </td>
        </tr>

        <tr>
            <td>
                Kembalian
            </td>

            <td class="right">
                Rp {{ number_format($transaction->change, 0, ',', '.') }}
            </td>
        </tr>

    </table>

    <div class="line"></div>

    <div class="center small">

        <div>
            Pembayaran:
            {{ $transaction->payment_method }}
        </div>

        <br>

        Terima kasih 🙏

        <br>

        <strong>KasirKU</strong>

    </div>

</body>
</html>