<!DOCTYPE html><html lang="id">
<head>
    <meta charset="UTF-8"><title>
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

    .business-name {
        font-size: 14px;
        font-weight: bold;
    }

    .logo {
        max-width: 45mm;
        max-height: 25mm;
        object-fit: contain;
        margin-bottom: 4px;
    }

    .header-text,
    .footer-text {
        white-space: pre-line;
    }

    .contact {
        font-size: 9px;
        line-height: 1.4;
    }
</style>

</head><body>@php
/*
* Ambil pengaturan struk berdasarkan toko transaksi.
*
* receiptSetting sudah dibuat per store.
*/
$receiptSetting = \App\Models\ReceiptSetting::where(
'store_id',
$transaction->store_id
)->first();

/*
 * Cek apakah toko memiliki fitur Custom Struk.
 */
$customReceipt = false;

if ($transaction->store) {
    $customReceipt = $transaction->store->hasFeature('custom_receipt');
}

@endphp

{{-- ========================================================= --}}
{{-- HEADER STRUK --}}
{{-- ========================================================= --}}

<div class="center">@if($customReceipt && $receiptSetting)

    {{-- LOGO --}}
    @if($receiptSetting->logo)
        <img
            src="{{ public_path($receiptSetting->logo) }}"
            class="logo"
            alt="Logo"
        >
    @endif


    {{-- NAMA USAHA --}}
    @if($receiptSetting->business_name)
        <div class="business-name">
            {{ $receiptSetting->business_name }}
        </div>
    @endif


    {{-- ALAMAT --}}
    @if($receiptSetting->address)
        <div class="contact">
            {{ $receiptSetting->address }}
        </div>
    @endif


    {{-- TELEPON --}}
    @if($receiptSetting->phone)
        <div class="contact">
            Telp: {{ $receiptSetting->phone }}
        </div>
    @endif


    {{-- EMAIL --}}
    @if($receiptSetting->email)
        <div class="contact">
            {{ $receiptSetting->email }}
        </div>
    @endif


    {{-- HEADER TAMBAHAN --}}
    @if($receiptSetting->header_text)
        <div class="header-text small" style="margin-top: 4px;">
            {{ $receiptSetting->header_text }}
        </div>
    @endif

@else

    {{-- HEADER DEFAULT FREE --}}
    <div class="business-name">
        KasirKU
    </div>

@endif


{{-- INVOICE --}}
<div style="margin-top: 5px;">
    {{ $transaction->invoice_number }}
</div>

{{-- TANGGAL --}}
<div class="small">
    {{ $transaction->created_at->format('d/m/Y H:i') }}
</div>

</div><div class="line"></div>{{-- ========================================================= --}}
{{-- CUSTOMER CASHBON --}}
{{-- ========================================================= --}}

@if ($transaction->payment_method === 'Cashbon / Utang')

<table>

    <tr>
        <td>Pelanggan</td>

        <td class="right">
            {{ $transaction->customer->name ?? '-' }}
        </td>
    </tr>

</table>

<div class="line"></div>

@endif

{{-- ========================================================= --}}
{{-- ITEMS --}}
{{-- ========================================================= --}}

<table>@foreach ($transaction->items as $item)

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

</table><div class="line"></div>{{-- ========================================================= --}}
{{-- SUMMARY --}}
{{-- ========================================================= --}}

<table><tr>
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

    <td>Bayar</td>

    <td class="right">
        Rp {{ number_format($transaction->paid, 0, ',', '.') }}
    </td>

</tr>


<tr>

    <td>

        @if ($transaction->payment_method === 'Cashbon / Utang')
            Sisa Utang
        @else
            Kembalian
        @endif

    </td>


    <td class="right">

        @if ($transaction->payment_method === 'Cashbon / Utang')

            Rp
            {{ number_format(
                max(
                    0,
                    $transaction->total - $transaction->paid
                ),
                0,
                ',',
                '.'
            ) }}

        @else

            Rp
            {{ number_format(
                $transaction->change,
                0,
                ',',
                '.'
            ) }}

        @endif

    </td>

</tr>

</table><div class="line"></div>{{-- ========================================================= --}}
{{-- FOOTER --}}
{{-- ========================================================= --}}

<div class="center small"><div>
    Pembayaran:
    {{ $transaction->payment_method }}
</div>


@if($customReceipt && $receiptSetting)

    @if($receiptSetting->footer_text)

        <div
            class="footer-text"
            style="margin-top: 6px;"
        >
            {{ $receiptSetting->footer_text }}
        </div>

    @else

        <div style="margin-top: 6px;">
            Terima kasih 🙏
        </div>

    @endif


    @if($receiptSetting->business_name)

        <div
            class="bold"
            style="margin-top: 4px;"
        >
            {{ $receiptSetting->business_name }}
        </div>

    @endif

@else

    <div style="margin-top: 6px;">
        Terima kasih 🙏
    </div>

    <div
        class="bold"
        style="margin-top: 4px;"
    >
        KasirKU
    </div>

@endif

</div></body>
</html>