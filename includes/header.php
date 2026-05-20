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
    <!-- Modern Font: Plus Jakarta Sans -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap">
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
        .dataTables_wrapper { padding: 1rem 0; }
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 1.5rem; padding: 0 0.5rem; }
        .dataTables_wrapper .dataTables_paginate { margin-top: 1.5rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #6366f1 !important; color: white !important; border: none !important; border-radius: 6px !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #e0e7ff !important; color: #6366f1 !important; border: none !important; }
        .dataTables_wrapper .dataTables_length select { border-radius: 6px; padding: 4px 8px; border-color: #e2e8f0; }
        .dataTables_wrapper .dataTables_filter input { border-radius: 6px; padding: 6px 12px; border: 1px solid #e2e8f0; margin-left: 0.5rem; }
        table.dataTable thead th { border-bottom: 1px solid #e2e8f0 !important; }
    </style>
</head>
<body>
    <div class="app-container">
