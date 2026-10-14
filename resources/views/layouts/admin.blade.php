{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - EMS</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/assets/icon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Nunito:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Check initial sidebar collapsed state before render to avoid flash
        if (window.innerWidth > 960 && localStorage.getItem('ems_sidebar_closed') === 'true') {
            document.documentElement.classList.add('sidebar-closed');
            document.addEventListener('DOMContentLoaded', function() {
                document.body.classList.add('sidebar-closed');
            });
        }
    </script>

    <style>
        /* ══════════════════════════════════════
           CSS VARIABLES — Vertical Garden Palette
        ══════════════════════════════════════ */
        :root {
            --gd: #0d3b1a;
            /* deep forest */
            --gk: #145a2c;
            /* dark green */
            --gm: #1e7c42;
            /* mid green */
            --gb: #27a355;
            /* bright green */
            --gl: #7ed53a;
            /* lime accent */
            --gs: #eaf6ed;
            /* soft mint bg */
            --gw: #f4fbf6;
            /* off-white bg */
            --gold: #c9a84c;
            --gold-l: #f0d080;
            --white: #ffffff;
            --txt: #0f2b18;
            --txt-m: #4a7a5a;
            --sb-w: 268px;
            --r-pill: 50px;
            --r-lg: 28px;
            --r-md: 18px;
            --r-sm: 12px;
            --tr: all 0.3s cubic-bezier(.4, 0, .2, 1);
            --glass: rgba(255, 255, 255, 0.58);
            --glass-b: rgba(255, 255, 255, 0.75);
            --shadow: 0 8px 40px rgba(13, 59, 26, 0.12);
            --blur: blur(20px);
        }

        /* ══════════════════════════════════════
           RESET & BASE
        ══════════════════════════════════════ */
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: var(--gw);
            min-height: 100vh;
            overflow-x: hidden;
            color: var(--txt);
        }

        /* ── Organic background blobs ── */
        body::before {
            content: '';
            position: fixed;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(126, 213, 58, .12) 0%, transparent 65%);
            top: -200px;
            right: -200px;
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(39, 163, 85, .10) 0%, transparent 65%);
            bottom: -150px;
            left: 80px;
            pointer-events: none;
            z-index: 0;
        }

        /* ══════════════════════════════════════
           SIDEBAR — Premium Light Theme
        ══════════════════════════════════════ */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sb-w);
            z-index: 1050;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: var(--tr);

            background: var(--white);
            border-right: 1px solid rgba(13, 59, 26, 0.05);
            box-shadow: 10px 0 40px rgba(13, 59, 26, 0.04);
        }

        /* Lime top stripe */
        .sidebar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gl), var(--gb), var(--gl));
            z-index: 2;
        }

        /* Subtle organic blob inside sidebar — softer on white */
        .sidebar::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(126, 213, 58, .04) 0%, transparent 70%);
            bottom: -80px;
            right: -80px;
            pointer-events: none;
        }

        /* ── Brand Header ── */
        .sidebar-header {
            padding: 1.4rem 1.4rem 1.2rem;
            border-bottom: 1px solid rgba(13, 59, 26, 0.05);
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-wrap {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .brand-text h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--gd);
            line-height: 1.2;
            letter-spacing: -0.2px;
            margin-bottom: 0.35rem;
        }

        .brand-text span {
            font-size: 0.68rem;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--gm);
            opacity: 0.8;
            font-weight: 700;
        }

        /* ── Menu Labels ── */
        .menu-section-label {
            font-size: .6rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(13, 59, 26, 0.4);
            padding: 1rem 1.7rem .4rem;
            font-weight: 800;
            display: block;
        }

        /* ── Sidebar Menu ── */
        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
            position: relative;
            z-index: 1;
            scrollbar-width: thin;
            scrollbar-color: rgba(126, 213, 58, .2) transparent;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(30, 124, 66, .1);
            border-radius: 4px;
        }

        .sidebar-menu .nav-link {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .7rem 1.2rem;
            margin: .15rem 1rem;
            border-radius: var(--r-pill);
            color: var(--txt-m);
            font-size: .88rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--tr);
            position: relative;
        }

        .sidebar-menu .nav-link .nav-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(13, 59, 26, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            flex-shrink: 0;
            transition: var(--tr);
            color: var(--gk);
            border: 1px solid rgba(13, 59, 26, 0.05);
        }

        .sidebar-menu .nav-link:hover {
            color: var(--gd);
            background: rgba(126, 213, 58, .08);
        }

        .sidebar-menu .nav-link:hover .nav-icon {
            background: var(--gl);
            border-color: var(--gl);
            color: var(--gd);
        }

        /* Active — premium lime highlight */
        .sidebar-menu .nav-link.active {
            color: var(--gd);
            background: linear-gradient(135deg, var(--gl), #6ec22f);
            box-shadow: 0 4px 15px rgba(126, 213, 58, .3);
            font-weight: 700;
        }

        .sidebar-menu .nav-link.active .nav-icon {
            background: rgba(13, 59, 26, .1);
            border-color: rgba(13, 59, 26, .1);
            color: var(--gd);
        }

        .nav-badge {
            margin-left: auto;
            background: var(--gd);
            color: var(--gl);
            font-size: .62rem;
            font-weight: 800;
            padding: .15rem .55rem;
            border-radius: 20px;
        }

        .sidebar-menu .nav-link.active .nav-badge {
            background: rgba(13, 59, 26, .15);
            color: var(--gd);
        }

        .sidebar-divider {
            height: 1px;
            background: rgba(13, 59, 26, 0.05);
            margin: .75rem 1.5rem;
        }

        /* ── Sidebar Footer ── */
        .sidebar-footer {
            padding: 1rem 1.5rem 1.5rem;
            border-top: 1px solid rgba(13, 59, 26, 0.05);
            position: relative;
            z-index: 1;
        }

        .logout-link {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .65rem 1.2rem;
            border-radius: var(--r-pill);
            color: #d32f2f;
            font-size: .88rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--tr);
            background: rgba(211, 47, 47, 0.04);
            border: 1px solid rgba(211, 47, 47, 0.05);
        }

        .logout-link:hover {
            background: #d32f2f;
            color: var(--white);
            box-shadow: 0 4px 12px rgba(211, 47, 47, .2);
        }

        .logout-link .nav-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(211, 47, 47, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .82rem;
        }

        /* ══════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════ */
        .main-content {
            margin-left: var(--sb-w);
            min-height: 100vh;
            padding: 1.5rem 1.8rem;
            position: relative;
            transition: var(--tr);
        }

        /* ══════════════════════════════════════
           TOPBAR — Glass pill
        ══════════════════════════════════════ */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .75rem 1.25rem .75rem 1.5rem;
            margin-bottom: 1.8rem;
            border-radius: var(--r-pill);
            background: var(--glass);
            backdrop-filter: var(--blur);
            -webkit-backdrop-filter: var(--blur);
            border: 1.5px solid var(--glass-b);
            box-shadow: var(--shadow);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .menu-toggle {
            display: flex;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1.5px solid rgba(30, 124, 66, .2);
            background: rgba(255, 255, 255, 0.75);
            color: var(--gm);
            font-size: 1.05rem;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: var(--tr);
            box-shadow: 0 2px 8px rgba(13, 59, 26, 0.06);
        }

        .menu-toggle:hover {
            background: var(--gl);
            color: var(--gd);
            border-color: var(--gl);
            transform: scale(1.05);
        }

        .sidebar-close-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 1px solid rgba(13, 59, 26, 0.1);
            background: rgba(13, 59, 26, 0.04);
            color: var(--txt-m);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--tr);
            flex-shrink: 0;
            font-size: 0.85rem;
        }

        .sidebar-close-btn:hover {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.3);
            transform: rotate(90deg);
        }

        /* ── Sidebar Closed / Collapsed State (Desktop) ── */
        body.sidebar-closed .sidebar,
        html.sidebar-closed .sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-closed .main-content,
        html.sidebar-closed .main-content {
            margin-left: 0 !important;
        }

        /* ── Sidebar Mobile Backdrop ── */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(13, 59, 26, 0.35);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .sidebar-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .page-breadcrumb .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--gd);
            line-height: 1.1;
        }

        .page-breadcrumb .page-subtitle {
            font-size: .72rem;
            color: var(--txt-m);
            margin-top: .1rem;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .topbar-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1.5px solid rgba(30, 124, 66, .15);
            background: rgba(255, 255, 255, .6);
            color: var(--txt-m);
            font-size: .9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--tr);
            position: relative;
        }

        .topbar-btn:hover {
            background: var(--gs);
            border-color: var(--gl);
            color: var(--gm);
            transform: translateY(-2px);
        }

        .topbar-btn .dot {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--gl);
            border: 1.5px solid white;
        }

        /* User chip — pill */
        .user-chip {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .35rem .35rem .35rem .9rem;
            border-radius: var(--r-pill);
            background: rgba(255, 255, 255, .75);
            border: 1.5px solid rgba(30, 124, 66, .15);
            cursor: pointer;
            transition: var(--tr);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e7c42, #0d3b1a);
            color: #ffffff;
            font-size: .78rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            letter-spacing: .5px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(13, 59, 26, 0.15);
        }

        /* ── Dropdown / Submenu Styling ── */
        .submenu-list {
            padding-left: 0.8rem;
            margin-bottom: 0.5rem;
            list-style: none;
            border-left: 1.5px dashed rgba(13, 59, 26, 0.08);
            margin-left: 2.2rem;
        }

        .sub-link {
            font-size: .82rem !important;
            padding: .55rem 1rem !important;
            margin: .1rem .5rem !important;
            background: transparent !important;
            box-shadow: none !important;
            color: var(--txt-m) !important;
            font-weight: 500 !important;
        }

        .sub-link:hover {
            color: var(--gd) !important;
            background: rgba(126, 213, 58, .08) !important;
        }

        .nav-icon-sm {
            font-size: 0.72rem;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(13, 59, 26, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--txt-m);
            transition: var(--tr);
            border: 1px solid rgba(13, 59, 26, 0.05);
        }

        .sub-link.active {
            color: var(--gd) !important;
            font-weight: 700 !important;
            background: rgba(126, 213, 58, .12) !important;
        }

        .sub-link.active .nav-icon-sm {
            color: var(--gd);
            background: var(--gl);
            border-color: var(--gl);
        }

        .submenu-arrow {
            font-size: 0.6rem;
            transition: transform 0.3s ease;
            opacity: 0.4;
            margin-left: auto;
            color: var(--gd);
        }

        .nav-link[aria-expanded="true"] .submenu-arrow {
            transform: rotate(90deg);
            opacity: 0.8;
            color: var(--gm);
        }

        /* Fix for active parent link color when collapsed */
        .nav-link[data-bs-toggle="collapse"].active {
            background: linear-gradient(135deg, var(--gl), #6ec22f);
            color: var(--gd);
            box-shadow: 0 4px 15px rgba(126, 213, 58, .2);
            font-weight: 700;
        }

        .nav-link[data-bs-toggle="collapse"] {
            cursor: pointer;
        }

        .user-chip:hover {
            background: var(--gs);
            border-color: var(--gl);
        }

        .user-chip .u-name {
            font-size: .82rem;
            font-weight: 700;
            color: var(--gk);
            line-height: 1.1;
        }

        .user-chip .u-role {
            font-size: .67rem;
            color: var(--txt-m);
            display: block;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gl), var(--gm));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gd);
            font-size: .78rem;
            font-weight: 800;
            box-shadow: 0 2px 10px rgba(126, 213, 58, .4);
            flex-shrink: 0;
        }

        /* ══════════════════════════════════════
           CARDS — Organic glass
        ══════════════════════════════════════ */
        .card {
            border: none;
            border-radius: var(--r-lg);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            transition: var(--tr);
            background: var(--glass);
            backdrop-filter: var(--blur);
            -webkit-backdrop-filter: var(--blur);
            border: 1.5px solid var(--glass-b);
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(13, 59, 26, .15);
            border-color: rgba(126, 213, 58, .3);
        }

        .card-header {
            background: rgba(255, 255, 255, .45);
            border-bottom: 1.5px solid rgba(126, 213, 58, .15);
            padding: 1rem 1.5rem;
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gd);
            border-top-left-radius: calc(var(--r-lg) - 1.5px) !important;
            border-top-right-radius: calc(var(--r-lg) - 1.5px) !important;
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .card-footer {
            border-bottom-left-radius: calc(var(--r-lg) - 1.5px) !important;
            border-bottom-right-radius: calc(var(--r-lg) - 1.5px) !important;
        }

        /* Prevent dropdowns from being clipped or overlapped */
        .card:has(.dropdown-menu.show),
        .card.dropdown-active {
            z-index: 1050 !important;
        }

        .dropdown-menu {
            z-index: 1060 !important;
        }

        .card-header .header-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gl), #5ab020);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gd);
            font-size: .72rem;
        }

        /* ══════════════════════════════════════
           STATS CARDS — Organic blobs
        ══════════════════════════════════════ */
        .stats-card {
            border-radius: var(--r-lg);
            padding: 1.5rem;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--tr);
            position: relative;
            overflow: hidden;
            border: 1.5px solid rgba(255, 255, 255, .15);
            background: linear-gradient(135deg, var(--gm) 0%, var(--gk) 100%);
            box-shadow: 0 8px 32px rgba(13, 59, 26, .2), inset 0 1px 0 rgba(255, 255, 255, .12);
        }

        /* Organic blob decoration */
        .stats-card::before {
            content: '';
            position: absolute;
            right: -35px;
            top: -35px;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: rgba(126, 213, 58, .15);
        }

        .stats-card::after {
            content: '';
            position: absolute;
            left: -20px;
            bottom: -40px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .06);
        }

        .stats-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(13, 59, 26, .28);
        }

        .stats-card.lime-card {
            background: linear-gradient(135deg, #5ab020 0%, #3d8010 100%);
        }

        .stats-card.teal-card {
            background: linear-gradient(135deg, #009e7a 0%, #007058 100%);
        }

        .stats-card.gold-card {
            background: linear-gradient(135deg, #b8920a 0%, #8a6a00 100%);
        }

        .stats-icon {
            font-size: 2.5rem;
            color: var(--gl);
            opacity: .95;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, .2));
        }

        .stats-card.lime-card .stats-icon,
        .stats-card.teal-card .stats-icon {
            color: rgba(255, 255, 255, .9);
        }

        .stats-info {
            position: relative;
            z-index: 1;
            text-align: right;
        }

        .stats-info h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2.1rem;
            margin: 0;
            font-weight: 700;
            color: var(--gl);
            line-height: 1;
        }

        .stats-card.lime-card .stats-info h3,
        .stats-card.teal-card .stats-info h3,
        .stats-card.gold-card .stats-info h3 {
            color: rgba(255, 255, 255, .95);
        }

        .stats-info p {
            margin: .3rem 0 0;
            font-size: .78rem;
            color: rgba(255, 255, 255, .78);
            letter-spacing: .3px;
        }

        .stats-trend {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            margin-top: .5rem;
            background: rgba(255, 255, 255, .14);
            padding: .2rem .65rem;
            border-radius: 20px;
            font-size: .68rem;
            color: rgba(255, 255, 255, .9);
        }

        .stats-trend.up {
            background: rgba(126, 213, 58, .25);
        }

        /* ══════════════════════════════════════
           TABLE — Premium Clean Design
        ══════════════════════════════════════ */
        .table {
            border-radius: var(--r-md);
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: #f8fafc !important;
            color: #475569 !important;
            border-bottom: 1.5px solid #e2e8f0 !important;
            border-top: none;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: .9rem 1.1rem;
            white-space: nowrap;
        }

        .table tbody td {
            padding: .95rem 1.1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: .865rem;
            color: #1e293b;
            transition: background 0.15s ease;
        }

        .table-hover tbody tr:hover td {
            background: #f8fafc !important;
        }

        /* ══════════════════════════════════════
           MODERN SAAS ACTION BUTTONS
        ══════════════════════════════════════ */
        .btn-action-group {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .82rem;
            border: 1px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none !important;
            cursor: pointer;
            background: #f8fafc;
            color: #64748b;
            padding: 0;
            line-height: 1;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .btn-action-view {
            background: rgba(37, 99, 235, 0.08);
            color: #2563eb;
            border-color: rgba(37, 99, 235, 0.18);
        }

        .btn-action-view:hover {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.28);
        }

        .btn-action-edit {
            background: rgba(79, 70, 229, 0.08);
            color: #4f46e5;
            border-color: rgba(79, 70, 229, 0.18);
        }

        .btn-action-edit:hover {
            background: #4f46e5;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.28);
        }

        .btn-action-call {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            border-color: rgba(16, 185, 129, 0.25);
        }

        .btn-action-call:hover {
            background: #059669;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }

        .btn-action-whatsapp {
            background: rgba(37, 211, 102, 0.14);
            color: #16a34a;
            border-color: rgba(37, 211, 102, 0.28);
        }

        .btn-action-whatsapp:hover {
            background: #22c55e;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.35);
        }

        .btn-action-delete {
            background: rgba(239, 68, 68, 0.08);
            color: #dc2626;
            border-color: rgba(239, 68, 68, 0.18);
        }

        .btn-action-delete:hover {
            background: #dc2626;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.28);
        }

        /* ══════════════════════════════════════
           BUTTONS — Pill style from reference
        ══════════════════════════════════════ */
        .btn {
            border-radius: var(--r-pill);
            padding: .55rem 1.4rem;
            font-size: .84rem;
            font-weight: 700;
            transition: var(--tr);
            letter-spacing: .2px;
            font-family: 'Nunito', sans-serif;
        }

        .btn-xs {
            padding: .25rem .8rem;
            font-size: .74rem;
            border-radius: var(--r-pill);
        }

        .bg-light-soft {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gm), var(--gk));
            border: none;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(30, 124, 66, .3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--gk), var(--gd));
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(30, 124, 66, .4);
            color: #ffffff;
        }

        /* Lime CTA */
        .btn-success {
            background: linear-gradient(135deg, var(--gl), #5ab020);
            border: none;
            color: var(--gd);
            font-weight: 800;
            box-shadow: 0 4px 18px rgba(126, 213, 58, .35);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #5ab020, #3d8010);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(126, 213, 58, .45);
            color: var(--gd);
        }

        .btn-outline-primary {
            border: 2px solid rgba(30, 124, 66, .35);
            color: var(--gm);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--gs);
            border-color: var(--gl);
            color: var(--gk);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            box-shadow: 0 4px 14px rgba(220, 38, 38, .3);
        }

        /* ══════════════════════════════════════
           BADGES — Pill shaped & Soft tints
        ══════════════════════════════════════ */
        .badge {
            padding: .35rem .85rem;
            border-radius: var(--r-pill);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .2px;
            display: inline-flex;
            align-items: center;
        }

        .badge-soft-primary { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-soft-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-soft-warning { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-soft-danger  { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-soft-info    { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-soft-purple  { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .badge-soft-teal    { background: #f0fdfa; color: #0f766e; border: 1px solid #99f6e4; }
        .badge-soft-neutral { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

        .badge-lime {
            background: rgba(126, 213, 58, .15);
            color: #2d6800;
            border: 1.5px solid rgba(126, 213, 58, .3);
        }

        .badge-green {
            background: rgba(39, 163, 85, .12);
            color: var(--gk);
            border: 1.5px solid rgba(39, 163, 85, .25);
        }

        .badge-gold {
            background: rgba(201, 168, 76, .15);
            color: #6b4c00;
            border: 1.5px solid rgba(201, 168, 76, .3);
        }

        .badge-red {
            background: rgba(220, 38, 38, 0.1);
            color: #991b1b;
            border: 1.5px solid rgba(220, 38, 38, 0.2);
        }

        .badge-blue,
        .badge-info {
            background: rgba(14, 165, 233, 0.1);
            color: #0369a1;
            border: 1.5px solid rgba(14, 165, 233, 0.2);
        }

        .badge-gray,
        .badge-secondary {
            background: rgba(107, 114, 128, 0.1);
            color: #374151;
            border: 1.5px solid rgba(107, 114, 128, 0.2);
        }

        /* ══════════════════════════════════════
           FORM CONTROLS
        ══════════════════════════════════════ */
        .form-control,
        .form-select {
            border-radius: var(--r-pill);
            border: 1.5px solid rgba(126, 213, 58, .25);
            padding: .6rem 1.2rem;
            font-family: 'Nunito', sans-serif;
            font-size: .875rem;
            color: var(--txt);
            background: rgba(255, 255, 255, .8);
            transition: var(--tr);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--gl);
            box-shadow: 0 0 0 4px rgba(126, 213, 58, .15);
            background: var(--white);
        }

        .form-label {
            font-size: .78rem;
            font-weight: 700;
            color: var(--gk);
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: .4rem;
        }

        /* ══════════════════════════════════════
           GRADIENT HELPERS
        ══════════════════════════════════════ */
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--gm), var(--gk));
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, var(--gl), #5ab020);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #f87171, #dc2626);
        }

        /* ══════════════════════════════════════
           WAVE DIVIDER UTILITY
        ══════════════════════════════════════ */
        .wave-divider {
            line-height: 0;
            overflow: hidden;
        }

        .wave-divider svg {
            display: block;
            width: 100%;
        }

        /* ══════════════════════════════════════
           GLASS PANEL UTILITY
        ══════════════════════════════════════ */
        .glass-panel {
            background: var(--glass);
            backdrop-filter: var(--blur);
            -webkit-backdrop-filter: var(--blur);
            border: 1.5px solid var(--glass-b);
            border-radius: var(--r-lg);
            box-shadow: var(--shadow);
        }

        /* ══════════════════════════════════════
           ORGANIC SECTION HEADER
        ══════════════════════════════════════ */
        .organic-rule {
            width: 36px;
            height: 3.5px;
            background: linear-gradient(90deg, var(--gl), var(--gb));
            border-radius: 2px;
            margin-bottom: .75rem;
        }

        /* ══════════════════════════════════════
           SCROLLBAR
        ══════════════════════════════════════ */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(126, 213, 58, .25);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(126, 213, 58, .45);
        }

        /* ══════════════════════════════════════
           MODAL FIXES
        ══════════════════════════════════════ */
        .modal {}

        .modal-backdrop {}

        .modal-content {
            border-radius: var(--r-lg);
            border: 1.5px solid var(--glass-b);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2) !important;
        }

        .modal-glass {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* ══════════════════════════════════════
           PAGE SECTION HERO STRIP
           (use at top of any inner page for the
            gradient + wave look from reference)
        ══════════════════════════════════════ */
        .page-hero-strip {
            background: linear-gradient(120deg, var(--gd) 0%, var(--gk) 40%, var(--gb) 80%, var(--gl) 100%);
            border-radius: var(--r-lg);
            padding: 2rem 2.5rem;
            margin-bottom: 1.8rem;
            position: relative;
            overflow: hidden;
            color: white;
        }

        .page-hero-strip::before {
            content: '';
            position: absolute;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(126, 213, 58, .12);
            right: -50px;
            top: -60px;
            animation: blobPulse 6s ease-in-out infinite;
        }

        .page-hero-strip::after {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .06);
            left: 40%;
            bottom: -50px;
        }

        .page-hero-strip .strip-rule {
            width: 32px;
            height: 3px;
            background: var(--gl);
            border-radius: 2px;
            margin-bottom: .75rem;
        }

        .page-hero-strip h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .page-hero-strip p {
            font-size: .85rem;
            color: rgba(255, 255, 255, .75);
            margin-top: .5rem;
            position: relative;
            z-index: 1;
        }

        /* ══════════════════════════════════════
           SIDEBAR CLOSE BUTTON & TOGGLED STATES
        ══════════════════════════════════════ */
        .sidebar-close-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid rgba(13, 59, 26, 0.1);
            background: rgba(13, 59, 26, 0.04);
            color: var(--txt-m);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--tr);
            font-size: .85rem;
            flex-shrink: 0;
            padding: 0;
        }

        .sidebar-close-btn:hover {
            background: rgba(211, 47, 47, 0.1);
            color: #dc2626;
            border-color: rgba(211, 47, 47, 0.2);
            transform: rotate(90deg);
        }

        /* Desktop Sidebar-Closed State */
        @media (min-width: 961px) {
            body.sidebar-closed .sidebar,
            html.sidebar-closed .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            body.sidebar-closed .main-content,
            html.sidebar-closed .main-content {
                margin-left: 0;
            }
        }

        /* ══════════════════════════════════════
           RESPONSIVE & MOBILE BACKDROP
        ══════════════════════════════════════ */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(13, 59, 26, 0.4);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 1040;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-backdrop.active {
            display: block;
            opacity: 1;
        }

        @media (max-width: 960px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
                box-shadow: 10px 0 40px rgba(13, 59, 26, 0.25);
            }

            .main-content {
                margin-left: 0 !important;
                padding: 1rem;
            }

            .menu-toggle {
                display: flex;
            }
        }

        @media (max-width: 576px) {
            .topbar {
                border-radius: var(--r-lg);
                flex-wrap: wrap;
                gap: .75rem;
            }

            .topbar-right {
                margin-left: auto;
            }

            .page-hero-strip {
                padding: 1.5rem;
            }

            .page-hero-strip h2 {
                font-size: 1.4rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- ══════════ SIDEBAR ══════════ -->
    <div class="sidebar" id="sidebar">

        @php
            $layoutUser = Auth::guard('admin')->user() ?? Auth::guard('web')->user();
            $isSalesUser = !Auth::guard('admin')->check() && optional($layoutUser)->role === 'sales';
            $isSalesRoute = request()->routeIs('sales.*');
            $isSalesSide = $isSalesRoute || $isSalesUser;
        @endphp

        <div class="sidebar-header">
            <div class="brand-wrap">
                <div class="brand-text">
                    <h3>DLK TECHNOLOGIES</h3>
                    <span>{{ $isSalesSide ? 'Sales Operations' : 'Employee Management System' }}</span>
                </div>
            </div>
            <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" title="Close Sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="sidebar-menu">
            <div class="nav flex-column">

                @if($isSalesSide)
                    {{-- ══════════════════════════════════════
                       SALES SIDEBAR (ONLY Lead Port, Website Leads, Profile, Help Desk)
                    ══════════════════════════════════════ --}}
                    @php
                        $salesUser = $layoutUser;
                        $salesLeadsCount = \App\Models\Lead::where(function($q) use ($salesUser) {
                            if ($salesUser && !($salesUser->is_admin ?? false)) {
                                $q->where('sales_id', $salesUser->id)->orWhere('assigned_to', $salesUser->id)->orWhereNull('sales_id');
                            }
                        })->where('status', 'new')->count();
                        $salesPendingSupportCount = \App\Models\SupportTicket::where('employee_id', optional($salesUser)->id)->where('status', 'pending')->count();
                    @endphp

                    <span class="menu-section-label">Sales Operations</span>

                    {{-- 1. Lead Port --}}
                    <div class="nav-item">
                        <a href="{{ route('sales.dashboard') }}"
                            class="nav-link {{ request()->routeIs('sales.dashboard') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-th-large text-success"></i></span>
                            Lead Port
                        </a>
                    </div>

                    {{-- 2. Website Leads --}}
                    <div class="nav-item">
                        <a href="{{ route('sales.leads.index') }}"
                            class="nav-link {{ request()->routeIs('sales.leads.*') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-funnel-dollar text-success"></i></span>
                            Website Leads
                            @if($salesLeadsCount > 0)
                                <span class="badge bg-success text-white rounded-pill ms-auto" style="font-size: 0.6rem; padding: 2px 6px;">{{ $salesLeadsCount }} new</span>
                            @endif
                        </a>
                    </div>

                    {{-- 3. Lead Reports --}}
                    <div class="nav-item">
                        <a href="{{ route('sales.reports') }}"
                            class="nav-link {{ request()->routeIs('sales.reports') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-chart-bar text-success"></i></span>
                            Lead Reports
                        </a>
                    </div>

                    <div class="sidebar-divider"></div>

                    <span class="menu-section-label">Account & Help</span>

                    {{-- 4. Help Desk --}}
                    <div class="nav-item">
                        <a href="{{ route('sales.support') }}"
                            class="nav-link {{ request()->routeIs('sales.support*') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-headset text-success"></i></span>
                            Help Desk
                            @if($salesPendingSupportCount > 0)
                                <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size: 0.6rem; padding: 2px 6px;">{{ $salesPendingSupportCount }}</span>
                            @endif
                        </a>
                    </div>

                    {{-- 5. Profile --}}
                    <div class="nav-item">
                        <a href="{{ route('sales.profile') }}"
                            class="nav-link {{ request()->routeIs('sales.profile*') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-user-circle text-success"></i></span>
                            Profile
                        </a>
                    </div>

                @else
                    <span class="menu-section-label">Main</span>

                <div class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-th-large"></i></span>
                        Dashboard
                    </a>
                </div>

                @php
                    $isLeadsActive = request()->routeIs('admin.leads.*');
                    $sidebarNewLeadsCount = \App\Models\Lead::where('status', 'new')->count();
                    $sidebarTodayFollowupsCount = \App\Models\Lead::whereDate('next_followup_date', \Carbon\Carbon::today())->whereIn('status', ['new', 'contacted', 'interested', 'follow_up'])->count();
                @endphp
                <div class="nav-item">
                    <a class="nav-link {{ $isLeadsActive ? 'active' : '' }}" data-bs-toggle="collapse"
                        href="#submenuLeads" role="button"
                        aria-expanded="{{ $isLeadsActive ? 'true' : 'false' }}">
                        <span class="nav-icon"><i class="fas fa-funnel-dollar text-success"></i></span>
                        Website Leads
                        @if($sidebarNewLeadsCount > 0)
                            <span class="badge bg-danger text-white rounded-pill ms-auto" style="font-size: 0.65rem; padding: 2px 7px;">{{ $sidebarNewLeadsCount }}</span>
                        @endif
                        <i class="fas fa-chevron-right submenu-arrow ms-auto"></i>
                    </a>
                    <div class="collapse {{ $isLeadsActive ? 'show' : '' }}" id="submenuLeads">
                        <ul class="submenu-list">
                            <li>
                                <a href="{{ route('admin.leads.dashboard') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.leads.dashboard') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-chart-pie"></i></span>
                                    Lead Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.leads.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.leads.index') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-list-ul"></i></span>
                                    All Leads
                                    @if($sidebarNewLeadsCount > 0)
                                        <span class="badge bg-success text-white rounded-pill ms-auto" style="font-size: 0.6rem; padding: 2px 5px;">{{ $sidebarNewLeadsCount }} new</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.leads.followups') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.leads.followups') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-phone-alt"></i></span>
                                    Follow-ups
                                    @if($sidebarTodayFollowupsCount > 0)
                                        <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size: 0.6rem; padding: 2px 5px;">{{ $sidebarTodayFollowupsCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.leads.analytics') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.leads.analytics') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-chart-line"></i></span>
                                    Analytics
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.leads.reports') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.leads.reports') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-file-invoice-dollar"></i></span>
                                    Master Lead Reports
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.leads.settings') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.leads.settings') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-sliders-h"></i></span>
                                    Settings & Forms
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.employees.index') }}"
                        class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-users"></i></span>
                        Employees
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.sales-team.index') }}"
                        class="nav-link {{ request()->routeIs('admin.sales-team.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-user-tie text-success"></i></span>
                        Sales Team
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.clients.index') }}"
                        class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-handshake"></i></span>
                        Clients
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.projects.index') }}"
                        class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-project-diagram"></i></span>
                        Projects
                    </a>
                </div>

                {{-- ── PORTALS & TEAM WORKFLOWS ── --}}
                <span class="menu-section-label">Team Portals</span>

                @php
                    $isSalesActive = request()->routeIs('sales.*');
                @endphp
                <div class="nav-item">
                    <a class="nav-link {{ $isSalesActive ? 'active' : '' }}" data-bs-toggle="collapse"
                        href="#submenuSales" role="button"
                        aria-expanded="{{ $isSalesActive ? 'true' : 'false' }}">
                        <span class="nav-icon"><i class="fas fa-hand-holding-usd text-success"></i></span>
                        Sales Portal
                        <i class="fas fa-chevron-right submenu-arrow ms-auto"></i>
                    </a>
                    <div class="collapse {{ $isSalesActive ? 'show' : '' }}" id="submenuSales">
                        <ul class="submenu-list">
                            <li>
                                <a href="{{ route('sales.dashboard') }}"
                                    class="nav-link sub-link {{ request()->routeIs('sales.dashboard') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-tachometer-alt"></i></span>
                                    Sales Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sales.leads.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('sales.leads.index') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-filter"></i></span>
                                    My Sales Pipeline
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sales.leads.create') }}"
                                    class="nav-link sub-link {{ request()->routeIs('sales.leads.create') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-plus-circle"></i></span>
                                    New Sales Lead
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sales.reports') }}"
                                    class="nav-link sub-link {{ request()->routeIs('sales.reports') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-chart-bar"></i></span>
                                    Sales Reports
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                @php
                    $isTlActive = request()->routeIs('tl.*');
                @endphp
                <div class="nav-item">
                    <a class="nav-link {{ $isTlActive ? 'active' : '' }}" data-bs-toggle="collapse"
                        href="#submenuTL" role="button"
                        aria-expanded="{{ $isTlActive ? 'true' : 'false' }}">
                        <span class="nav-icon"><i class="fas fa-crown text-warning"></i></span>
                        Team Leader Hub
                        <i class="fas fa-chevron-right submenu-arrow ms-auto"></i>
                    </a>
                    <div class="collapse {{ $isTlActive ? 'show' : '' }}" id="submenuTL">
                        <ul class="submenu-list">
                            <li>
                                <a href="{{ route('tl.dashboard') }}"
                                    class="nav-link sub-link {{ request()->routeIs('tl.dashboard') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-chalkboard-teacher"></i></span>
                                    TL Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tl.leads.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('tl.leads.index') || request()->routeIs('tl.leads.show') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-users-cog"></i></span>
                                    Assigned Leads & Workers
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tl.reports') }}"
                                    class="nav-link sub-link {{ request()->routeIs('tl.reports') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-file-contract"></i></span>
                                    TL Execution Reports
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="nav-item">
                    <a href="{{ route('employee.assigned-leads.index') }}"
                        class="nav-link {{ request()->routeIs('employee.assigned-leads.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-tasks text-primary"></i></span>
                        Assigned Projects
                    </a>
                </div>

                <div class="nav-item">
                    @php $pendingDeletionsCount = \App\Models\AccountDeletionRequest::where('status', 'pending')->count(); @endphp
                    <a href="{{ route('admin.account-deletions.index') }}"
                        class="nav-link {{ request()->routeIs('admin.account-deletions.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-user-times text-danger"></i></span>
                        Account Deletions
                        @if($pendingDeletionsCount > 0)
                            <span class="badge bg-danger text-white rounded-pill ms-auto" style="font-size: 0.65rem; padding: 2px 6px;">{{ $pendingDeletionsCount }}</span>
                        @endif
                    </a>
                </div>

                @php
                    $isOperationsActive = request()->routeIs('admin.attendance.*') ||
                        request()->routeIs('admin.time-sheets.*') ||
                        request()->routeIs('admin.leaves.*') ||
                        request()->routeIs('admin.payroll.*') ||
                        request()->routeIs('admin.shifts.*');
                @endphp
                <div class="nav-item">
                    <a class="nav-link {{ $isOperationsActive ? 'active' : '' }}" data-bs-toggle="collapse"
                        href="#submenuOperations" role="button"
                        aria-expanded="{{ $isOperationsActive ? 'true' : 'false' }}">
                        <span class="nav-icon"><i class="fas fa-cogs"></i></span>
                        Operations
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <div class="collapse {{ $isOperationsActive ? 'show' : '' }}" id="submenuOperations">
                        <ul class="submenu-list">
                            <li>
                                <a href="{{ route('admin.attendance.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-calendar-check"></i></span>
                                    Attendance
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.time-sheets.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.time-sheets.*') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-clock"></i></span>
                                    Time Sheets
                                </a>
                            </li>
                            <li>
                                @php $pendingLeavesCount = \App\Models\Leave::where('status', 'pending')->count(); @endphp
                                <a href="{{ route('admin.leaves.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-envelope-open-text"></i></span>
                                    Leaves
                                    @if($pendingLeavesCount > 0)
                                        <span class="nav-badge">{{ $pendingLeavesCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.payroll.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.payroll.*') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-money-bill-wave"></i></span>
                                    Payroll
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.shifts.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.shifts.*') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-clock"></i></span>
                                    Shift Management
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                @php
                    $isInventoryActive = request()->routeIs('admin.assets.*');
                    $pendingMaintenanceCount = \App\Models\AssetMaintenance::where('status', 'pending')->count();
                    $unreadNotificationsCount = \App\Models\Notification::where('is_read', false)->count();
                @endphp
                <div class="nav-item">
                    <a class="nav-link {{ $isInventoryActive ? 'active' : '' }}" data-bs-toggle="collapse"
                        href="#submenuInventory" role="button"
                        aria-expanded="{{ $isInventoryActive ? 'true' : 'false' }}">
                        <span class="nav-icon"><i class="fas fa-boxes"></i></span>
                        Inventory
                        @if($pendingMaintenanceCount > 0)
                            <span class="badge bg-danger text-white rounded-circle p-1 ms-auto" style="width: 10px; height: 10px; display: inline-block;" title="{{ $pendingMaintenanceCount }} Pending Maintenance Reports"></span>
                        @endif
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <div class="collapse {{ $isInventoryActive ? 'show' : '' }}" id="submenuInventory">
                        <ul class="submenu-list">
                            <li>
                                <a href="{{ route('admin.assets.dashboard') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.assets.dashboard') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-microchip"></i></span>
                                    Asset Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.assets.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.assets.index') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-laptop"></i></span>
                                    Device Inventory
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.assets.allocations.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.assets.allocations.*') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-hands-helping"></i></span>
                                    Allocations
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.assets.maintenance.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.assets.maintenance.*') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-tools"></i></span>
                                    Maintenance
                                    @if($pendingMaintenanceCount > 0)
                                        <span class="badge bg-danger text-white rounded-pill ms-auto" style="font-size: 0.65rem; padding: 2px 6px;">{{ $pendingMaintenanceCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.assets.reports.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.assets.reports.*') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-file-invoice"></i></span>
                                    Asset Reports
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                @php
                    $isMoreActive = request()->routeIs('admin.birthdays.*') ||
                        request()->routeIs('admin.reports.*') ||
                        request()->routeIs('admin.activity-log.*');
                @endphp
                <div class="nav-item">
                    <a class="nav-link {{ $isMoreActive ? 'active' : '' }}" data-bs-toggle="collapse"
                        href="#submenuMore" role="button" aria-expanded="{{ $isMoreActive ? 'true' : 'false' }}">
                        <span class="nav-icon"><i class="fas fa-plus-circle"></i></span>
                        More
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <div class="collapse {{ $isMoreActive ? 'show' : '' }}" id="submenuMore">
                        <ul class="submenu-list">
                            <li>
                                <a href="{{ route('admin.birthdays.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.birthdays.*') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-birthday-cake"></i></span>
                                    Celebrations
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-chart-bar"></i></span>
                                    Reports
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.activity-log.index') }}"
                                    class="nav-link sub-link {{ request()->routeIs('admin.activity-log.*') ? 'active' : '' }}">
                                    <span class="nav-icon-sm"><i class="fas fa-history"></i></span>
                                    Activity Log
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="sidebar-divider"></div>

                <div class="nav-item">
                    @php $pendingSupportCount = \App\Models\SupportTicket::where('status', 'pending')->count(); @endphp
                    <a href="{{ route('admin.support.index') }}"
                        class="nav-link {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-headset"></i></span>
                        Help Desk
                        @if($pendingSupportCount > 0)
                            <span class="badge bg-danger text-white rounded-pill ms-auto" style="font-size: 0.65rem; padding: 2px 6px;">{{ $pendingSupportCount }}</span>
                        @endif
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.profile') }}"
                        class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-user-circle"></i></span>
                        Profile
                    </a>
                </div>
                @endif

            </div>
        </div>

        <div class="sidebar-footer">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="logout-link">
                <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                Sign Out
            </a>
            <form id="logout-form" action="{{ Auth::guard('admin')->check() ? route('admin.logout') : (optional(Auth::guard('web')->user())->role === 'sales' ? route('sales.logout') : route('employee.logout')) }}" method="POST" style="display:none;">@csrf</form>
        </div>

    </div>
    <!-- ══════════ /SIDEBAR ══════════ -->

    <!-- ══════════ SIDEBAR BACKDROP (Mobile) ══════════ -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>


    <!-- ══════════ MAIN CONTENT ══════════ -->
    <div class="main-content" id="mainContent">

        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" type="button" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="page-breadcrumb">
                    <span class="page-title">@yield('page-title', 'Dashboard')</span>
                    <span class="page-subtitle">@yield('page-subtitle', 'Welcome back — have a great day')</span>
                </div>
            </div>

            <div class="topbar-right">
                @if($isSalesSide)
                    <a href="{{ route('sales.support') }}" class="topbar-btn position-relative text-dark text-decoration-none" data-bs-toggle="tooltip" title="Help Desk & Support">
                        <i class="fas fa-headset"></i>
                    </a>
                    <a href="{{ route('sales.profile') }}" class="topbar-btn text-dark text-decoration-none" data-bs-toggle="tooltip" title="My Profile & Security">
                        <i class="fas fa-cog"></i>
                    </a>
                @else
                    @php $totalAlerts = $pendingMaintenanceCount + $unreadNotificationsCount + $pendingSupportCount + $pendingDeletionsCount; @endphp
                    <a href="{{ route('admin.notifications.index') }}" class="topbar-btn position-relative text-dark text-decoration-none" data-bs-toggle="tooltip" title="Notifications ({{ $totalAlerts }} New Alerts)">
                        <i class="fas fa-bell"></i>
                        @if($totalAlerts > 0)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        @endif
                    </a>
                    <button class="topbar-btn" data-bs-toggle="tooltip" title="Settings">
                        <i class="fas fa-cog"></i>
                    </button>
                @endif
                @php
                    $currentUser = Auth::guard('admin')->user() ?? Auth::guard('web')->user();
                    $currentUserName = $currentUser ? $currentUser->name : 'User';
                    $currentUserRole = Auth::guard('admin')->check() 
                        ? 'Administrator' 
                        : (optional($currentUser)->is_team_leader ? 'Team Leader (TL)' : (optional($currentUser)->role === 'sales' ? 'Sales Executive' : 'Staff Member'));
                    $profileRoute = $isSalesSide ? route('sales.profile') : (Auth::guard('admin')->check() ? route('admin.profile') : route('employee.profile'));
                @endphp
                <a href="{{ $profileRoute }}" class="user-chip text-decoration-none">
                    <div>
                        <span class="u-name">
                            {{ $currentUserName }}
                            @if(optional($currentUser)->employee_code)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success font-monospace ms-1" style="font-size: 0.65rem; vertical-align: middle;">
                                    {{ $currentUser->employee_code }}
                                </span>
                            @endif
                        </span>
                        <span class="u-role">{{ $currentUserRole }}</span>
                    </div>
                    <div class="user-avatar">
                        {{ strtoupper(substr($currentUserName, 0, 2)) }}
                    </div>
                </a>
            </div>
        </div>

        <!-- Page Content -->
        @yield('content')

    </div>
    <!-- ══════════ /MAIN CONTENT ══════════ -->


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar(e) {
            if (e) {
                if (e.preventDefault) e.preventDefault();
                if (e.stopPropagation) e.stopPropagation();
            }
            const isMobile = window.innerWidth <= 960;
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (isMobile) {
                if (sidebar) {
                    const isActive = sidebar.classList.toggle('active');
                    if (backdrop) {
                        backdrop.classList.toggle('active', isActive);
                    }
                }
            } else {
                const isNowClosed = document.body.classList.toggle('sidebar-closed');
                document.documentElement.classList.toggle('sidebar-closed', isNowClosed);
                localStorage.setItem('ems_sidebar_closed', isNowClosed ? 'true' : 'false');
            }
        }

        // Restore state on load
        (function() {
            if (window.innerWidth > 960) {
                if (localStorage.getItem('ems_sidebar_closed') === 'true') {
                    document.documentElement.classList.add('sidebar-closed');
                    document.body.classList.add('sidebar-closed');
                }
            }
        })();

        // Close on mobile when clicking backdrop or outside
        document.addEventListener('click', function (e) {
            const sb = document.getElementById('sidebar');
            const btn = document.getElementById('menuToggle');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (window.innerWidth <= 960 && sb && sb.classList.contains('active')) {
                if (!sb.contains(e.target) && btn && !btn.contains(e.target)) {
                    sb.classList.remove('active');
                    if (backdrop) backdrop.classList.remove('active');
                }
            }
        });

        $(window).resize(function () {
            const sb = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if ($(window).width() > 960) {
                if (sb) sb.classList.remove('active');
                if (backdrop) backdrop.classList.remove('active');
            }
        });

        function showToast(message, type = 'success') {
            const colors = { success: '#27a355', error: '#dc2626', warning: '#c9a84c', info: '#0ea5e9' };
            Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                background: 'rgba(255,255,255,0.95)',
                backdrop: false,
                didOpen: (t) => {
                    t.addEventListener('mouseenter', Swal.stopTimer);
                    t.addEventListener('mouseleave', Swal.resumeTimer);
                }
            }).fire({ icon: type, title: message, iconColor: colors[type] || colors.success });
        }

        @if(session('success')) showToast('{{ session('success') }}', 'success'); @endif
        @if(session('error'))   showToast('{{ session('error') }}', 'error'); @endif
        @if(session('warning')) showToast('{{ session('warning') }}', 'warning'); @endif
        @if(session('info'))    showToast('{{ session('info') }}', 'info'); @endif

        function confirmDelete(url, name) {
            Swal.fire({
                title: 'Delete this record?',
                html: `<span style="color:#4a7a5a;font-size:.9rem">Removing <strong>${name}</strong> cannot be undone.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#27a355',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((r) => { if (r.isConfirmed) window.location.href = url; });
        }

        function showLoading() {
            Swal.fire({ title: 'Please wait…', allowOutsideClick: false, background: 'rgba(255,255,255,0.95)', didOpen: () => Swal.showLoading() });
        }
        function closeLoading() { Swal.close(); }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', minimumFractionDigits: 2 }).format(amount);
        }
        function formatDate(date) {
            return new Date(date).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        $(document).ready(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
            $('#mainContent').addClass('fade-in-up');

            // Auto-expand and scroll to active submenu item
            $('.sub-link.active').closest('.collapse').addClass('show').show();
            $('.sub-link.active').closest('.collapse').prev('.nav-link').attr('aria-expanded', 'true').addClass('active');

            // ── Sidebar Submenu Accordion Toggle (Dropdown Open / Close) ──
            $(document).on('click', '.sidebar [data-bs-toggle="collapse"]', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var $btn = $(this);
                var targetSelector = $btn.attr('data-bs-target') || $btn.attr('href');
                if (!targetSelector) return;
                var $target = $(targetSelector);

                if ($target.length) {
                    var targetEl = $target[0];
                    if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                        var bsCol = bootstrap.Collapse.getOrCreateInstance(targetEl, { toggle: false });
                        if ($target.hasClass('show')) {
                            bsCol.hide();
                            $btn.attr('aria-expanded', 'false');
                        } else {
                            bsCol.show();
                            $btn.attr('aria-expanded', 'true');
                        }
                    } else {
                        var isVisible = $target.is(':visible') || $target.hasClass('show');
                        if (isVisible) {
                            $target.slideUp(180, function () { $target.removeClass('show'); });
                            $btn.attr('aria-expanded', 'false');
                        } else {
                            $target.slideDown(180, function () { $target.addClass('show'); });
                            $btn.attr('aria-expanded', 'true');
                        }
                    }
                }
            });

            // ── Guaranteed Tab Bar Switcher (Pills & Tabs on All Pages) ──
            $(document).on('click', '[data-bs-toggle="pill"], [data-bs-toggle="tab"]', function (e) {
                e.preventDefault();
                var $btn = $(this);
                var targetSelector = $btn.attr('data-bs-target') || $btn.attr('href');

                // 1. Native Bootstrap 5 Tab trigger
                if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    try {
                        var bTab = bootstrap.Tab.getOrCreateInstance(this);
                        bTab.show();
                    } catch (err) {}
                }

                // 2. Guaranteed Active Button Highlight
                var $nav = $btn.closest('.nav, .nav-pills, .nav-tabs');
                if ($nav.length) {
                    $nav.find('.nav-link').removeClass('active').attr('aria-selected', 'false');
                } else {
                    $btn.siblings().removeClass('active').attr('aria-selected', 'false');
                }
                $btn.addClass('active').attr('aria-selected', 'true');

                // 3. Guaranteed Pane Switch
                if (targetSelector) {
                    var $pane = $(targetSelector);
                    if ($pane.length) {
                        var $tabContent = $pane.closest('.tab-content');
                        if ($tabContent.length) {
                            $tabContent.find('.tab-pane').removeClass('show active');
                        } else {
                            $('.tab-pane').removeClass('show active');
                        }
                        $pane.addClass('show active');
                    }
                }
            });

            // ── Direct Binding for Menu Toggle & Close Buttons ──
            $(document).off('click', '#menuToggle, .sidebar-close-btn').on('click', '#menuToggle, .sidebar-close-btn', function(e) {
                toggleSidebar(e);
            });

            // ── Dynamic z-index elevation for cards containing active dropdowns ──
            $(document).on('show.bs.dropdown', function(e) {
                $(e.target).closest('.card').addClass('dropdown-active').css('z-index', 1050);
            });
            $(document).on('hidden.bs.dropdown', function(e) {
                $(e.target).closest('.card').removeClass('dropdown-active').css('z-index', '');
            });
        });
    </script>

    @stack('scripts')
</body>

</html>