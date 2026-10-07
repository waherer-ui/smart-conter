@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', '')
@section('mobile_action', 'scan')


{{-- =============================================================
     DASHBOARD CSS
     Diletakkan SEBELUM HTML dashboard agar tidak terjadi
     flash HTML polos saat halaman sedang reload.
============================================================== --}}

<style>

    /* =========================================================
       SEARCH STYLING (Clean & Minimalist ala E-commerce)
    ========================================================= */

    .dashboard-search-form {
        width: 100%;
    }

    .dashboard-search-box {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .dashboard-search-box input {
        width: 100%;
        height: 38px;
        /* Padding kiri untuk kaca pembesar, kanan untuk kamera */
        padding: 0 38px 0 34px; 
        box-sizing: border-box;

        /* Background putih semi-transparan / clean */
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px; /* Membuat bentuknya melengkung halus (pill shape) */

        color: #1f2937; /* Teks warna gelap agar kontras dengan background putih */
        font-size: 13px;
        outline: none;
        transition: all 0.2s ease;
    }

    .dashboard-search-box input:focus {
        background: #ffffff;
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    .dashboard-search-box input::placeholder {
        color: #9ca3af;
    }

    /* Posisi Icon Kaca Pembesar di Kiri */
    .search-icon-left {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 16px;
        pointer-events: none;
        z-index: 2;
    }

    /* Posisi Tombol Kamera di Kanan dalam Box */
    .search-icon-right {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        
        background: transparent;
        border: none;
        width: 30px;
        height: 30px;
        
        display: flex;
        align-items: center;
        justify-content: center;
        
        color: #4b5563;
        font-size: 14px;
        cursor: pointer;
        border-radius: 50%;
        z-index: 2;
        transition: background 0.15s ease;
    }

    .search-icon-right:active {
        background: rgba(0, 0, 0, 0.08);
    }
    /* =========================================================
       DASHBOARD
    ========================================================= */

    .dashboard-mobile {
        width: 100%;
        padding-bottom: 7rem;
    }

/* =========================================================
   CATEGORY FILTER CONTAINER
========================================================= */

.dashboard-category {
    width: 100%;

    margin-top: 8px;
    margin-bottom: 10px;

    padding: 5px;

    background: rgba(17, 24, 39, .72);

    border: 1px solid rgba(255, 255, 255, .07);

    border-radius: 15px;

    overflow: hidden;

    box-shadow:
        0 4px 14px rgba(0, 0, 0, .12);
}

/* =========================================================
   CATEGORY HORIZONTAL SCROLL
========================================================= */

.category-scroll {
    display: flex;
    align-items: center;

    gap: 6px;

    width: 100%;

    overflow-x: auto;
    overflow-y: hidden;

    white-space: nowrap;

    padding: 1px;

    scrollbar-width: none;

    -webkit-overflow-scrolling: touch;

    overscroll-behavior-x: contain;
}

.category-scroll::-webkit-scrollbar {
    display: none;
}


/* =========================================================
   CATEGORY CHIP
========================================================= */

.category-chip {
    flex: 0 0 auto;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 31px;

    padding: 0 12px;

    border-radius: 999px;

    background: transparent;

    border: 1px solid transparent;

    color: #9ca3af;

    font-size: 10px;
    font-weight: 700;

    line-height: 1;

    text-decoration: none;

    white-space: nowrap;

    transition:
        background .18s ease,
        color .18s ease,
        border-color .18s ease,
        box-shadow .18s ease,
        transform .12s ease;
}


/* =========================================================
   CATEGORY HOVER
========================================================= */

.category-chip:hover {
    background: rgba(255, 255, 255, .05);

    color: #e5e7eb;
}


/* =========================================================
   CATEGORY ACTIVE
========================================================= */

.category-chip.active {
    background: #4f46e5;

    border-color: #6366f1;

    color: #ffffff;

    box-shadow:
        0 4px 10px rgba(79, 70, 229, .25);
}


/* =========================================================
   CATEGORY TAP
========================================================= */

.category-chip:active {
    transform: scale(.95);
}


/* =========================================================
   TABLET / DESKTOP
========================================================= */

@media (min-width: 640px) {

    .dashboard-category {
        margin-top: 10px;
        margin-bottom: 12px;

        padding: 6px;
    }

    .category-scroll {
        gap: 7px;
    }

    .category-chip {
        min-height: 33px;

        padding: 0 14px;

        font-size: 11px;
    }

}
    /* =========================================================
       PRODUCTS HEADER
    ========================================================= */

    .products-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;

        padding: 2px 2px 0;
    }

    .products-title-row {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .products-title-row h2 {
        color: white;

        font-size: 16px;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .products-count {
        min-width: 22px;
        padding: 3px 6px;

        text-align: center;

        border-radius: 7px;

        background: rgba(255, 255, 255, .05);
        border: 1px solid rgba(255, 255, 255, .06);

        color: #6b7280;

        font-size: 8px;
        font-weight: 700;
    }

    .products-heading p {
        margin-top: 2px;

        color: #6b7280;

        font-size: 9px;
    }


    /* =========================================================
       PRODUCT GRID
    ========================================================= */

    .product-grid {
        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 9px;
    }


    /* =========================================================
       PRODUCT CARD
    ========================================================= */

    .product-card {
        position: relative;

        min-width: 0;

        display: flex;
        flex-direction: column;

        overflow: hidden;

        padding: 8px;

        border-radius: 17px;

        background:
            linear-gradient(
                145deg,
                rgba(17, 24, 39, .96),
                rgba(15, 23, 42, .88)
            );

        border: 1px solid rgba(255, 255, 255, .075);

        box-shadow:
            0 8px 25px rgba(0, 0, 0, .15);

        cursor: pointer;

        -webkit-tap-highlight-color: transparent;

        transition:
            transform .18s ease,
            border-color .18s ease,
            background .18s ease;
    }

    .product-card:active {
        transform: scale(.975);
    }

    .product-image {
        position: relative;

        width: 100%;

        aspect-ratio: 1 / 1;

        overflow: hidden;

        border-radius: 12px;

        background: #1f2937;

        border: 1px solid rgba(255, 255, 255, .06);
    }

    .product-image img {
        width: 100%;
        height: 100%;

        display: block;

        object-fit: cover;

        transition:
            transform .3s ease;
    }

    .product-no-image {
        width: 100%;
        height: 100%;

        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        color: #4b5563;
    }

    .product-no-image span {
        font-size: 26px;
    }

    .product-no-image small {
        margin-top: 4px;

        font-size: 8px;
    }


    /* =========================================================
       STOCK BADGE
    ========================================================= */

    .stock-badge {
        position: absolute;

        top: 7px;
        right: 7px;

        padding: 5px 7px;

        border-radius: 999px;

        font-size: 8px;
        font-weight: 800;

        backdrop-filter: blur(10px);

        box-shadow:
            0 4px 12px rgba(0, 0, 0, .2);
    }

    .stock-empty {
        display: flex;
        align-items: center;
        gap: 4px;

        background: rgba(239, 68, 68, .92);

        color: white;
    }

    .stock-empty i {
        width: 5px;
        height: 5px;

        border-radius: 999px;

        background: white;
    }

    .stock-low {
        background: rgba(245, 158, 11, .92);

        color: #111827;
    }

    .stock-safe {
        background: rgba(3, 7, 18, .78);

        border: 1px solid rgba(255, 255, 255, .10);

        color: #6ee7b7;
    }


    /* =========================================================
       PRODUCT INFO
    ========================================================= */

    .product-info {
        min-width: 0;

        padding: 9px 2px 0;
    }

    .product-meta {
        display: flex;
        align-items: center;
        gap: 5px;

        min-width: 0;

        margin-bottom: 6px;
    }

    .product-category {
        max-width: 62%;

        overflow: hidden;

        text-overflow: ellipsis;
        white-space: nowrap;

        padding: 3px 5px;

        border-radius: 5px;

        background: rgba(99, 102, 241, .09);
        border: 1px solid rgba(99, 102, 241, .10);

        color: #a5b4fc;

        font-size: 7px;
        font-weight: 700;
    }

    .product-sku {
        min-width: 0;

        overflow: hidden;

        text-overflow: ellipsis;
        white-space: nowrap;

        color: #4b5563;

        font-family: monospace;

        font-size: 7px;
    }

    .product-info h3 {
        display: -webkit-box;

        overflow: hidden;

        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;

        min-height: 31px;

        color: white;

        font-size: 11px;
        line-height: 1.4;

        font-weight: 700;
    }

    .product-brand {
        margin-top: 2px;

        overflow: hidden;

        text-overflow: ellipsis;
        white-space: nowrap;

        color: #6b7280;

        font-size: 8px;
    }

    .product-brand-placeholder {
        height: 12px;
    }

    .product-price {
        margin-top: 8px;
    }

    .product-price small {
        display: block;

        margin-bottom: 2px;

        color: #4b5563;

        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .product-price strong {
        display: block;

        overflow: hidden;

        text-overflow: ellipsis;
        white-space: nowrap;

        color: #34d399;

        font-size: 14px;
        line-height: 1.2;

        font-weight: 900;
    }

    /* =========================================================
       PRODUCT ACTION
    ========================================================= */

    .product-action {
        margin-top: 9px;
    }

    .product-action button {
        width: 100%;
        height: 35px;

        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;

        border: 0;
        border-radius: 10px;

        background: #4f46e5;

        color: white;

        font-size: 9px;
        font-weight: 800;

        box-shadow:
            0 6px 15px rgba(49, 46, 129, .25);

        transition: .16s ease;
    }

    .product-action button:active {
        transform: scale(.96);
    }

    .product-action-disabled {
        background: #1f2937 !important;
        color: #4b5563 !important;

        box-shadow: none !important;
    }


    /* =========================================================
       MODAL
    ========================================================= */

    .modal-backdrop {
        position: fixed;
        inset: 0;

        z-index: 110;

        align-items: flex-end;
        justify-content: center;

        padding: 0;

        background: rgba(0, 0, 0, .78);

        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .modal-backdrop.hidden {
        display: none !important;
    }

    .modal-backdrop.flex {
        display: flex !important;
    }

    .product-modal {
        width: 100%;
        max-height: 92vh;

        overflow-y: auto;

        border-radius: 24px 24px 0 0;

        background:
            linear-gradient(
                160deg,
                #111827,
                #0f172a
            );

        border: 1px solid rgba(255, 255, 255, .08);

        box-shadow:
            0 -20px 60px rgba(0, 0, 0, .4);
    }

    .modal-product-image {
        position: relative;

        width: 100%;
        height: 230px;

        display: flex;
        align-items: center;
        justify-content: center;

        overflow: hidden;

        background: #1f2937;
    }

    .modal-product-image img {
        width: 100%;
        height: 100%;

        object-fit: contain;
    }

    .modal-product-image > span {
        color: #4b5563;
        font-size: 40px;
    }

    .modal-image-close {
        position: absolute;

        top: 12px;
        right: 12px;

        z-index: 5;

        width: 36px;
        height: 36px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 999px;

        background: rgba(0, 0, 0, .6);

        color: white;

        font-size: 12px;
    }

    .modal-product-body {
        padding: 16px;
    }

    .modal-product-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        gap: 12px;
    }

    .modal-product-heading > div {
        min-width: 0;
    }

    .modal-product-heading > div > span {
        color: #818cf8;
        font-size: 9px;
        font-weight: 700;
    }

    .modal-product-heading h3 {
        margin-top: 3px;

        color: white;

        font-size: 19px;
        line-height: 1.25;

        font-weight: 900;
    }

    .modal-product-heading p {
        margin-top: 3px;

        color: #9ca3af;

        font-size: 10px;
    }

    .modal-product-heading > span {
        flex-shrink: 0;

        color: #4b5563;

        font-family: monospace;

        font-size: 8px;
    }

    .modal-price-box {
        margin-top: 16px;

        padding: 13px;

        border-radius: 14px;

        background: rgba(16, 185, 129, .07);
        border: 1px solid rgba(16, 185, 129, .10);
    }

    .modal-price-box small {
        display: block;

        color: #6b7280;

        font-size: 9px;
    }

    .modal-price-box strong {
        display: block;

        margin-top: 2px;

        color: #34d399;

        font-size: 22px;
        font-weight: 900;
    }

    .modal-stock {
        display: flex;
        align-items: center;
        justify-content: space-between;

        margin-top: 9px;

        padding: 12px 14px;

        border-radius: 13px;

        background: rgba(31, 41, 55, .8);
    }

    .modal-stock span {
        color: #9ca3af;
        font-size: 10px;
    }

    .modal-stock strong {
        color: white;
        font-size: 11px;
    }

    .modal-quantity {
        margin-top: 16px;
    }

    .modal-quantity label {
        display: block;

        margin-bottom: 7px;

        color: #d1d5db;

        font-size: 10px;
        font-weight: 700;
    }

    .quantity-control {
        display: flex;

        height: 46px;
    }

    .quantity-control button,
    .quantity-control div {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quantity-control button {
        width: 52px;

        background: #1f2937;

        border: 1px solid rgba(255, 255, 255, .08);

        color: white;

        font-size: 20px;
    }

    .quantity-control button:first-child {
        border-radius: 12px 0 0 12px;
    }

    .quantity-control button:last-child {
        border-radius: 0 12px 12px 0;
    }

    .quantity-control div {
        flex: 1;

        background: #111827;

        border-top: 1px solid rgba(255, 255, 255, .08);
        border-bottom: 1px solid rgba(255, 255, 255, .08);

        color: white;

        font-size: 14px;
        font-weight: 800;
    }

    .modal-total {
        display: flex;
        align-items: center;
        justify-content: space-between;

        margin-top: 12px;

        padding: 13px 14px;

        border-radius: 13px;

        background: rgba(255, 255, 255, .04);
    }

    .modal-total span {
        color: #9ca3af;
        font-size: 11px;
    }

    .modal-total strong {
        color: white;
        font-size: 16px;
    }

    .modal-add-button {
        width: 100%;

        height: 48px;

        margin-top: 12px;

        border: 0;
        border-radius: 13px;

        background: #4f46e5;

        color: white;

        font-size: 12px;
        font-weight: 800;

        box-shadow:
            0 10px 25px rgba(49, 46, 129, .3);

        transition: .16s ease;
    }

    .modal-add-button:active {
        transform: scale(.98);
    }


    /* =========================================================
       SCANNER
    ========================================================= */

    .scanner-modal {
        width: calc(100% - 24px);
        max-width: 420px;

        overflow: hidden;

        border-radius: 22px;

        background: #111827;

        border: 1px solid rgba(255, 255, 255, .08);

        box-shadow:
            0 25px 70px rgba(0, 0, 0, .5);
    }

    .scanner-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 10px;

        padding: 15px;

        border-bottom: 1px solid rgba(255, 255, 255, .07);
    }

    .scanner-header h3 {
        color: white;

        font-size: 14px;
        font-weight: 800;
    }

    .scanner-header p {
        margin-top: 3px;

        color: #6b7280;

        font-size: 9px;
    }

    .modal-close {
        width: 34px;
        height: 34px;

        flex-shrink: 0;

        border-radius: 999px;

        background: #1f2937;

        color: white;
    }

    .scanner-body {
        padding: 13px;
    }

    .scanner-reader {
        min-height: 230px;

        overflow: hidden;

        border-radius: 16px;

        background: black;
    }

    .scanner-status {
        margin-top: 9px;

        text-align: center;

        color: #9ca3af;

        font-size: 9px;
    }


    /* =========================================================
       FLOATING CART
    ========================================================= */

    .floating-cart {
        position: fixed;

        left: 12px;
        right: 12px;
        bottom: 72px;

        z-index: 90;

        max-width: 500px;

        margin: auto;
    }

    .floating-cart.hidden {
        display: none !important;
    }

    .floating-cart > button {
        width: 100%;

        min-height: 58px;

        display: flex;
        align-items: center;
        justify-content: space-between;

        padding: 8px 10px;

        border: 1px solid rgba(255, 255, 255, .09);

        border-radius: 17px;

        background: rgba(3, 7, 18, .94);

        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);

        box-shadow:
            0 15px 40px rgba(0, 0, 0, .4);

        color: white;
    }

    .floating-cart-left {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .floating-cart-icon {
        width: 40px;
        height: 40px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 12px;

        background: #4f46e5;

        box-shadow:
            0 6px 18px rgba(49, 46, 129, .4);

        font-size: 17px;
    }

    .floating-cart-left strong {
        display: block;

        color: white;

        font-size: 13px;
        line-height: 1.2;

        text-align: left;
    }

    .floating-cart-left span {
        display: block;

        margin-top: 2px;

        color: #6b7280;

        font-size: 8px;

        text-align: left;
    }

    .floating-cart-action {
        display: flex;
        align-items: center;
        gap: 5px;

        color: #818cf8;

        font-size: 9px;
        font-weight: 800;
    }


    /* =========================================================
       GUEST PROMO
    ========================================================= */

    .guest-promo {
        position: relative;

        grid-column: 1 / -1;

        overflow: hidden;

        padding: 22px 17px;

        border-radius: 22px;

        background:
            linear-gradient(
                145deg,
                #4f46e5,
                #2563eb 55%,
                #10b981
            );

        box-shadow:
            0 20px 45px rgba(37, 99, 235, .22);
    }

    .guest-promo-glow {
        position: absolute;

        width: 170px;
        height: 170px;

        top: -80px;
        right: -60px;

        border-radius: 999px;

        background: rgba(255, 255, 255, .09);

        filter: blur(35px);
    }

    .guest-promo-label {
        display: inline-flex;

        padding: 6px 9px;

        border-radius: 999px;

        background: rgba(255, 255, 255, .10);
        border: 1px solid rgba(255, 255, 255, .12);

        color: white;

        font-size: 9px;
        font-weight: 800;
    }

    .guest-promo h3 {
        margin-top: 13px;

        color: white;

        font-size: 22px;
        line-height: 1.2;

        font-weight: 900;
    }

    .guest-promo h3 span {
        display: block;

        color: #a7f3d0;
    }

    .guest-promo > div > p {
        margin-top: 9px;

        color: #dbeafe;

        font-size: 10px;
        line-height: 1.6;
    }

    .guest-features {
        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 6px;

        margin-top: 15px;
    }

    .guest-features > div {
        padding: 9px 6px;

        border-radius: 12px;

        background: rgba(0, 0, 0, .10);
        border: 1px solid rgba(255, 255, 255, .10);

        text-align: center;
    }

    .guest-features span {
        display: block;

        font-size: 18px;
    }

    .guest-features small {
        display: block;

        margin-top: 4px;

        color: white;

        font-size: 7px;
        font-weight: 700;
    }

    .guest-cta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        width: 100%;

        margin-top: 15px;
        padding: 11px 14px;

        border-radius: 12px;

        background: white;

        color: #4338ca;

        font-size: 11px;
        font-weight: 900;

        box-shadow:
            0 8px 20px rgba(0, 0, 0, .16);
    }

    .guest-tagline {
        margin-top: 8px !important;

        color: rgba(239, 246, 255, .75) !important;

        font-size: 8px !important;

        text-align: center;
    }


    /* =========================================================
       EMPTY PRODUCTS
    ========================================================= */

    .empty-products {
        grid-column: 1 / -1;

        padding: 42px 20px;

        text-align: center;

        border-radius: 20px;

        background: rgba(31, 41, 55, .48);

        border: 1px solid rgba(255, 255, 255, .08);
    }

    .empty-products-icon {
        font-size: 38px;
    }

    .empty-products h3 {
        margin-top: 10px;

        color: white;

        font-size: 14px;
        font-weight: 800;
    }

    .empty-products p {
        max-width: 300px;

        margin: 5px auto 16px;

        color: #6b7280;

        font-size: 9px;
        line-height: 1.6;
    }

    .empty-products a {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 10px 14px;

        border-radius: 11px;

        background: #4f46e5;

        color: white;

        font-size: 10px;
        font-weight: 800;

        box-shadow:
            0 8px 20px rgba(49, 46, 129, .25);
    }

    /* =========================================================
       TABLET / DESKTOP
    ========================================================= */

    @media (min-width: 640px) {

        .dashboard-product-modal {
            max-height: 85vh;
        }

    }


    /* =========================================================
       DESKTOP
    ========================================================= */

    @media (min-width: 641px) {

        .dashboard-mobile {
            padding-bottom: 100px;
        }

        .product-grid {
            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 12px;
        }

        .product-card:hover {
            transform: translateY(-3px);

            border-color:
                rgba(52, 211, 153, .28);
        }

        .product-card:hover .product-image img {
            transform: scale(1.03);
        }

        .product-info h3 {
            font-size: 13px;
        }

        .product-price strong {
            font-size: 17px;
        }

        .product-action button {
            height: 40px;

            font-size: 11px;
        }

        .product-modal {
            width: calc(100% - 32px);
            max-width: 480px;

            border-radius: 24px;
        }

        .floating-cart {
            left: auto;
            right: 20px;
            bottom: 20px;

            width: 380px;

            margin: 0;
        }

    }


    /* =========================================================
       LARGE DESKTOP
    ========================================================= */

    @media (min-width: 1024px) {

        .product-grid {
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
        }

    }


    /* =========================================================
       REDUCED MOTION
    ========================================================= */

    @media (prefers-reduced-motion: reduce) {

        *,
        *::before,
        *::after {
            scroll-behavior: auto !important;
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }

    }
    
/* =========================================================
   DASHBOARD MENU — ICON ONLY
========================================================= */

.dashboard-menu-box {
    min-height: 78px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    gap: 7px;

    padding: 6px 2px;

    background: transparent !important;
    border: none !important;
    box-shadow: none !important;

    color: white;
    text-decoration: none;

    transition:
        transform 0.18s ease,
        opacity 0.18s ease;
}

.dashboard-menu-box:hover {
    background: transparent !important;
    border: none !important;

    transform: translateY(-3px);
}

.dashboard-menu-box:active {
    transform: scale(0.94);
}


/* =========================================================
   MODERN ICON
========================================================= */

.dashboard-menu-icon {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: transparent !important;
    border: none !important;

    font-size: 0;

    line-height: 1;

    flex-shrink: 0;

    filter:
        drop-shadow(0 5px 10px rgba(0, 0, 0, 0.18));
}

.dashboard-menu-icon svg {
    width: 34px;
    height: 34px;

    display: block;

    stroke-width: 1.8;

    transition:
        transform 0.18s ease,
        filter 0.18s ease;
}

.dashboard-menu-box:hover .dashboard-menu-icon svg {
    transform: scale(1.08);

    filter:
        drop-shadow(0 4px 8px rgba(255, 255, 255, 0.12));
}


/* =========================================================
   ICON COLORS
========================================================= */

.menu-icon-cart {
    color: #35e0a1;
}

.menu-icon-product {
    color: #ffb84d;
}

.menu-icon-attendance {
    color: #b58cff;
}

.menu-icon-supplier {
    color: #42b8ff;
}

.menu-icon-cashbon {
    color: #ffd45a;
}

.menu-icon-customer {
    color: #f472b6;
}

.menu-icon-transfer {
    color: #42d9e8;
}

.menu-icon-payment {
    color: #a78bfa;
}


/* =========================================================
   LABEL
========================================================= */

.dashboard-menu-label {
    font-size: 10.5px;

    font-weight: 600;

    color: rgba(255, 255, 255, 0.88);

    line-height: 1.15;

    text-align: center;

    white-space: nowrap;
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 639px) {

    .dashboard-menu-box {
        min-height: 72px;

        padding: 5px 2px;

        gap: 6px;
    }

    .dashboard-menu-icon {
        width: 36px;
        height: 36px;
    }

    .dashboard-menu-icon svg {
        width: 32px;
        height: 32px;
    }

    .dashboard-menu-label {
        font-size: 10px;
    }
}

/* =========================================================
   TOUCH / CLICK FEEDBACK
========================================================= */

.dashboard-menu-box {
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}

.dashboard-menu-box:active {
    transform: scale(0.92);
    opacity: 0.78;
}

.dashboard-menu-box:active .dashboard-menu-icon svg {
    transform: scale(0.88);
    filter:
        drop-shadow(0 2px 5px rgba(255, 255, 255, 0.20));
    transition:
        transform 0.08s ease,
        filter 0.08s ease;
}


/* =========================================================
   MOBILE TOUCH FEEDBACK
========================================================= */

@media (max-width: 639px) {

    .dashboard-menu-box:active {
        transform: scale(0.90);
        opacity: 0.75;
    }

    .dashboard-menu-box:active .dashboard-menu-icon svg {
        transform: scale(0.86);
    }
}

</style>

@section('header_tools')

    {{-- =========================================================
         MOBILE SEARCH (Gaya E-commerce / Minimalis)
    ========================================================== --}}
        <form
            action="{{ route('dashboard') }}"
            method="GET"
            class="dashboard-search-form"
        >
            <div class="dashboard-search-box">
                <!-- Icon Kaca Pembesar di Kiri -->
                <span class="search-icon-left">
                    ⌕
                </span>

                <!-- Input Pencarian -->
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari produk atau SKU..."
                    autocomplete="off"
                >

                <!-- Icon Kamera di Kanan (Berfungsi sebagai tombol/trigger scan) -->
                <button
                    type="button"
                    onclick="openQrScanner()"
                    class="search-icon-right"
                    aria-label="Scan QR / Barcode"
                >
                    📷
                </button>
            </div>
        </form>
@endsection

@section('content')

    <div class="dashboard-mobile">
        <section class="">
{{-- =====================================================
     MENU GRID
====================================================== --}}

<div class="grid grid-cols-4 gap-2.5 sm:gap-3">

    {{-- KASIR --}}
    <a
        href="{{ route('kasir.index') }}"
        class="dashboard-menu-box"
    >
        <span class="dashboard-menu-icon menu-icon-cart">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="20" r="1.5"/>
                <circle cx="18" cy="20" r="1.5"/>
                <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6"/>
                <path d="M8 12h10"/>
            </svg>
        </span>

        <span class="dashboard-menu-label">
            Keranjang
        </span>
    </a>


    {{-- PRODUK --}}
    <a
        href="{{ route('produk.index') }}"
        class="dashboard-menu-box"
    >
        <span class="dashboard-menu-icon menu-icon-product">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/>
                <path d="m4.5 7.5 7.5 4 7.5-4"/>
                <path d="M12 11.5V21"/>
            </svg>
        </span>

        <span class="dashboard-menu-label">
           Restock Produk
        </span>
    </a>


    {{-- ABSEN --}}
    <a
        href="{{ route('attendance.index') }}"
        class="dashboard-menu-box"
    >
        <span class="dashboard-menu-icon menu-icon-attendance">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="8.5"/>
                <path d="M12 7v5l3 2"/>
            </svg>
        </span>

        <span class="dashboard-menu-label">
            Absen
        </span>
    </a>


    {{-- SUPPLIER --}}
    <a
        href="{{ route('supplier.index') }}"
        class="dashboard-menu-box"
    >
        <span class="dashboard-menu-icon menu-icon-supplier">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h11v11H3z"/>
                <path d="M14 10h4l3 3v4h-7z"/>
                <circle cx="7" cy="19" r="1.7"/>
                <circle cx="18" cy="19" r="1.7"/>
                <path d="M14 14h7"/>
            </svg>
        </span>

        <span class="dashboard-menu-label">
            Supplier
        </span>
    </a>


    {{-- CASHBON --}}
    <a
        href="{{ route('laporan.piutang') }}"
        class="dashboard-menu-box"
    >
        <span class="dashboard-menu-icon menu-icon-cashbon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="8.5"/>
                <path d="M12 7v10"/>
                <path d="M15 9.5c-.7-.8-1.7-1.2-3-1.2-1.6 0-2.7.8-2.7 2s1 1.8 2.7 2.1c1.7.3 2.7 1 2.7 2.1s-1.1 2-2.8 2c-1.3 0-2.4-.5-3.1-1.4"/>
            </svg>
        </span>

        <span class="dashboard-menu-label">
            Cashbon
        </span>
    </a>


    {{-- PELANGGAN --}}
    <a
        href="{{ route('pelanggan.index') }}"
        class="dashboard-menu-box"
    >
        <span class="dashboard-menu-icon menu-icon-customer">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="8" r="3"/>
                <path d="M3.5 19c.5-3.2 2.4-5 5.5-5s5 1.8 5.5 5"/>
                <path d="M16 5.5a3 3 0 0 1 0 5.8"/>
                <path d="M17 14c2.1.4 3.4 2 3.7 4.5"/>
            </svg>
        </span>

        <span class="dashboard-menu-label">
            Pelanggan
        </span>
    </a>


    {{-- TRANSFER --}}
    <a
        href="{{ route('transfer.index') }}"
        class="dashboard-menu-box"
    >
        <span class="dashboard-menu-icon menu-icon-transfer">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 8h13"/>
                <path d="m14 5 3 3-3 3"/>
                <path d="M20 16H7"/>
                <path d="m10 13-3 3 3 3"/>
            </svg>
        </span>

        <span class="dashboard-menu-label">
            Transfer Produk
        </span>
    </a>


    {{-- REKENING --}}
    <a
        href="{{ route('payment-settings.index') }}"
        class="dashboard-menu-box"
    >
        <span class="dashboard-menu-icon menu-icon-payment">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="5" width="18" height="14" rx="2"/>
                <path d="M3 10h18"/>
                <path d="M7 15h4"/>
            </svg>
        </span>

        <span class="dashboard-menu-label">
            Rekening
        </span>
    </a>

</div>

        </section>


{{-- =========================================================
     CATEGORY
========================================================== --}}

<section class="dashboard-category">

    <div class="category-scroll">

        {{-- SEMUA --}}
        <a
            href="{{ route('dashboard', array_filter([
                'search' => request('search'),
            ])) }}"
            class="category-chip {{ (!$category || $category === 'all') ? 'active' : '' }}"
        >
            Semua
        </a>

        {{-- CATEGORY DARI DATABASE --}}
        @isset($categories)

            @foreach($categories as $cat)

                <a
                    href="{{ route('dashboard', array_filter([
                        'search' => request('search'),
                        'category' => $cat,
                    ])) }}"
                    class="category-chip {{ $category === $cat ? 'active' : '' }}"
                >
                    {{ $cat }}
                </a>

            @endforeach

        @endisset

    </div>

