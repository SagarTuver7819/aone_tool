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
    <!-- Modern Fonts: Plus Jakarta Sans, Hanken Grotesk, Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Hanken+Grotesk:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 for Grid/Utilities -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Main Style -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>style.css">
    <!-- Chart.js (pinned to match reference) & JQuery -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>$.fn.dataTable.ext.errMode = 'none';</script>
    <style>
        /* Custom Premium DataTables & Global Tables Styling */
        .dataTables_wrapper { padding: 1.5rem 0; }
        .dataTables_wrapper .dataTables_length { margin-bottom: 1.5rem; float: left; }
        .dataTables_wrapper .dataTables_filter { margin-bottom: 1.5rem; float: right; }
        .dataTables_wrapper .dataTables_info { color: #64748b !important; font-weight: 700 !important; font-size: 0.8rem !important; margin-top: 1.5rem; float: left; }
        .dataTables_wrapper .dataTables_paginate { margin-top: 1.5rem; float: right; display: flex; gap: 4px; }
        
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
            background: #0f52ff !important; /* Premium Executive Blue */
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
        table.dataTable, table.table, table.analysis-table, table.trend-table { 
            width: 100% !important; 
            border-collapse: collapse !important; 
            border-spacing: 0 !important;
            margin: 1.5rem 0 !important;
            overflow: hidden !important;
            border-radius: 16px !important;
            border: 1px solid #f1f5f9 !important;
        }

        table.dataTable thead th, table.table thead th, table.analysis-table thead th, table.trend-table thead th { 
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

        table.dataTable tbody td, table.table tbody td, table.analysis-table tbody td, table.trend-table tbody td { 
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
        table.dataTable tbody td.text-start, table.table tbody td.text-start, table.analysis-table tbody td.text-start, table.trend-table tbody td.text-start,
        table.dataTable tbody td.align-left, table.table tbody td.align-left, table.analysis-table tbody td.align-left, table.trend-table tbody td.align-left {
            text-align: left !important;
        }
        table.dataTable tbody td.text-end, table.table tbody td.text-end, table.analysis-table tbody td.text-end, table.trend-table tbody td.text-end,
        table.dataTable tbody td.align-right, table.table tbody td.align-right, table.analysis-table tbody td.align-right, table.trend-table tbody td.align-right {
            text-align: right !important;
        }

        /* Hover effect */
        table.dataTable tbody tr:hover td, table.table tbody tr:hover td, table.analysis-table tbody tr:hover td, table.trend-table tbody tr:hover td { 
            background: #f8fafc !important; 
            color: #0f172a !important;
        }
        
        /* Eliminate native DataTable border lines */
        table.dataTable.no-footer { border-bottom: 1px solid #f1f5f9 !important; }
    </style>
</head>
<body>
    <div class="app-container">
