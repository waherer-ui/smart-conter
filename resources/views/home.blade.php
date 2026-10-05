@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', '🏠')
@section('mobile_action', 'scan')


{{-- =============================================================
     DASHBOARD CSS
     Diletakkan SEBELUM HTML dashboard agar tidak terjadi
     flash HTML polos saat halaman sedang reload.
============================================================== --}}

<style>

    /* =========================================================
       SEARCH
    ========================================================= */

    .dashboard-search {
        display: flex;
        align-items: center;
        gap: 6px;
        width: 100%;
    }

    .dashboard-search-input {
        position: relative;
        flex: 1;
        min-width: 0;
    }

    .dashboard-search-input input {
        width: 100%;
        height: 40px;
        padding: 0 12px 0 36px;
        box-sizing: border-box;

        background: rgba(17, 24, 39, .92);
        border: 1px solid rgba(255, 255, 255, .08);
        border-radius: 12px;

        color: white;
        font-size: 12px;
        outline: none;
    }

    .dashboard-search-input input:focus {
        border-color: rgba(99, 102, 241, .55);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .08);
    }

    .dashboard-search-input input::placeholder {
        color: #6b7280;
    }

    .dashboard-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);

        color: #6b7280;
        font-size: 18px;
        z-index: 2;
    }

    .dashboard-search-scan,
    .dashboard-search-submit {
        height: 40px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, .08);

        display: flex;
        align-items: center;
        justify-content: center;

        transition: .18s ease;
    }

    .dashboard-search-scan {
        width: 40px;
        flex-shrink: 0;

        background: rgba(31, 41, 55, .95);
        color: white;
        font-size: 16px;
    }

    .dashboard-search-submit {
        padding: 0 13px;
        flex-shrink: 0;

        background: #4f46e5;
        color: white;

        font-size: 11px;
        font-weight: 700;
    }

    .dashboard-search-scan:active,
    .dashboard-search-submit:active {
        transform: scale(.94);
    }


    /* =========================================================
       DASHBOARD
    ========================================================= */

    .dashboard-mobile {
        width: 100%;
        padding-bottom: 7rem;
    }


    /* =========================================================
       HERO
    ========================================================= */

    .dashboard-hero {
        position: relative;
        overflow: hidden;

        padding: 12px;

        border-radius: 20px;

        background:
            linear-gradient(
                145deg,
                rgba(17, 24, 39, .98),
                rgba(15, 23, 42, .92)
            );

        border: 1px solid rgba(255, 255, 255, .08);

        box-shadow:
            0 18px 45px rgba(0, 0, 0, .22);

        margin-bottom: 8px !important;
    }

    .dashboard-hero-glow {
        position: absolute;
        border-radius: 999px;
        filter: blur(35px);
        pointer-events: none;
    }

    .dashboard-hero-glow-1 {
        width: 120px;
        height: 120px;

        top: -55px;
        right: -35px;

        background: rgba(16, 185, 129, .14);
    }

    .dashboard-hero-glow-2 {
        width: 120px;
        height: 120px;

        bottom: -65px;
        left: -45px;

        background: rgba(132, 204, 22, .10);
    }


    /* =========================================================
       STORE / HERO
    ========================================================= */

    .store-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 6px 9px;

        border-radius: 999px;

        background: rgba(255, 255, 255, .06);
        border: 1px solid rgba(255, 255, 255, .08);

        color: #d1d5db;

        font-size: 9px;
        font-weight: 700;
    }

    .store-status-dot {
        width: 6px;
        height: 6px;

        border-radius: 999px;

        background: #34d399;

        box-shadow: 0 0 8px rgba(52, 211, 153, .7);

        animation: storePulse 2s infinite;
    }

    @keyframes storePulse {
        0%, 100% {
            opacity: 1;
        }

        50% {
            opacity: .45;
        }
    }

    .hero-package-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        padding: 7px 10px;

        border-radius: 10px;

        background: rgba(16, 185, 129, .92);

        color: #07130e;

        font-size: 9px;
        font-weight: 800;

        box-shadow:
            0 7px 18px rgba(16, 185, 129, .15);
    }

    .hero-title {
        margin-top: 14px;

        color: white;

        font-size: 19px;
        line-height: 1.25;

        font-weight: 800;
        letter-spacing: -.025em;
    }

    .hero-title span {
        color: #f8fafc;
    }

    .hero-description {
        margin-top: 7px;

        max-width: 500px;

        color: #9ca3af;

        font-size: 10px;
        line-height: 1.6;
    }

    .hero-description img {
        height: 16px;
        width: auto;

        display: inline-block;

        vertical-align: middle;
    }


    /* =========================================================
       PREMIUM STRIP
    ========================================================= */

    .hero-premium {
        margin-top: 15px;
        padding-top: 12px;

        border-top: 1px solid rgba(255, 255, 255, .08);
    }

    .hero-premium-title {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;

        color: white;

        font-size: 9px;
        font-weight: 800;
    }

    .premium-badge {
        padding: 3px 7px;

        border-radius: 999px;

        background: rgba(251, 191, 36, .12);
        border: 1px solid rgba(251, 191, 36, .16);

        color: #fcd34d;

        font-size: 7px;
    }

    .hero-marquee-wrapper {
        overflow: hidden;
        margin-top: 9px;
    }

    .dashboard-marquee {
        width: max-content;

        display: flex;
        align-items: center;
        gap: 24px;

        white-space: nowrap;

        color: #9ca3af;

        font-size: 9px;

        animation:
            dashboardMarquee 24s linear infinite;
    }

    @keyframes dashboardMarquee {
        from {
            transform: translateX(100%);
        }

        to {
            transform: translateX(-100%);
        }
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
       HERO PACKAGE CARD
    ========================================================= */

    .hero-package-card {
        margin-top: 18px;
        padding: 15px;

        border-radius: 20px;

        background:
            linear-gradient(
                145deg,
                rgba(255, 255, 255, .10),
                rgba(255, 255, 255, .045)
            );

        border: 1px solid rgba(255, 255, 255, .10);

        box-shadow:
            0 14px 35px rgba(0, 0, 0, .18),
            inset 0 1px 0 rgba(255, 255, 255, .05);

        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }

    .hero-package-top {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 12px;
    }

    .hero-package-label {
        font-size: 14px;
        font-weight: 800;

        color: #fff;

        letter-spacing: .2px;
    }

    .hero-package-expiry {
        display: flex;
        align-items: center;
        gap: 6px;

        margin-top: 4px;

        font-size: 11px;
        color: rgba(255, 255, 255, .65);
    }

    .hero-active-dot {
        width: 6px;
        height: 6px;

        border-radius: 50%;

        background: #22c55e;

        box-shadow:
            0 0 8px rgba(34, 197, 94, .7);
    }

    .hero-package-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        padding: 8px 11px;

        border-radius: 11px;

        font-size: 11px;
        font-weight: 800;

        color: #fff;

        background: rgba(255, 255, 255, .09);
        border: 1px solid rgba(255, 255, 255, .10);

        text-decoration: none;

        white-space: nowrap;

        transition: .2s ease;
    }

    .hero-package-action:hover {
        background: rgba(255, 255, 255, .15);

        transform: translateY(-1px);
    }

    .hero-package-status {
        display: flex;
        align-items: center;
        gap: 10px;

        margin-top: 13px;
        padding: 11px 12px;

        border-radius: 14px;

        background: rgba(0, 0, 0, .14);
    }

    .hero-package-status > span {
        font-size: 20px;
        line-height: 1;
    }

    .hero-package-status strong {
        display: block;

        font-size: 12px;
        font-weight: 800;

        color: #fff;
    }

    .hero-package-status small {
        display: block;

        margin-top: 2px;

        font-size: 10px;
        line-height: 1.4;

        color: rgba(255, 255, 255, .58);
    }


    /* =========================================================
       FEATURE SLIDER
    ========================================================= */

    .hero-feature-slider {
        margin-top: 12px;

        overflow: hidden;
    }

    .hero-feature-track {
        position: relative;

        min-height: 52px;
    }

    .hero-feature-slide {
        display: none;
        align-items: center;
        gap: 10px;

        min-height: 52px;

        animation:
            heroFeatureIn .35s ease;
    }

    .hero-feature-slide.active {
        display: flex;
    }

    .hero-feature-icon {
        width: 36px;
        height: 36px;

        flex: 0 0 36px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 11px;

        background: rgba(255, 255, 255, .08);

        font-size: 17px;
    }

    .hero-feature-slide strong {
        display: block;

        font-size: 12px;
        font-weight: 800;

        color: #fff;
    }

    .hero-feature-slide small {
        display: block;

        margin-top: 2px;

        font-size: 10px;
        line-height: 1.35;

        color: rgba(255, 255, 255, .55);
    }

    .hero-feature-dots {
        display: flex;
        justify-content: center;
        gap: 5px;

        margin-top: 8px;
    }

    .hero-feature-dots span {
        width: 5px;
        height: 5px;

        border-radius: 50%;

        background: rgba(255, 255, 255, .20);

        transition: .25s ease;
    }

    .hero-feature-dots span.active {
        width: 14px;

        border-radius: 999px;

        background: rgba(255, 255, 255, .75);
    }

    @keyframes heroFeatureIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    /* =========================================================
       DASHBOARD MENU BOX
    ========================================================= */

    .dashboard-menu-box {
        min-height: 88px;

        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        gap: 7px;

        padding: 12px 6px;

        border-radius: 16px;

        background: rgba(255, 255, 255, 0.075);

        border: 1px solid rgba(255, 255, 255, 0.10);

        color: white;

        text-decoration: none;

        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);

        transition:
            transform 0.18s ease,
            background 0.18s ease,
            border-color 0.18s ease;
    }

    .dashboard-menu-box:hover {
        background: rgba(255, 255, 255, 0.12);

        border-color: rgba(255, 255, 255, 0.18);

        transform: translateY(-2px);
    }

    .dashboard-menu-box:active {
        transform: scale(0.96);
    }

    .dashboard-menu-icon {
        width: 42px;
        height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 13px;

        background: rgba(255, 255, 255, 0.10);

        font-size: 22px;

        line-height: 1;
    }

    .dashboard-menu-label {
        font-size: 11px;
        font-weight: 600;

        color: rgba(255, 255, 255, 0.90);

        line-height: 1.2;

        text-align: center;

        white-space: nowrap;
    }


    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 639px) {

        .dashboard-menu-box {
            min-height: 82px;

            padding: 10px 4px;

            border-radius: 15px;
        }

        .dashboard-menu-icon {
            width: 38px;
            height: 38px;

            font-size: 20px;

            border-radius: 12px;
        }

        .dashboard-menu-label {
            font-size: 10.5px;
        }

        .hero-package-card {
            margin-top: 14px;
            padding: 13px;

            border-radius: 18px;
        }

        .hero-package-label {
            font-size: 13px;
        }

        .hero-package-action {
            padding: 7px 9px;

            font-size: 10px;
        }

        .hero-package-status {
            margin-top: 11px;
            padding: 10px;

            border-radius: 13px;
        }

        .hero-package-status strong {
            font-size: 11px;
        }

        .hero-package-status small {
            font-size: 9px;
        }

        .hero-feature-slide {
            min-height: 48px;
        }

        .hero-feature-icon {
            width: 33px;
            height: 33px;

            flex-basis: 33px;

            font-size: 15px;
        }

        .hero-feature-slide strong {
            font-size: 11px;
        }

        .hero-feature-slide small {
            font-size: 9px;
        }

        #productModal {
            align-items: flex-end !important;
            justify-content: center !important;
            padding: 12px !important;
        }

        .dashboard-product-modal {
            width: 100% !important;
            max-width: 430px !important;

            max-height: 68dvh !important;
            min-height: auto !important;

            border-radius: 26px !important;

            display: flex;
            flex-direction: column;

            margin: 0 auto;

            position: relative;

            overflow-y: auto;

            padding-bottom: env(safe-area-inset-bottom);

            box-shadow:
                0 25px 70px rgba(0, 0, 0, .55),
                0 0 0 1px rgba(255, 255, 255, .05);
        }

        .dashboard-product-modal::before {
            content: "";

            width: 42px;
            height: 4px;

            border-radius: 999px;

            background: rgba(255, 255, 255, .18);

            position: absolute;

            top: 9px;
            left: 50%;

            transform: translateX(-50%);

            z-index: 20;
        }

        .dashboard-product-modal img {
            max-height: 135px !important;

            object-fit: contain !important;
        }

        .dashboard-product-modal .p-5,
        .dashboard-product-modal .p-6 {
            padding: 14px !important;
        }

        .dashboard-product-modal h2,
        .dashboard-product-modal h3 {
            font-size: 16px !important;
            line-height: 1.3 !important;
        }

        .dashboard-product-modal .text-xl,
        .dashboard-product-modal .text-2xl {
            font-size: 19px !important;
        }

        .dashboard-product-modal .space-y-6 {
            gap: 12px !important;
        }

        .dashboard-product-modal .space-y-4 {
            gap: 10px !important;
        }

        .dashboard-product-modal button {
            min-height: 40px;
        }

        .dashboard-product-modal button[type="button"] {
            border-radius: 14px;
        }
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

        .dashboard-hero {
            padding: 24px;
        }

        .hero-title {
            font-size: 26px;
        }

        .hero-description {
            font-size: 13px;
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

</style>


@section('header_tools')

    {{-- =========================================================
         MOBILE SEARCH
    ========================================================== --}}

    <div class="mt-1 w-full">

        <form
            action="{{ route('dashboard') }}"
            method="GET"
            class="dashboard-search"
        >

            <div class="dashboard-search-input">

                <span class="dashboard-search-icon">
                    ⌕
                </span>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari produk atau SKU..."
                    autocomplete="off"
                >

            </div>


            <button
                type="button"
                onclick="openQrScanner()"
                class="dashboard-search-scan"
                aria-label="Scan QR / Barcode"
            >
                📷
            </button>


            <button
                type="submit"
                class="dashboard-search-submit"
            >
                Cari
            </button>

        </form>

    </div>

@endsection


@section('content')

    <div class="dashboard-mobile">

        {{-- =========================================================
             HERO / MENU
        ========================================================== --}}

        <section class="dashboard-hero">

            <div class="dashboard-hero-glow dashboard-hero-glow-1"></div>
            <div class="dashboard-hero-glow dashboard-hero-glow-2"></div>


            {{-- =====================================================
                 MENU GRID
            ====================================================== --}}

            <div class="grid grid-cols-4 gap-2.5 sm:gap-3">

                {{-- KASIR --}}
                <a
                    href="{{ route('kasir.index') }}"
                    class="dashboard-menu-box"
                >

                    <span class="dashboard-menu-icon">
                        🛒
                    </span>

                    <span class="dashboard-menu-label">
                        Kasir
                    </span>

                </a>


                {{-- PRODUK --}}
                <a
                    href="{{ route('produk.index') }}"
                    class="dashboard-menu-box"
                >

                    <span class="dashboard-menu-icon">
                        📦
                    </span>

                    <span class="dashboard-menu-label">
                        Produk
                    </span>

                </a>


                {{-- ABSEN --}}
                <a
                    href="{{ route('attendance.index') }}"
                    class="dashboard-menu-box"
                >

                    <span class="dashboard-menu-icon">
                        🕘
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

                    <span class="dashboard-menu-icon">
                        🚚
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

                    <span class="dashboard-menu-icon">
                        💰
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

                    <span class="dashboard-menu-icon">
                        👥
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

                    <span class="dashboard-menu-icon">
                        🔄
                    </span>

                    <span class="dashboard-menu-label">
                        Transfer
                    </span>

                </a>


                <a
                    href="{{ route('payment-settings.index') }}"
                    class="dashboard-menu-box"
                >

                    <span class="dashboard-menu-icon">
                        💳
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