</section>


        {{-- =========================================================
             PRODUCT HEADER
        ========================================================== --}}

        <section class="products-heading">

            <div>

                <div class="products-title-row">

                    <h2>
                        Produk
                    </h2>

                    <span class="products-count">
                        {{ $products->count() }}
                    </span>

                </div>


                <p>
                    Pilih produk untuk mulai transaksi
                </p>

            </div>

        </section>


        {{-- =========================================================
             PRODUCTS
        ========================================================== --}}

        <div class="product-grid">

            @forelse($products as $p)

                <article
                    class="product-card"
                    onclick="openProductModal(
                        {{ $p->id }},
                        @js($p->name),
                        @js($p->sku),
                        @js($p->category),
                        @js($p->brand),
                        {{ (float) $p->price }},
                        {{ (int) $p->stock }},
                        @js($p->image)
                    )"
                >

                    {{-- PRODUCT IMAGE --}}
                    <div class="product-image">

                        @if($p->image)

                            <img
                                src="{{ Storage::disk('s3')->url($p->image) }}"
                                alt="{{ $p->name }}"
                                loading="lazy"
                            >

                        @else

                            <div class="product-no-image">

                                <span>
                                    📦
                                </span>

                                <small>
                                    No Image
                                </small>

                            </div>

                        @endif


                        {{-- STOCK --}}
                        @if($p->stock <= 0)

                            <span class="stock-badge stock-empty">

                                <i></i>

                                Habis

                            </span>

                        @elseif($p->stock <= 5)

                            <span class="stock-badge stock-low">
                                {{ $p->stock }} pcs
                            </span>

                        @else

                            <span class="stock-badge stock-safe">
                                {{ $p->stock }} pcs
                            </span>

                        @endif

                    </div>


                    {{-- PRODUCT INFO --}}
                    <div class="product-info">

                        <div class="product-meta">

                            <span class="product-category">
                                {{ $p->category }}
                            </span>

                            <span class="product-sku">
                                {{ $p->sku }}
                            </span>

                        </div>


                        <h3>
                            {{ $p->name }}
                        </h3>


                        @if($p->brand)

                            <p class="product-brand">
                                {{ $p->brand }}
                            </p>

                        @else

                            <div class="product-brand-placeholder"></div>

                        @endif


                        <div class="product-price">

                            <small>
                                Harga jual
                            </small>

                            <strong>
                                Rp {{ number_format($p->price, 0, ',', '.') }}
                            </strong>

                        </div>

                    </div>


                    {{-- ACTION --}}
                    <div class="product-action">

                        @if($p->stock > 0)

                            <button
                                type="button"
                                onclick="event.stopPropagation(); openProductModal(
                                    {{ $p->id }},
                                    @js($p->name),
                                    @js($p->sku),
                                    @js($p->category),
                                    @js($p->brand),
                                    {{ (float) $p->price }},
                                    {{ (int) $p->stock }},
                                    @js($p->image)
                                )"
                            >

                                <span>
                                    ＋
                                </span>

                                Tambah

                            </button>

                        @else

                            <button
                                type="button"
                                disabled
                                class="product-action-disabled"
                            >
                                Stok Habis
                            </button>

                        @endif

                    </div>

                </article>


            @empty

                {{-- =================================================
                     EMPTY STATE / PROMO
                ================================================== --}}

                @if(!session('logged_in'))

                    <section class="guest-promo">

                        <div class="guest-promo-glow"></div>

                        <div class="relative z-10">

                            <span class="guest-promo-label">
                                ✨ KasirKU untuk UMKM
                            </span>


                            <h3>
                                Punya Usaha?
                                <span>
                                    Saatnya Naik Level!
                                </span>
                            </h3>


                            <p>
                                Kelola produk, stok, transaksi, dan laporan
                                toko Anda lebih mudah dalam satu aplikasi.
                            </p>


                            <div class="guest-features">

                                <div>
                                    <span>📦</span>
                                    <small>
                                        Produk & Stok
                                    </small>
                                </div>

                                <div>
                                    <span>🛒</span>
                                    <small>
                                        Kasir & Transaksi
                                    </small>
                                </div>

                                <div>
                                    <span>📊</span>
                                    <small>
                                        Laporan
                                    </small>
                                </div>

                            </div>


                            <a
                                href="{{ route('register') }}"
                                class="guest-cta"
                            >

                                🚀

                                Buat Toko Gratis

                                <span>
                                    →
                                </span>

                            </a>


                            <p class="guest-tagline">
                                Cocok untuk UMKM, toko kecil, dan usaha berkembang.
                            </p>

                        </div>

                    </section>

                @else

                    <section class="empty-products">

                        <div class="empty-products-icon">
                            📦
                        </div>


                        <h3>
                            Belum ada produk
                        </h3>


                        <p>
                            Tambahkan produk pertama Anda untuk mulai mengelola
                            stok dan penjualan.
                        </p>


                        <a href="{{ route('produk.index') }}">

                            <span>
                                ＋
                            </span>

                            Tambah Produk / Servis

                        </a>

                    </section>

                @endif

            @endforelse

        </div>

    </div>


    {{-- =============================================================
         QR SCANNER MODAL
    ============================================================== --}}

    <div
        id="qrScannerModal"
        class="modal-backdrop hidden"
    >

        <div class="scanner-modal">

            <div class="scanner-header">

                <div>

                    <h3>
                        📷 Scan Produk
                    </h3>

                    <p>
                        Arahkan kamera ke QR / barcode SKU
                    </p>

                </div>


                <button
                    type="button"
                    onclick="closeQrScanner()"
                    class="modal-close"
                >
                    ✕
                </button>

            </div>


            <div class="scanner-body">

                <div
                    id="qr-reader"
                    class="scanner-reader"
                ></div>


                <p
                    id="qrScannerStatus"
                    class="scanner-status"
                >
                    Menyiapkan kamera...
                </p>

            </div>

        </div>

    </div>


    {{-- =============================================================
         PRODUCT MODAL
    ============================================================== --}}

    <div
        id="productModal"
        class="modal-backdrop product-modal-backdrop hidden"
    >

        <div
            id="productModalContent"
            class="dashboard-product-modal w-full max-w-lg bg-gray-900 border border-white/10 rounded-t-[28px] sm:rounded-[28px] shadow-2xl overflow-hidden"
        >

            {{-- IMAGE --}}
            <div class="modal-product-image">

                <button
                    type="button"
                    onclick="closeProductModal()"
                    class="modal-image-close"
                >
                    ✕
                </button>


                <img
                    id="modalProductImage"
                    src=""
                    alt=""
                    class="hidden"
                >


                <span id="modalNoImage">
                    📦
                </span>

            </div>


            {{-- CONTENT --}}
            <div class="modal-product-body">

                <div class="modal-product-heading">

                    <div class="min-w-0">

                        <span id="modalProductCategory"></span>

                        <h3 id="modalProductName"></h3>

                        <p id="modalProductBrand"></p>

                    </div>


                    <span id="modalProductSku"></span>

                </div>


                {{-- PRICE --}}
                <div class="modal-price-box">

                    <small>
                        Harga
                    </small>

                    <strong id="modalProductPrice">
                        Rp 0
                    </strong>

                </div>


                {{-- STOCK --}}
                <div class="modal-stock">

                    <span>
                        Stok tersedia
                    </span>

                    <strong id="modalProductStock">
                        0 pcs
                    </strong>

                </div>


                {{-- QUANTITY --}}
                <div class="modal-quantity">

                    <label>
                        Jumlah
                    </label>


                    <div class="quantity-control">

                        <button
                            type="button"
                            onclick="changeModalQty(-1)"
                        >
                            −
                        </button>


                        <div id="modalQty">
                            1
                        </div>


                        <button
                            type="button"
                            onclick="changeModalQty(1)"
                        >
                            +
                        </button>

                    </div>

                </div>


                {{-- TOTAL --}}
                <div class="modal-total">

                    <span>
                        Total
                    </span>

                    <strong id="modalTotal">
                        Rp 0
                    </strong>

                </div>


                {{-- ADD --}}
                <button
                    id="modalAddButton"
                    type="button"
                    onclick="addModalToCart()"
                    class="modal-add-button"
                >
                    🛒
                    Masukkan Keranjang
                </button>

            </div>

        </div>

    </div>


    {{-- =============================================================
         FLOATING CART
    ============================================================== --}}

    <div
        id="floatingCart"
        class="floating-cart hidden"
    >

        <button
            type="button"
            onclick="goToKasir()"
        >

            <div class="floating-cart-left">

                <div class="floating-cart-icon">
                    🛒
                </div>


                <div>

                    <strong id="floatingCartTotal">
                        Rp 0
                    </strong>

                    <span id="floatingCartItems">
                        0 item
                    </span>

                </div>

            </div>


            <div class="floating-cart-action">

                Keranjang

                <span>
                    →
                </span>

            </div>

        </button>

    </div>


    {{-- =============================================================
         JAVASCRIPT
    ============================================================== --}}

    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>

        const CART_STORAGE_KEY = 'smart_pos_cart';

        let dashboardCart = [];

        let selectedProduct = {
            id: null,
            name: '',
            sku: '',
            category: '',
            brand: '',
            price: 0,
            stock: 0,
            image: null
        };

        let modalQty = 1;


        function formatRupiah(value) {

            return 'Rp ' +
                Number(value || 0)
                    .toLocaleString('id-ID');

        }


        /* =============================================================
           CART
        ============================================================= */

        function loadDashboardCart() {

            try {

                const savedCart =
                    localStorage.getItem(
                        CART_STORAGE_KEY
                    );

                if (!savedCart) {

                    dashboardCart = [];

                    return;
                }


                const parsed =
                    JSON.parse(savedCart);


                if (!Array.isArray(parsed)) {

                    dashboardCart = [];

                    return;
                }


                dashboardCart = parsed

                    .map(item => ({

                        id: item.id,

                        name: item.name,

                        price:
                            Number(item.price) || 0,

                        qty:
                            Number(item.qty) || 0,

                        stock:
                            Number(item.stock) || 0

                    }))

                    .filter(item =>
                        item.qty > 0 &&
                        item.stock > 0
                    );


            } catch (error) {

                console.error(
                    'Gagal memuat keranjang:',
                    error
                );

                dashboardCart = [];

            }

        }


        function saveDashboardCart() {

            try {

                localStorage.setItem(
                    CART_STORAGE_KEY,
                    JSON.stringify(dashboardCart)
                );

            } catch (error) {

                console.error(
                    'Gagal menyimpan keranjang:',
                    error
                );

            }

        }


        function updateFloatingCart() {

            const floatingCart =
                document.getElementById(
                    'floatingCart'
                );

            const totalElement =
                document.getElementById(
                    'floatingCartTotal'
                );

            const itemsElement =
                document.getElementById(
                    'floatingCartItems'
                );


            let total = 0;
            let itemCount = 0;


            dashboardCart.forEach(item => {

                total +=
                    Number(item.price) *
                    Number(item.qty);

                itemCount +=
                    Number(item.qty);

            });


            if (itemCount <= 0) {

                floatingCart.classList.add(
                    'hidden'
                );

                return;
            }


            floatingCart.classList.remove(
                'hidden'
            );


            totalElement.textContent =
                formatRupiah(total);


            itemsElement.textContent =
                itemCount + ' ' +
                (itemCount === 1 ? 'item' : 'item');

        }


        /* =============================================================
           QR SCANNER
        ============================================================= */

        let qrScanner = null;

        let scannerRunning = false;


        const scanUrlTemplate = @json(
            route('produk.scan', ['sku' => '__SKU__'])
        );


        function openQrScanner() {

            const modal =
                document.getElementById(
                    'qrScannerModal'
                );

            const status =
                document.getElementById(
                    'qrScannerStatus'
                );


            modal.classList.remove(
                'hidden'
            );

            modal.classList.add(
                'flex'
            );


            document.body.classList.add(
                'overflow-hidden'
            );


            status.textContent =
                'Memeriksa scanner...';


            if (
                typeof Html5Qrcode ===
                'undefined'
            ) {

                status.textContent =
                    'Library scanner belum berhasil dimuat.';

                console.error(
                    'Html5Qrcode tidak ditemukan.'
                );

                return;
            }


            if (scannerRunning) {

                return;
            }


            status.textContent =
                'Menyiapkan kamera...';


            try {

                qrScanner =
                    new Html5Qrcode(
                        'qr-reader'
                    );


                qrScanner.start(

                    {
                        facingMode:
                            'environment'
                    },

                    {
                        fps: 10,

                        qrbox: {
                            width: 250,
                            height: 150
                        }
                    },

                    function(decodedText) {

                        handleScannedSku(
                            decodedText
                        );

                    },

                    function() {

                        // Error scan sementara diabaikan

                    }

                )

                .then(function() {

                    scannerRunning = true;

                    status.textContent =
                        'Arahkan kamera ke QR / barcode produk.';

                })

                .catch(function(error) {

                    console.error(
                        'Gagal membuka kamera:',
                        error
                    );

                    status.textContent =
                        'Kamera gagal dibuka. Izinkan akses kamera di Chrome.';

                });


            } catch (error) {

                console.error(
                    'Scanner error:',
                    error
                );

                status.textContent =
                    'Scanner gagal dijalankan.';

            }

        }


        function closeQrScanner() {

            const modal =
                document.getElementById(
                    'qrScannerModal'
                );


            if (
                qrScanner &&
                scannerRunning
            ) {

                qrScanner.stop()

                    .then(function() {

                        qrScanner.clear();

                        scannerRunning =
                            false;

                        qrScanner =
                            null;

                    })

                    .catch(function(error) {

                        console.error(
                            'Gagal menghentikan scanner:',
                            error
                        );

                        scannerRunning =
                            false;

                        qrScanner =
                            null;

                    });

            }


            modal.classList.add(
                'hidden'
            );

            modal.classList.remove(
                'flex'
            );


            document.body.classList.remove(
                'overflow-hidden'
            );

        }


        function handleScannedSku(sku) {

            sku =
                String(sku).trim();


            if (!sku) {

                return;
            }


            closeQrScanner();


            fetch(

                scanUrlTemplate.replace(
                    '__SKU__',
                    encodeURIComponent(sku)
                )

            )

            .then(response => {

                return response.json()

                    .then(data => ({

                        ok:
                            response.ok,

                        data:
                            data

                    }));

            })

            .then(result => {

                if (!result.ok) {

                    alert(
                        result.data.message ||
                        'Produk tidak ditemukan.'
                    );

                    return;
                }


                const product =
                    result.data.product;


                openProductModal(

                    product.id,

                    product.name,

                    product.sku,

                    product.category || '',

                    product.brand || '',

                    product.price,

                    product.stock,

                    product.image || null

                );

            })

            .catch(error => {

                console.error(
                    'Gagal mencari produk:',
                    error
                );

                alert(
                    'Terjadi kesalahan saat mencari produk.'
                );

            });

        }


        /* =============================================================
           PRODUCT MODAL
        ============================================================= */

        function openProductModal(
            id,
            name,
            sku,
            category,
            brand,
            price,
            stock,
            image
        ) {

            selectedProduct = {

                id,

                name,

                sku,

                category,

                brand,

                price:
                    Number(price) || 0,

                stock:
                    Number(stock) || 0,

                image

            };


            modalQty = 1;


            document.getElementById(
                'modalProductName'
            ).textContent =
                name;


            document.getElementById(
                'modalProductSku'
            ).textContent =
                sku;


            document.getElementById(
                'modalProductCategory'
            ).textContent =
                category;


            const brandElement =
                document.getElementById(
                    'modalProductBrand'
                );


            if (brand) {

                brandElement.textContent =
                    brand;

                brandElement.classList.remove(
                    'hidden'
                );

            } else {

                brandElement.textContent =
                    '';

                brandElement.classList.add(
                    'hidden'
                );

            }


            document.getElementById(
                'modalProductPrice'
            ).textContent =
                formatRupiah(price);


            document.getElementById(
                'modalProductStock'
            ).textContent =
                stock + ' pcs';


            const imageElement =
                document.getElementById(
                    'modalProductImage'
                );


            const noImageElement =
                document.getElementById(
                    'modalNoImage'
                );


            if (image) {

                imageElement.src =
                    "{{ Storage::disk('s3')->url('') }}" +
                    image;

                imageElement.alt =
                    name;

                imageElement.classList.remove(
                    'hidden'
                );

                noImageElement.classList.add(
                    'hidden'
                );

            } else {

                imageElement.src =
                    '';

                imageElement.classList.add(
                    'hidden'
                );

                noImageElement.classList.remove(
                    'hidden'
                );

            }


            updateModalQty();


            const modal =
                document.getElementById(
                    'productModal'
                );


            modal.classList.remove(
                'hidden'
            );

            modal.classList.add(
                'flex'
            );


            document.body.classList.add(
                'overflow-hidden'
            );

        }


        function closeProductModal() {

            const modal =
                document.getElementById(
                    'productModal'
                );


            modal.classList.add(
                'hidden'
            );

            modal.classList.remove(
                'flex'
            );


            document.body.classList.remove(
                'overflow-hidden'
            );

        }


        /* =============================================================
           QUANTITY
        ============================================================= */

        function changeModalQty(change) {

            let newQty =
                modalQty + change;


            if (newQty < 1) {

                newQty = 1;

            }


            if (
                selectedProduct.stock > 0 &&
                newQty > selectedProduct.stock
            ) {

                newQty =
                    selectedProduct.stock;

            }


            modalQty =
                newQty;


            updateModalQty();

        }


        function updateModalQty() {

            document.getElementById(
                'modalQty'
            ).textContent =
                modalQty;


            document.getElementById(
                'modalTotal'
            ).textContent =

                formatRupiah(

                    selectedProduct.price *
                    modalQty

                );

        }


        /* =============================================================
           ADD TO CART
        ============================================================= */

        function addModalToCart() {

            if (
                !selectedProduct.id ||
                selectedProduct.stock <= 0
            ) {

                return;
            }


            const existingIndex =
                dashboardCart.findIndex(

                    item =>
                        String(item.id) ===
                        String(selectedProduct.id)

                );


            if (existingIndex !== -1) {

                const newQty =
                    dashboardCart[
                        existingIndex
                    ].qty +
                    modalQty;


                if (
                    newQty >
                    selectedProduct.stock
                ) {

                    alert(
                        'Jumlah melebihi stok tersedia.'
                    );

                    return;
                }


                dashboardCart[
                    existingIndex
                ].qty =
                    newQty;


            } else {

                dashboardCart.push({

                    id:
                        selectedProduct.id,

                    name:
                        selectedProduct.name,

                    price:
                        selectedProduct.price,

                    qty:
                        modalQty,

                    stock:
                        selectedProduct.stock

                });

            }


            saveDashboardCart();

            updateFloatingCart();

            closeProductModal();

        }


        /* =============================================================
           KASIR
        ============================================================= */

        function goToKasir() {

            window.location.href =
                "{{ route('kasir.index') }}";

        }


        /* =============================================================
           MODAL INTERACTION
        ============================================================= */

        document.getElementById(
            'productModal'
        ).addEventListener(

            'click',

            function(event) {

                if (event.target === this) {

                    closeProductModal();

                }

            }

        );


        document.addEventListener(

            'keydown',

            function(event) {

                if (event.key === 'Escape') {

                    closeProductModal();

                }

            }

        );


        /* =============================================================
           INIT
        ============================================================= */

        document.addEventListener(

            'DOMContentLoaded',

            function() {

                loadDashboardCart();

                updateFloatingCart();

            }

        );

    </script>

@endsection