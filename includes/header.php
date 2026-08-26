<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AOne - Amazon Intelligence</title>
    <!-- Modern Fonts: Plus Jakarta Sans, Inter -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Hanken+Grotesk:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 for Grid/Utilities -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Main Style -->
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>style.css?v=<?php echo filemtime(dirname(__DIR__) . '/style.css'); ?>">
    <!-- Chart.js (pinned to match reference) & JQuery -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Flatpickr Range Datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>$.fn.dataTable.ext.errMode = 'none';</script>
    <style>
        /* Unified Flatpickr Theme matching Figma styling */
        .flatpickr-calendar {
            font-family: 'Inter', 'Inter', -apple-system, sans-serif !important;
            border-radius: 14px !important;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15) !important;
            border: 1px solid #E2E8F0 !important;
            padding: 10px !important;
            width: 308px !important;
            background: #ffffff !important;
        }

        .flatpickr-calendar::before,
        .flatpickr-calendar::after {
            border-bottom-color: #ffffff !important;
        }

        .flatpickr-months {
            padding: 4px 0 8px 0 !important;
            position: relative;
            align-items: center !important;
        }

        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            padding: 6px !important;
            fill: #64748B !important;
            color: #64748B !important;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            color: #0F172A !important;
            background: #F1F4F9 !important;
        }

        .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-months .flatpickr-next-month svg {
            width: 14px !important;
            height: 14px !important;
            fill: #475569 !important;
        }

        .flatpickr-current-month {
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            color: #0F172A !important;
            padding: 2px 0 0 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            appearance: none !important;
            -webkit-appearance: none !important;
            background: #F8FAFC !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 8px !important;
            padding: 4px 10px !important;
            font-weight: 700 !important;
            font-size: 0.85rem !important;
            color: #0F172A !important;
            cursor: pointer !important;
            outline: none !important;
            margin: 0 !important;
            height: 32px !important;
        }

        .flatpickr-current-month .numInputWrapper {
            width: 65px !important;
        }

        .flatpickr-current-month input.cur-year {
            font-weight: 700 !important;
            font-size: 0.95rem !important;
            color: #0F172A !important;
            padding: 4px 0 !important;
        }

        .flatpickr-weekdays {
            border-bottom: 1px solid #F1F5F9 !important;
            margin-bottom: 6px !important;
            padding-bottom: 4px !important;
        }

        span.flatpickr-weekday {
            color: #64748B !important;
            font-weight: 700 !important;
            font-size: 0.78rem !important;
        }

        .flatpickr-rContainer,
        .flatpickr-days,
        .dayContainer {
            width: 288px !important;
            min-width: 288px !important;
            max-width: 288px !important;
            border: none !important;
            background: transparent !important;
        }

        .dayContainer {
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
        }

        .flatpickr-day {
            border-radius: 8px !important;
            border: none !important;
            height: 36px !important;
            line-height: 36px !important;
            max-width: 36px !important;
            margin: 2px 2.5px !important;
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            color: #1E293B !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        .flatpickr-day:hover {
            background: #F1F4F9 !important;
            color: #0F172A !important;
            border-color: transparent !important;
        }

        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #CBD5E1 !important;
            background: transparent !important;
        }

        .flatpickr-day.today {
            border: 1.5px solid #4362CE !important;
            background: transparent !important;
            color: #4362CE !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background: #4362CE !important;
            border-color: #4362CE !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
        }

        .flatpickr-day.inRange {
            background: #EEF2FF !important;
            border-color: transparent !important;
            color: #4362CE !important;
            border-radius: 0 !important;
            box-shadow: -2px 0 0 #EEF2FF, 2px 0 0 #EEF2FF !important;
        }

        .flatpickr-day.selected.startRange {
            border-radius: 8px 0 0 8px !important;
        }

        .flatpickr-day.selected.endRange {
            border-radius: 0 8px 8px 0 !important;
        }

        .flatpickr-day.selected.startRange.endRange {
            border-radius: 8px !important;
        }

        /* Unified Date Filter Container */
        .figma-date-picker-wrap {
            display: inline-flex;
            align-items: center;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 0.45rem 0.95rem;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
            gap: 0.55rem;
        }

        .figma-date-picker-wrap:hover,
        .figma-date-picker-wrap:focus-within {
            border-color: #CBD5E1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .figma-date-picker-wrap svg,
        .figma-date-picker-wrap img,
        .figma-date-picker-wrap i {
            color: #363B4F;
            flex-shrink: 0;
        }

        .figma-date-picker-wrap input.flatpickr-range-input {
            border: none !important;
            outline: none !important;
            background: transparent !important;
            font-family: 'Inter', -apple-system, sans-serif !important;
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            color: #1E2238 !important;
            cursor: pointer !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 215px !important;
            letter-spacing: -0.01em !important;
        }

        .btn-figma-refresh {
            width: 38px;
            height: 38px;
            border: 1px solid #E2E8F0 !important;
            border-radius: 10px !important;
            background: #FFFFFF !important;
            color: #363B4F !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            padding: 0;
        }

        .btn-figma-refresh:hover {
            background: #F8FAFC !important;
            color: #0F172A !important;
            border-color: #CBD5E1 !important;
        }

        .btn-figma-refresh svg,
        .btn-figma-refresh img,
        .btn-figma-refresh i {
            color: #363B4F;
        }

        /* Flatpickr Reset to prevent table conflicts */
        .flatpickr-calendar table {
            border: none !important;
            margin: 0 !important;
            border-radius: 0 !important;
        }

        .flatpickr-calendar table td,
        .flatpickr-calendar table th {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
        }

        .flatpickr-calendar select {
            background-color: #F8FAFC !important;
            color: #0F172A !important;
        }

        .flatpickr-calendar select option {
            background-color: #FFFFFF !important;
            color: #0F172A !important;
        }

        /* Custom Premium DataTables & Global Tables Styling */
        .dataTables_wrapper {
            padding: 1.5rem 0;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1.5rem;
            float: left;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1.5rem;
            float: right;
        }

        .dataTables_wrapper .dataTables_info {
            color: #64748b !important;
            font-weight: 700 !important;
            font-size: 0.8rem !important;
            margin-top: 1.5rem;
            float: left;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1.5rem;
            float: right;
            display: flex;
            gap: 4px;
        }

        /* Modern pagination buttons styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: #ffffff !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 8px 14px !important;
            font-weight: 700 !important;
            font-size: 0.8rem !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
            transform: translateY(-1px);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0f52ff !important;
            /* Premium Executive Blue */
            color: #ffffff !important;
            border-color: #0f52ff !important;
            box-shadow: 0 4px 12px rgba(15, 82, 255, 0.25) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f8fafc !important;
            color: #cbd5e1 !important;
            border-color: #f1f5f9 !important;
            cursor: not-allowed !important;
            opacity: 0.6;
            transform: none !important;
        }

        /* Length & Filter Controls */
        .dataTables_wrapper .dataTables_length select {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 6px 12px;
            font-weight: 700;
            font-size: 0.85rem;
            color: #1e293b;
            outline: none;
            transition: all 0.3s;
        }

        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #3b82f6;
            background: #ffffff;
        }

        .dataTables_wrapper .dataTables_filter input {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #1e293b;
            margin-left: 0.5rem;
            outline: none;
            transition: all 0.3s;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Unified Table, DataTable, Analysis Table styling */
        table.dataTable,
        table.table,
        table.analysis-table,
        table.trend-table {
            width: 100% !important;
            border-collapse: collapse !important;
            border-spacing: 0 !important;
            margin: 1.5rem 0 !important;
            overflow: hidden !important;
            border-radius: 16px !important;
            border: 1px solid #f1f5f9 !important;
        }

        table.dataTable thead th,
        table.table thead th,
        table.analysis-table thead th,
        table.trend-table thead th {
            background: #f8fafc !important;
            color: #475569 !important;
            font-weight: 800 !important;
            font-size: 0.9rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 1.25rem 1rem !important;
            border: 1px solid #e2e8f0 !important;
            text-align: center !important;
            vertical-align: middle !important;
            white-space: nowrap !important;
        }

        table.dataTable tbody td,
        table.table tbody td,
        table.analysis-table tbody td,
        table.trend-table tbody td {
            padding: 1.25rem 1rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            font-size: 0.975rem !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            text-align: center !important;
            vertical-align: middle !important;
            white-space: nowrap !important;
            background: #ffffff;
        }

        /* Alignment utilities for table body cells */
        table.dataTable tbody td.text-start,
        table.table tbody td.text-start,
        table.analysis-table tbody td.text-start,
        table.trend-table tbody td.text-start,
        table.dataTable tbody td.align-left,
        table.table tbody td.align-left,
        table.analysis-table tbody td.align-left,
        table.trend-table tbody td.align-left {
            text-align: left !important;
        }

        table.dataTable tbody td.text-end,
        table.table tbody td.text-end,
        table.analysis-table tbody td.text-end,
        table.trend-table tbody td.text-end,
        table.dataTable tbody td.align-right,
        table.table tbody td.align-right,
        table.analysis-table tbody td.align-right,
        table.trend-table tbody td.align-right {
            text-align: right !important;
        }

        /* Hover effect */
        table.dataTable tbody tr:hover td,
        table.table tbody tr:hover td,
        table.analysis-table tbody tr:hover td,
        table.trend-table tbody tr:hover td {
            background: #f8fafc !important;
            color: #0f172a !important;
        }

        /* Eliminate native DataTable border lines */
        /* Mobile App Header (Only visible on screens <= 1024px) */
        .mobile-app-header {
            display: none;
        }

        @media (max-width: 1024px) {
            .mobile-app-header {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                padding: 0.75rem 1rem;
                background: #FFFFFF;
                border-bottom: 1px solid #EAECEF;
                position: sticky;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1020;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            }

            .mobile-app-header-left {
                display: flex;
                align-items: center;
                gap: 0.65rem;
            }

            .mobile-app-header-logo {
                width: 32px;
                height: 32px;
                background: #4362CE;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 3px 8px rgba(67, 98, 206, 0.25);
            }

            .mobile-app-header-title {
                display: flex;
                flex-direction: column;
            }

            .mobile-app-header-title .brand-main {
                font-family: 'Inter', sans-serif;
                font-size: 0.95rem;
                font-weight: 800;
                color: #1E2238;
                line-height: 1.1;
            }

            .mobile-app-header-title .brand-sub {
                font-size: 0.68rem;
                font-weight: 600;
                color: #64748B;
            }

            .mobile-app-header-right {
                display: flex;
                align-items: center;
                gap: 0.45rem;
            }

            .mobile-header-action-btn {
                width: 34px;
                height: 34px;
                border-radius: 8px;
                border: 1px solid #E2E8F0;
                background: #FFFFFF;
                color: #363B4F;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.15s ease;
            }

            .mobile-header-action-btn:hover,
            .mobile-header-action-btn:active {
                background: #F1F4F9;
                color: #4362CE;
                border-color: #CBD5E1;
            }
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Mobile Native App Header -->
        <header class="mobile-app-header">
            <div class="mobile-app-header-left" onclick="toggleSidebar()" role="button" tabindex="0">
                <div class="mobile-app-header-logo">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                    </svg>
                </div>
                <div class="mobile-app-header-title">
                    <span class="brand-main">A'One Intelligence</span>
                    <span class="brand-sub"><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></span>
                </div>
            </div>
            <div class="mobile-app-header-right">
                <button type="button" class="mobile-header-action-btn" onclick="toggleSidebar()" title="Open Menu">
                    <svg width="17" height="17" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 5H17M3 10H17M3 15H17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </header>