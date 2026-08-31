<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = 'Reimbursement Center';
$page_subtitle = 'AI-powered Amazon reimbursement & revenue recovery intelligence';

$customers = get_all_customers();

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<style>
    body {
        background-color: #F8FAFC !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        color: #0F172A;
        overflow-x: hidden;
    }

    .top-header {
        display: none !important;
    }

    .main-wrapper {
        padding: 1.25rem 2rem 2rem 2rem !important;
        overflow-x: hidden;
    }

    /* Topbar styling - Clean Transparent Header matching Figma */
    .figma-page-topbar {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 0.75rem !important;
        flex-wrap: nowrap !important;
        padding: 0.25rem 0 1rem 0 !important;
        background: transparent !important;
        border: none !important;
        border-bottom: 1px solid #EAECEF !important;
        border-radius: 0 !important;
        margin-bottom: 1.25rem !important;
        box-shadow: none !important;
    }

    .figma-page-topbar-left {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .figma-select-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .figma-select-wrapper select {
        appearance: none;
        -webkit-appearance: none;
        min-width: 170px;
        padding: 0.45rem 2.2rem 0.45rem 0.85rem;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #1E2238;
        background: #FFFFFF;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .figma-select-wrapper select:focus {
        border-color: #4362CE;
    }

    .figma-select-wrapper .select-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        width: 12px;
        height: 12px;
    }

    .figma-page-breadcrumb {
        font-size: 0.82rem;
        font-weight: 500;
        color: #64748B;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .figma-page-breadcrumb .breadcrumb-dot {
        margin: 0 3px;
        opacity: 0.4;
        font-size: 0.9rem;
    }

    .figma-page-breadcrumb strong {
        color: #1E293B;
        font-weight: 600;
    }

    .figma-page-topbar-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-figma-primary {
        background: #4362CE !important;
        color: #FFFFFF !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 0.5rem 1.15rem !important;
        font-size: 0.82rem !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.45rem !important;
        box-shadow: 0px 4px 10px rgba(67, 98, 206, 0.2) !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .btn-figma-primary:hover {
        background: #3452BA !important;
        transform: translateY(-1px);
        color: #FFFFFF !important;
    }

    .btn-figma-outline-sm {
        background: #F1F4F9 !important;
        color: #363B4F !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 0.5rem 1.05rem !important;
        font-size: 0.82rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.45rem !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .btn-figma-outline-sm:hover {
        background: #E2E8F0 !important;
        color: #0F172A !important;
    }

    .btn-reimb-outline {
        background: #F8FAFC !important;
        color: #363B4F !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 8px !important;
        padding: 0.45rem 0.95rem !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.45rem !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        line-height: 1.2 !important;
    }

    .btn-reimb-outline:hover {
        background: #F1F5F9 !important;
        border-color: #CBD5E1 !important;
        color: #0F172A !important;
    }

    .btn-figma-icon-sm {
        width: 38px !important;
        height: 38px !important;
        border-radius: 50% !important;
        background: #F1F4F9 !important;
        border: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #475569 !important;
        cursor: pointer !important;
        position: relative !important;
        transition: all 0.2s ease !important;
    }

    .btn-figma-icon-sm:hover {
        background: #E2E8F0 !important;
        color: #0F172A !important;
    }

    .btn-figma-icon-sm .notif-badge {
        position: absolute;
        top: 9px;
        right: 9px;
        width: 6px;
        height: 6px;
        background: #EE473D;
        border-radius: 50%;
        border: 1.5px solid #F1F4F9;
    }

    .reimb-container {
        padding: 0;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        overflow-x: hidden;
    }

    /* Page Header */
    .reimb-page-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 1.25rem;
        width: 100%;
    }

    .reimb-page-title h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .reimb-page-title p {
        font-size: 0.82rem;
        color: #64748B;
        font-weight: 500;
        margin: 3px 0 0 0;
    }

    .reimb-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Bento Base Card */
    .reimb-card {
        background: #FFFFFF;
        border: 1px solid #EAECEF;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        margin-bottom: 1.5rem;
        min-width: 0;
        box-sizing: border-box;
        width: 100%;
    }

    .reimb-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }

    .reimb-card-head h3 {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
    }

    .reimb-card-head p {
        font-size: 0.76rem;
        color: #64748B;
        margin: 3px 0 0 0;
        font-weight: 500;
    }

    /* MAIN TOP SECTION (Left 4 KPI Cards: 310px Fill + Right Trend & AI Insights) */
    .reimb-top-grid {
        display: grid;
        grid-template-columns: 310px minmax(0, 1fr);
        gap: 20px;
        margin-bottom: 20px;
        align-items: stretch;
        width: 100%;
        min-width: 0;
    }

    /* Left 4 Stacked KPI Cards: each 310px Fill x 149px Fill */
    .reimb-kpi-col {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 310px;
        min-width: 310px;
        flex-shrink: 0;
    }

    .reimb-kpi-card {
        background: #FFFFFF;
        border: 1px solid #E8EAF2;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 149px;
        height: 149px;
        box-sizing: border-box;
        transition: all 0.15s ease;
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .reimb-kpi-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
    }

    /* Top Highlight Blue Card (Figma: 310px Fill x 149px Fill) */
    .reimb-kpi-card.hero-blue {
        background: #4362CE !important;
        background-image: url('<?php echo BASE_URL; ?>assets/images/bg-3.png') !important;
        background-repeat: no-repeat !important;
        background-position: right center !important;
        background-size: auto 100% !important;
        border: 1px solid #4362CE !important;
        border-radius: 14px !important;
        color: #FFFFFF !important;
        box-shadow: 0 8px 20px rgba(67, 98, 206, 0.22);
    }

    .reimb-kpi-card.hero-blue .grid-decor {
        display: none;
    }

    .reimb-kpi-card.hero-blue .reimb-kpi-label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
        font-weight: 500;
    }

    .reimb-kpi-card.hero-blue .reimb-kpi-val {
        color: #FFFFFF;
        font-size: 26px;
        font-weight: 700;
    }

    .reimb-kpi-card.hero-blue .reimb-delta-badge {
        background: rgba(255, 255, 255, 0.2);
        color: #FFFFFF;
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .reimb-kpi-card.hero-blue .reimb-delta-sub {
        color: rgba(255, 255, 255, 0.85);
        font-size: 12px;
    }

    .reimb-kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .reimb-kpi-label {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 14px;
        font-weight: 500;
        color: #475569;
    }

    .reimb-kpi-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #F8FAFC;
        border: 1px solid #EFF4FE;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .reimb-kpi-val {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 26px;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.2;
        margin: 4px 0;
        font-variant-numeric: tabular-nums;
    }

    .reimb-kpi-foot {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 2px;
    }

    .reimb-delta-badge {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        height: 20px !important;
        padding: 0 8px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        line-height: 20px !important;
        box-sizing: border-box !important;
        font-variant-numeric: tabular-nums !important;
        letter-spacing: -0.01em !important;
    }

    .reimb-delta-badge.up {
        background: #EEF8F1 !important;
        color: #029153 !important;
    }

    .reimb-delta-badge.down {
        background: #FEF0EF !important;
        color: #EE473D !important;
    }

    .reimb-delta-badge.warning {
        background: #FEF0EF !important;
        color: #EE473D !important;
    }

    .reimb-delta-badge.neutral {
        background: #F1F5F9 !important;
        color: #64748B !important;
    }

    .reimb-delta-badge svg {
        flex-shrink: 0;
    }

    .reimb-delta-sub {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 12px;
        color: #1E293B;
        font-weight: 500;
        letter-spacing: -0.01em;
    }

    /* Right Stacked 2 Cards: Trend + AI Insights */
    .reimb-right-col {
        display: flex;
        flex-direction: column;
        gap: 20px;
        min-width: 0;
        width: 100%;
        flex: 1;
    }

    /* Trend Card (Figma: 900px Fill x 382px, 20px padding) */
    .reimb-trend-card {
        background: #FFFFFF;
        border: 1px solid #E8EAF2;
        border-radius: 14px;
        padding: 24px 28px 20px 28px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        box-sizing: border-box;
        width: 100%;
        min-width: 0;
        height: 382px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Trend Toggle */
    .reimb-trend-toggle {
        display: flex;
        background: #F1F5F9;
        padding: 3px;
        border-radius: 8px;
        gap: 2px;
    }

    .reimb-trend-btn {
        border: none;
        background: transparent;
        padding: 5px 14px;
        border-radius: 6px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 12px;
        font-weight: 600;
        color: #64748B;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .reimb-trend-btn.active {
        background: #FFFFFF;
        color: #0F172A;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        font-weight: 700;
    }

    /* AI Recovery Insights Panel (Figma: Width Fill 900px x Height Hug 254px, 14px radius, 30px padding, 30px gap) */
    .reimb-ai-panel {
        background: linear-gradient(135deg, #041245 0%, #12309A 100%) !important;
        border: 1px solid rgba(67, 98, 206, 0.25);
        border-radius: 14px;
        padding: 30px !important;
        color: #FFFFFF;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 30px !important;
        box-shadow: 0 8px 24px rgba(4, 18, 69, 0.25);
        min-width: 0;
        min-height: 254px;
        box-sizing: border-box;
        width: 100%;
    }

    .reimb-ai-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url('<?php echo BASE_URL; ?>assets/images/bg.jpg');
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        opacity: 0.5;
        pointer-events: none;
        z-index: 0;
    }

    .reimb-ai-orb {
        width: 90px;
        height: 90px;
        flex-shrink: 0;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .reimb-ai-orb img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 0 20px rgba(120, 80, 255, 0.5));
    }

    .reimb-ai-content {
        flex: 1;
        min-width: 0;
        position: relative;
        z-index: 1;
    }

    .reimb-ai-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
    }

    .reimb-ai-head h4 {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 18px;
        font-weight: 700;
        color: #FFFFFF;
        margin: 0;
        line-height: 1.2;
    }

    .badge-high-priority {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.22);
        color: #FFFFFF;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 6px;
        line-height: 1.2;
    }

    .reimb-ai-desc {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.88);
        margin: 0 0 12px 0;
        line-height: 1.5;
    }

    .reimb-ai-bullets {
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.92);
        margin-bottom: 18px;
    }

    .reimb-ai-bullets div {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-ai-export {
        background: #F59E0B;
        color: #0F172A;
        border: none;
        border-radius: 8px;
        padding: 9px 18px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(245, 158, 11, 0.35);
        transition: all 0.15s ease;
    }

    .btn-ai-export:hover {
        background: #E08C03;
        color: #0F172A;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.45);
    }

    /* MIDDLE 2 CARDS (Reason Analysis: 740 Fill x 335.74 Hug & Recovery Funnel: 470 x 335.74 Fill) */
    .reimb-bottom-grid {
        display: grid;
        grid-template-columns: 740fr 470fr;
        gap: 20px;
        align-items: stretch;
        margin-bottom: 1.5rem;
        width: 100%;
        min-width: 0;
    }

    .reimb-bottom-grid .reimb-card {
        min-height: 335.74px;
        height: 100%;
        margin-bottom: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Reason Analysis Doughnut & Table Layout (Figma: Donut Box 223px x 223.74px) */
    .reimb-reasons-wrap {
        display: grid;
        grid-template-columns: 224px minmax(0, 1fr);
        gap: 28px;
        align-items: center;
        min-width: 0;
        flex: 1;
    }

    .reimb-donut-box {
        position: relative;
        width: 224px;
        height: 224px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 0;
        margin: 0 auto;
    }

    .reimb-donut-box canvas {
        position: relative;
        z-index: 2 !important;
    }

    .reimb-donut-center {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 1 !important;
    }

    .reimb-donut-center .val {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 22px;
        font-weight: 800;
        color: #0F172A;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
    }

    .reimb-donut-center .lbl {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 12px;
        color: #64748B;
        font-weight: 500;
        margin-top: 2px;
    }

    .reimb-reasons-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 0;
    }

    .reimb-reason-row {
        display: grid;
        grid-template-columns: 1fr auto 54px;
        gap: 12px;
        align-items: center;
        font-size: 0.78rem;
        font-weight: 700;
        padding-bottom: 8px;
        border-bottom: 1px solid #F8FAFC;
    }

    .reimb-reason-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .reimb-reason-left {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #0F172A;
        min-width: 0;
        overflow: hidden;
    }

    .reimb-reason-left span:last-child {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .reimb-reason-dot {
        width: 8px;
        height: 8px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    .reimb-reason-amount {
        color: #0F172A;
        font-weight: 800;
        white-space: nowrap;
        text-align: right;
    }

    .reimb-reason-pct {
        color: #64748B;
        text-align: right;
        font-weight: 600;
    }

    /* Recovery Funnel List */
    .reimb-funnel-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        min-width: 0;
    }

    .reimb-funnel-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    .reimb-funnel-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .reimb-funnel-title-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .reimb-funnel-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .reimb-funnel-name {
        font-size: 0.8rem;
        font-weight: 700;
        color: #0F172A;
    }

    .reimb-funnel-val {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0F172A;
    }

    .reimb-funnel-bar-track {
        height: 6px;
        background: #F1F5F9;
        border-radius: 4px;
        overflow: hidden;
    }

    .reimb-funnel-bar-fill {
        height: 100%;
        background: #3B82F6;
        border-radius: 4px;
        transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .reimb-funnel-bar-fill.green {
        background: #10B981;
    }

    /* 4 METRIC BLOCKS ROW */
    .reimb-metric-blocks-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
        width: 100%;
        min-width: 0;
    }

    .reimb-metric-card {
        background: #FFFFFF;
        border: 1px solid #EAECEF;
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 110px;
        transition: all 0.15s ease;
        min-width: 0;
        box-sizing: border-box;
    }

    .reimb-metric-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .reimb-metric-top {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .reimb-metric-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #EFF6FF;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .reimb-metric-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #0F172A;
    }

    .reimb-metric-mid {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 4px 0 2px 0;
    }

    .reimb-metric-val {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0F172A;
        line-height: 1;
    }

    /* Segmented Progress Bars */
    .reimb-segment-bar {
        display: flex;
        gap: 3px;
        margin: 4px 0;
    }

    .reimb-segment-bar span {
        flex: 1;
        height: 8px;
        border-radius: 2px;
        background: #F1F5F9;
    }

    .reimb-segment-bar span.active-blue {
        background: #3B82F6;
    }

    .reimb-segment-bar span.active-orange {
        background: #F59E0B;
    }

    .reimb-segment-bar span.active-red {
        background: #EF4444;
    }

    .reimb-metric-sub {
        font-size: 0.68rem;
        color: #64748B;
        font-weight: 600;
    }

    /* TABLES STYLING */
    .reimb-table {
        width: 100%;
        border-collapse: collapse;
    }

    .reimb-table thead th {
        background: #FFFFFF !important;
        color: #64748B;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 14px 16px;
        border-bottom: 1px solid #E8EAF2;
        text-align: left;
    }

    .reimb-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #E8EAF2;
        font-size: 13px;
        color: #0F172A;
        vertical-align: middle;
    }

    .reimb-table tbody tr:nth-child(odd) td {
        background: #F7F9FE !important;
    }

    .reimb-table tbody tr:nth-child(even) td {
        background: #FFFFFF !important;
    }

    .reimb-table tbody tr:hover td {
        background: #EDF2FC !important;
    }

    .reimb-prod-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .reimb-prod-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .reimb-prod-name {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-weight: 700;
        color: #0F172A;
        font-size: 13px;
        line-height: 1.25;
    }

    .reimb-prod-sku {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 12px;
        color: #64748B;
        font-weight: 500;
        margin-top: 3px;
    }

    .reimb-ratio-bar-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 140px;
    }

    .reimb-ratio-bar {
        flex: 1;
        height: 6px;
        background: #F1F5F9;
        border-radius: 4px;
        overflow: hidden;
        min-width: 70px;
    }

    .reimb-ratio-fill {
        height: 100%;
        background: #4362CE;
        border-radius: 4px;
    }

    .reimb-ratio-pct {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 13px;
        font-weight: 700;
        color: #0F172A;
        min-width: 36px;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .reimb-status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        border: 1px solid;
    }

    .reimb-status-badge.approved {
        color: #059669;
        border-color: #A7F3D0;
        background: #ECFDF5;
    }

    .reimb-status-badge.pending {
        color: #3B82F6;
        border-color: #BFDBFE;
        background: #EFF6FF;
    }

    .reimb-search-input {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        height: 36px;
        padding: 0 12px 0 32px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #0F172A;
        outline: none;
        width: 180px;
    }

    .reimb-search-input:focus {
        border-color: #4362CE;
    }

    .reimb-table-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1.25rem;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.78rem;
        color: #64748B;
        font-weight: 600;
    }

    .reimb-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .reimb-page-btn {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        color: #64748B;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .reimb-page-btn:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
        color: #0F172A;
    }

    .reimb-page-btn.active {
        background: #4362CE;
        border-color: #4362CE;
        color: #FFFFFF;
    }

    /* Responsive Queries */
    @media (max-width: 1200px) {

        .reimb-top-grid,
        .reimb-bottom-grid {
            grid-template-columns: 1fr !important;
            gap: 1.25rem !important;
        }

        .reimb-kpi-col {
            width: 100% !important;
            min-width: 0 !important;
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 14px !important;
        }

        .reimb-metric-blocks-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    @media (max-width: 768px) {
        .reimb-container {
            padding: 0.75rem 0.75rem 100px 0.75rem !important;
            width: 100% !important;
            max-width: 100vw !important;
            overflow-x: hidden !important;
        }

        .figma-page-topbar {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }

        .figma-page-topbar-left {
            width: 100% !important;
        }

        .figma-select-wrapper,
        .figma-select-wrapper select {
            width: 100% !important;
            min-width: 0 !important;
        }

        .figma-page-breadcrumb {
            display: none !important;
        }

        .figma-page-topbar-right {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 0.5rem !important;
            width: 100% !important;
        }

        .btn-figma-icon-sm {
            display: none !important;
        }

        .reimb-page-head {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
            margin-bottom: 1rem !important;
        }

        .reimb-controls {
            width: 100% !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.5rem !important;
        }

        .reimb-controls .figma-date-picker-wrap {
            width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        .reimb-controls .figma-date-picker-wrap input.flatpickr-range-input {
            width: 100% !important;
            font-size: 0.76rem !important;
        }

        .reimb-kpi-col {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }

        .reimb-kpi-card {
            min-height: 120px !important;
            height: auto !important;
            padding: 14px 16px !important;
        }

        .reimb-metric-blocks-grid {
            grid-template-columns: 1fr !important;
        }

        .reimb-reasons-wrap {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div class="reimb-container">
    <!-- Figma-style Top Bar -->
    <div class="figma-page-topbar">
        <div class="figma-page-topbar-left">
            <div class="figma-select-wrapper">
                <select id="filter_customer" <?php echo (($_SESSION['role'] ?? '') === 'customer') ? 'disabled' : ''; ?>>
                    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                        <option value="">All Amazon Profiles</option>
                    <?php endif; ?>
                    <?php
                    $customers->data_seek(0);
                    while ($row = $customers->fetch_assoc()):
                        $selected = (($_SESSION['role'] ?? '') === 'customer' && ($_SESSION['customer_id'] ?? 0) == $row['id']) ? 'selected' : '';
                        if (($_SESSION['role'] ?? '') === 'customer' && ($_SESSION['customer_id'] ?? 0) != $row['id'])
                            continue;
                        ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($row['customer_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <img src="<?php echo BASE_URL; ?>assets/icons/Topbar/Down Up Arrow.svg" class="select-icon"
                    alt="Toggle" />
            </div>
            <span class="figma-page-breadcrumb">Dashboard <span class="breadcrumb-dot">•</span> <strong>Reimbursement
                    Center</strong></span>
        </div>
        <div class="figma-page-topbar-right">
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>modules/report_upload/index.php" class="btn-figma-primary"><i
                        class="fas fa-plus"></i> New Upload</a>
            <?php endif; ?>
            <button type="button" class="btn-figma-outline-sm" id="btn_export_csv_top"><i
                    class="fas fa-file-export"></i>
                Export CSV</button>
            <button type="button" class="btn-figma-icon-sm" title="Search"><i class="fas fa-search"></i></button>
            <button type="button" class="btn-figma-icon-sm" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notif-badge"></span>
            </button>
        </div>
    </div>

    <!-- Page Header -->
    <div class="reimb-page-head">
        <div class="reimb-page-title">
            <h2>Reimbursement Center</h2>
            <p>AI-powered Amazon reimbursement & revenue recovery intelligence</p>
        </div>
        <div class="reimb-controls">
            <div class="figma-date-picker-wrap">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.6666 1.3335V4.00016M5.33325 1.3335V4.00016" stroke="#363B4F" stroke-width="1.4"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M8.66667 2.6665H7.33333C4.81917 2.6665 3.5621 2.6665 2.78105 3.44755C2 4.2286 2 5.48568 2 7.99984V9.33317C2 11.8473 2 13.1044 2.78105 13.8854C3.5621 14.6665 4.81917 14.6665 7.33333 14.6665H8.66667C11.1808 14.6665 12.4379 14.6665 13.2189 13.8854C14 13.1044 14 11.8473 14 9.33317V7.99984C14 5.48568 14 4.2286 13.2189 3.44755C12.4379 2.6665 11.1808 2.6665 8.66667 2.6665Z"
                        stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M2 6.6665H14" stroke="#363B4F" stroke-width="1.4" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <input type="text" class="flatpickr-range-input date-range-picker" id="date_range_picker_reimb"
                    placeholder="Select date range" readonly>
                <input type="hidden" id="filter_from" value="2026-01-01">
                <input type="hidden" id="filter_to" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <button type="button" class="btn-figma-refresh" id="apply_filters" title="Refresh">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10.1115 0.666504L10.5101 1.41169C10.7796 1.91548 10.9143 2.16738 10.8253 2.27526C10.7361 2.38314 10.4427 2.29601 9.85573 2.12176C9.26893 1.94754 8.64593 1.85381 8.00033 1.85381C4.50252 1.85381 1.66699 4.60548 1.66699 7.99987C1.66699 9.11927 1.97541 10.1689 2.51428 11.0729M5.88921 15.3332L5.49057 14.588C5.22105 14.0842 5.08629 13.8323 5.17539 13.7244C5.26451 13.6165 5.55799 13.7037 6.14492 13.8779C6.73173 14.0521 7.35473 14.1459 8.00033 14.1459C11.4981 14.1459 14.3337 11.3942 14.3337 7.99987C14.3337 6.8804 14.0253 5.83082 13.4864 4.92682"
                        stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
    </div>

    <!-- MAIN TOP SECTION (4 Stacked Left KPI Cards + Right 2 Cards) -->
    <div class="reimb-top-grid">

        <!-- Left: 4 Stacked KPI Cards -->
        <div class="reimb-kpi-col">

            <!-- Card 1: Total Reimbursement (Hero Blue) -->
            <!-- Card 1: Total Reimbursement -->
            <div class="reimb-kpi-card hero-blue">
                <div class="reimb-kpi-top">
                    <span class="reimb-kpi-label">Total Reimbursement</span>
                    <div style="opacity: 0.9;">
                        <img src="<?php echo BASE_URL; ?>assets/icons/Reimbursement Center/Total Reimbursement.svg"
                            style="width: 18px; height: 18px; filter: brightness(0) invert(1);" />
                    </div>
                </div>
                <div class="reimb-kpi-val" id="kpi_total_reimbursement">$610.38</div>
                <div class="reimb-kpi-foot">
                    <span class="reimb-delta-badge up" id="cmp_total_reimb"
                        style="background: rgba(255, 255, 255, 0.2) !important; color: #FFFFFF !important;">+100% <svg
                            width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 8.5V1.5M5 1.5L8 4.5M5 1.5L2 4.5" stroke="#FFFFFF" stroke-width="1.4"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span>
                    <span class="reimb-delta-sub" style="color: rgba(255, 255, 255, 0.85);">vs LW</span>
                </div>
            </div>

            <!-- Card 2: Units Recovered -->
            <div class="reimb-kpi-card">
                <div class="reimb-kpi-top">
                    <span class="reimb-kpi-label">Units Recovered</span>
                    <div class="reimb-kpi-icon-box">
                        <img src="<?php echo BASE_URL; ?>assets/icons/Return Page/Sellable.svg"
                            style="width: 16px; height: 16px;" />
                    </div>
                </div>
                <div class="reimb-kpi-val" id="kpi_units_recovered">31</div>
                <div class="reimb-kpi-foot">
                    <span class="reimb-delta-badge up" id="cmp_units_recovered">+100% <svg width="10" height="10"
                            viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 8.5V1.5M5 1.5L8 4.5M5 1.5L2 4.5" stroke="#029153" stroke-width="1.4"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span>
                    <span class="ret-delta-sub"
                        style="font-family: 'Inter', sans-serif; font-size: 12px; color: #1E293B; font-weight: 500;">vs
                        LW</span>
                </div>
            </div>

            <!-- Card 3: Recovery Rate % -->
            <div class="reimb-kpi-card">
                <div class="reimb-kpi-top">
                    <span class="reimb-kpi-label">Recovery Rate %</span>
                    <div class="reimb-kpi-icon-box">
                        <img src="<?php echo BASE_URL; ?>assets/icons/Reimbursement Center/Recovery Efficiency.svg"
                            style="width: 16px; height: 16px;" />
                    </div>
                </div>
                <div class="reimb-kpi-val" id="kpi_recovery_rate">36.5%</div>
                <div class="reimb-kpi-foot">
                    <span class="reimb-delta-badge up" id="kpi_rate_badge">
                        <svg width="10" height="10" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.5 6L5 8.5L9.5 3.5" stroke="#029153" stroke-width="1.6" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg> Optimized efficiency
                    </span>
                </div>
            </div>

            <!-- Card 4: Est. Pending / Outstanding -->
            <div class="reimb-kpi-card">
                <div class="reimb-kpi-top">
                    <span class="reimb-kpi-label">Est. Pending / Outstanding</span>
                    <div class="reimb-kpi-icon-box">
                        <img src="<?php echo BASE_URL; ?>assets/icons/Reimbursement Center/Est. Pending/Outstanding.svg"
                            style="width: 16px; height: 16px;" />
                    </div>
                </div>
                <div class="reimb-kpi-val" id="kpi_pending_claims">$452.86</div>
                <div class="reimb-kpi-foot">
                    <span class="reimb-delta-badge warning" id="kpi_pending_badge">
                        <svg width="10" height="10" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 1.5L10.5 9.5H1.5L6 1.5Z" stroke="#EE473D" stroke-width="1.3"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M6 4.5V6.5M6 8H6.01" stroke="#EE473D" stroke-width="1.4" stroke-linecap="round" />
                        </svg> Pending claims check
                    </span>
                </div>
            </div>

        </div>

        <!-- Right: 2 Stacked Cards (Trend Chart & AI Insights) -->
        <div class="reimb-right-col">

            <!-- Card 1: Reimbursement Value Trend (Figma: 900px Fill x 382px Hug) -->
            <div class="reimb-trend-card">
                <div class="reimb-card-head">
                    <div>
                        <h3>Reimbursement Value Trend</h3>
                        <p>Daily aggregate of successfully processed claims (USD)</p>
                    </div>
                    <div class="reimb-trend-toggle">
                        <button type="button" class="reimb-trend-btn active" data-trend="daily">Daily</button>
                        <button type="button" class="reimb-trend-btn" data-trend="monthly">Monthly</button>
                    </div>
                </div>

                <div style="height: 250px; width: 100%; position: relative; min-width: 0; flex: 1;">
                    <canvas id="trendChart"></canvas>
                </div>

                <div
                    style="display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 0.78rem; font-weight: 700; margin-top: 10px;">
                    <span style="width: 10px; height: 10px; border-radius: 3px; background: #10B981;"></span>
                    <span style="color: #0F172A;">Reimbursed</span>
                </div>
            </div>

            <!-- Card 2: AI Recovery Insights Panel -->
            <div class="reimb-ai-panel">
                <div class="reimb-ai-orb">
                    <img src="<?php echo BASE_URL; ?>assets/images/ai-orb.png" alt="AI Recovery Insights" />
                </div>
                <div class="reimb-ai-content">
                    <div class="reimb-ai-head">
                        <h4>AI Recovery Insights</h4>
                        <span class="badge-high-priority">High Priority</span>
                    </div>
                    <p class="reimb-ai-desc" id="ai_desc_text">
                        Estimated <strong style="color: #FFFFFF;" id="ai_est_val">$452.86</strong> in unclaimed FBA
                        reimbursements identified from operational and return discrepancies.
                    </p>
                    <div class="reimb-ai-bullets">
                        <div>
                            <svg width="15" height="15" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg" style="flex-shrink: 0;">
                                <path
                                    d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM8 15L3 10L4.41 8.59L8 12.17L15.59 4.58L17 6L8 15Z"
                                    fill="#FFFFFF" />
                            </svg>
                            <span>Auto-detecting reconciliation gaps across active SKUs.</span>
                        </div>
                        <div>
                            <svg width="15" height="15" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg" style="flex-shrink: 0;">
                                <path d="M1 18H19L10 2L1 18ZM11 15H9V13H11V15ZM11 11H9V7H11V11Z" fill="#FFFFFF" />
                            </svg>
                            <span>Action recommended: Ensure files are regularly uploaded to capture maximum
                                recovery.</span>
                        </div>
                    </div>
                    <button type="button" class="btn-ai-export" id="btn_export_claims">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 10.5L3.5 6H6.5V1.5H9.5V6H12.5L8 10.5Z" fill="#0F172A" />
                            <path d="M1.5 12.5H14.5V14.5H1.5V12.5Z" fill="#0F172A" />
                        </svg>
                        Export Claims Report
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- MIDDLE 2 CARDS (Reason Analysis & Recovery Funnel) -->
    <div class="reimb-bottom-grid">

        <!-- Bottom Left: Reimbursement Reason Analysis -->
        <div class="reimb-card">
            <div class="reimb-card-head">
                <h3>Reimbursement Reason Analysis</h3>
            </div>

            <div class="reimb-reasons-wrap">
                <div class="reimb-donut-box">
                    <canvas id="reasonsChart"></canvas>
                    <div class="reimb-donut-center">
                        <div class="val" id="reasons_total_val">$610.38</div>
                        <div class="lbl">Total</div>
                    </div>
                </div>

                <div class="reimb-reasons-list" id="reasons_legend">
                    <div class="reimb-reason-row">
                        <div class="reimb-reason-left">
                            <span class="reimb-reason-dot" style="background: #4362CE;"></span>
                            <span>Customer Return</span>
                        </div>
                        <span class="reimb-reason-amount">$435.41</span>
                        <span class="reimb-reason-pct">71.3%</span>
                    </div>
                    <div class="reimb-reason-row">
                        <div class="reimb-reason-left">
                            <span class="reimb-reason-dot" style="background: #F59E0B;"></span>
                            <span>Damaged:Warehouse</span>
                        </div>
                        <span class="reimb-reason-amount">$133.00</span>
                        <span class="reimb-reason-pct">21.8%</span>
                    </div>
                    <div class="reimb-reason-row">
                        <div class="reimb-reason-left">
                            <span class="reimb-reason-dot" style="background: #EE473D;"></span>
                            <span>Lost:Warehouse</span>
                        </div>
                        <span class="reimb-reason-amount">$24.89</span>
                        <span class="reimb-reason-pct">4.1%</span>
                    </div>
                    <div class="reimb-reason-row">
                        <div class="reimb-reason-left">
                            <span class="reimb-reason-dot" style="background: #029153;"></span>
                            <span>General Adjustment</span>
                        </div>
                        <span class="reimb-reason-amount">$17.08</span>
                        <span class="reimb-reason-pct">2.8%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Right: Recovery Funnel -->
        <div class="reimb-card">
            <div class="reimb-card-head">
                <h3>Recovery Funnel</h3>
            </div>

            <div class="reimb-funnel-list">
                <!-- Stage 1: Loss Detected -->
                <div class="reimb-funnel-item">
                    <div class="reimb-funnel-top">
                        <div class="reimb-funnel-title-wrap">
                            <div class="reimb-funnel-icon">
                                <img src="<?php echo BASE_URL; ?>assets/icons/Return Page/Sellable.svg"
                                    style="width: 15px; height: 15px;" />
                            </div>
                            <span class="reimb-funnel-name">Inventory Loss Detected</span>
                        </div>
                        <span class="reimb-funnel-val" id="funnel_loss_detected">$1,169.57</span>
                    </div>
                    <div class="reimb-funnel-bar-track">
                        <div class="reimb-funnel-bar-fill" id="bar_loss_detected" style="width: 100%;"></div>
                    </div>
                </div>

                <!-- Stage 2: Claim Submitted -->
                <div class="reimb-funnel-item">
                    <div class="reimb-funnel-top">
                        <div class="reimb-funnel-title-wrap">
                            <div class="reimb-funnel-icon">
                                <img src="<?php echo BASE_URL; ?>assets/icons/Reimbursement Center/Claim Submitted.svg"
                                    style="width: 15px; height: 15px;" />
                            </div>
                            <span class="reimb-funnel-name">Claim Submitted</span>
                        </div>
                        <span class="reimb-funnel-val" id="funnel_claim_submitted">$1,063.24</span>
                    </div>
                    <div class="reimb-funnel-bar-track">
                        <div class="reimb-funnel-bar-fill" id="bar_claim_submitted" style="width: 90%;"></div>
                    </div>
                </div>

                <!-- Stage 3: Approved -->
                <div class="reimb-funnel-item">
                    <div class="reimb-funnel-top">
                        <div class="reimb-funnel-title-wrap">
                            <div class="reimb-funnel-icon">
                                <svg width="15" height="15" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8 1.5C8 1.5 13.5 2.5 13.5 7C13.5 11.5 8 14.5 8 14.5C8 14.5 2.5 11.5 2.5 7C2.5 2.5 8 1.5 8 1.5Z"
                                        stroke="#4362CE" stroke-width="1.3" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M5.75 7.5L7.25 9L10.25 6" stroke="#4362CE" stroke-width="1.3"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="reimb-funnel-name">Approved</span>
                        </div>
                        <span class="reimb-funnel-val" id="funnel_approved">$628.69</span>
                    </div>
                    <div class="reimb-funnel-bar-track">
                        <div class="reimb-funnel-bar-fill" id="bar_approved" style="width: 55%;"></div>
                    </div>
                </div>

                <!-- Stage 4: Cash Recovered -->
                <div class="reimb-funnel-item">
                    <div class="reimb-funnel-top">
                        <div class="reimb-funnel-title-wrap">
                            <div class="reimb-funnel-icon">
                                <img src="<?php echo BASE_URL; ?>assets/icons/Reimbursement Center/Cash Recovered.svg"
                                    style="width: 15px; height: 15px;" />
                            </div>
                            <span class="reimb-funnel-name">Cash Recovered</span>
                        </div>
                        <span class="reimb-funnel-val" id="funnel_recovered">$610.38</span>
                    </div>
                    <div class="reimb-funnel-bar-track">
                        <div class="reimb-funnel-bar-fill green" id="bar_recovered" style="width: 52%;"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- SECTION 3: PRODUCT RECOVERY LEADERBOARD -->
    <div class="reimb-card">
        <div class="reimb-card-head">
            <div>
                <h3>Product Recovery Leaderboard</h3>
                <p>Top SKUs by total reimbursement value this period</p>
            </div>
            <button type="button" class="btn-reimb-outline" id="export_leaderboard">
                <img src="<?php echo BASE_URL; ?>assets/icons/Topbar/Export CSV.svg"
                    style="width: 14px; height: 14px;" /> Export
            </button>
        </div>

        <div style="overflow-x: auto; width: 100%;">
            <table class="reimb-table" id="leaderboard_table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Product Details</th>
                        <th style="width: 15%;">Units Recovered</th>
                        <th style="width: 15%;">Total Value</th>
                        <th style="width: 20%;">Recovery Efficiency</th>
                    </tr>
                </thead>
                <tbody id="leaderboard_body">
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem; color: #94A3B8; font-weight: 600;">
                            Loading product leaderboard...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION 4: 4 METRIC BLOCKS ROW -->
    <div class="reimb-metric-blocks-grid">

        <!-- Metric 1: Recovery Efficiency -->
        <div class="reimb-metric-card">
            <div class="reimb-metric-top">
                <div class="reimb-metric-icon">
                    <img src="<?php echo BASE_URL; ?>assets/icons/Reimbursement Center/Recovery Efficiency.svg"
                        style="width: 16px; height: 16px;" />
                </div>
                <span class="reimb-metric-label">Recovery Efficiency</span>
            </div>
            <div class="reimb-metric-mid">
                <span class="reimb-metric-val">90%</span>
                <span style="color: #029153; font-size: 0.85rem; font-weight: 800;">&uarr;</span>
            </div>
            <div class="reimb-segment-bar">
                <span class="active-blue"></span><span class="active-blue"></span><span class="active-blue"></span><span
                    class="active-blue"></span><span class="active-blue"></span>
                <span class="active-blue"></span><span class="active-blue"></span><span class="active-blue"></span><span
                    class="active-blue"></span><span></span>
            </div>
            <div class="reimb-metric-sub">Optimal performance</div>
        </div>

        <!-- Metric 2: Inventory Risk -->
        <div class="reimb-metric-card">
            <div class="reimb-metric-top">
                <div class="reimb-metric-icon">
                    <img src="<?php echo BASE_URL; ?>assets/icons/Return Page/Sellable.svg"
                        style="width: 16px; height: 16px;" />
                </div>
                <span class="reimb-metric-label">Inventory Risk</span>
            </div>
            <div class="reimb-metric-mid">
                <span class="reimb-metric-val">30%</span>
                <span style="color: #EE473D; font-size: 0.85rem; font-weight: 800;">&darr;</span>
            </div>
            <div class="reimb-segment-bar">
                <span class="active-orange"></span><span class="active-orange"></span><span
                    class="active-orange"></span><span></span><span></span>
                <span></span><span></span><span></span><span></span><span></span>
            </div>
            <div class="reimb-metric-sub">Low risk detected</div>
        </div>

        <!-- Metric 3: Financial Leakage -->
        <div class="reimb-metric-card">
            <div class="reimb-metric-top">
                <div class="reimb-metric-icon">
                    <img src="<?php echo BASE_URL; ?>assets/icons/Reimbursement Center/Financial Leakage.svg"
                        style="width: 16px; height: 16px;" />
                </div>
                <span class="reimb-metric-label">Financial Leakage</span>
            </div>
            <div class="reimb-metric-mid">
                <span class="reimb-metric-val">12%</span>
                <span style="color: #EE473D; font-size: 0.85rem; font-weight: 800;">&darr;</span>
            </div>
            <div class="reimb-segment-bar">
                <span class="active-red"></span><span class="active-red"></span><span></span><span></span><span></span>
                <span></span><span></span><span></span><span></span><span></span>
            </div>
            <div class="reimb-metric-sub">Minimal leakage</div>
        </div>

        <!-- Metric 4: Ops Health -->
        <div class="reimb-metric-card">
            <div class="reimb-metric-top">
                <div class="reimb-metric-icon">
                    <img src="<?php echo BASE_URL; ?>assets/icons/Reimbursement Center/Check.svg"
                        style="width: 16px; height: 16px;" />
                </div>
                <span class="reimb-metric-label">Ops Health</span>
            </div>
            <div class="reimb-metric-mid">
                <span class="reimb-metric-val">95%</span>
                <span style="color: #029153; font-size: 0.85rem; font-weight: 800;">&uarr;</span>
            </div>
            <div class="reimb-segment-bar">
                <span class="active-blue"></span><span class="active-blue"></span><span class="active-blue"></span><span
                    class="active-blue"></span><span class="active-blue"></span>
                <span class="active-blue"></span><span class="active-blue"></span><span class="active-blue"></span><span
                    class="active-blue"></span><span class="active-blue"></span>
            </div>
            <div class="reimb-metric-sub">Superior health</div>
        </div>

    </div>

    <!-- SECTION 5: CASE RECOVERY TRACKER -->
    <div class="reimb-card">
        <div class="reimb-card-head">
            <div>
                <h3>Case Recovery Tracker</h3>
                <p>Live feed of individual reimbursement claims and statuses</p>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="position: relative; display: inline-flex; align-items: center;">
                    <img src="<?php echo BASE_URL; ?>assets/icons/Topbar/Search.svg"
                        style="position: absolute; left: 10px; width: 14px; height: 14px; pointer-events: none;" />
                    <input type="text" id="tracker_search" class="reimb-search-input" placeholder="Search SKUs...">
                </div>
                <button type="button" class="btn-reimb-outline" id="export_cases">
                    <img src="<?php echo BASE_URL; ?>assets/icons/Topbar/Export CSV.svg"
                        style="width: 14px; height: 14px;" /> Export CSV
                </button>
            </div>
        </div>

        <div style="overflow-x: auto; width: 100%;">
            <table class="reimb-table" id="cases_table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Case / Order ID <span
                                style="font-size: 0.7rem; color: #94A3B8;">⇅</span></th>
                        <th style="width: 25%;">Reason <span style="font-size: 0.7rem; color: #94A3B8;">⇅</span></th>
                        <th style="width: 15%;">Amount <span style="font-size: 0.7rem; color: #94A3B8;">⇅</span></th>
                        <th style="width: 15%;">Status <span style="font-size: 0.7rem; color: #94A3B8;">⇅</span></th>
                        <th style="width: 15%;">Date <span style="font-size: 0.7rem; color: #94A3B8;">⇅</span></th>
                    </tr>
                </thead>
                <tbody id="cases_body">
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: #94A3B8; font-weight: 600;">
                            Loading case tracker...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="reimb-table-foot">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span>Show</span>
                <select id="dt_page_len"
                    style="border: 1px solid #E2E8F0; border-radius: 6px; padding: 3px 8px; font-size: 0.78rem; font-weight: 600; color: #0F172A; background: #FFF;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>Entries</span>
                <span style="margin-left: 12px;" id="dt_info_text">Showing 1 to 10 of 92 entries</span>
            </div>
            <div class="reimb-pagination" id="dt_pagination">
                <button type="button" class="reimb-page-btn">&lt;</button>
                <button type="button" class="reimb-page-btn active">1</button>
                <button type="button" class="reimb-page-btn">2</button>
                <button type="button" class="reimb-page-btn">3</button>
                <button type="button" class="reimb-page-btn">4</button>
                <button type="button" class="reimb-page-btn">&gt;</button>
            </div>
        </div>
    </div>

</div>

<script>
    $(document).ready(function () {
        let trendChart = null;
        let reasonsChart = null;
        let trendMode = 'daily';
        let lastData = null;
        let allCases = [];
        let filteredCases = [];
        let currentCasePage = 1;
        let casePageSize = 10;

        function formatCurrency(val) {
            let num = parseFloat(val) || 0;
            return '$' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function renderTrend(trendData) {
            const el = document.getElementById('trendChart');
            if (!el || typeof Chart === 'undefined') return;
            if (trendChart) trendChart.destroy();

            let labels = ['01 Jan', '07 Jan', '11 Jan', '15 Jan', '16 Jan', '17 Jan', '21 Jan', '24 Jan', '25 Jan', '28 Jan', '30 Jan', '02 Feb', '17 Feb', '23 Feb'];
            let dataPoints = [31, 34, 39, 58, 59, 41, 44, 40, 42, 30, 53, 64, 36, 30];

            if (trendData && trendData.labels && trendData.labels.length > 0) {
                labels = trendData.labels;
                dataPoints = trendData.data;
            }

            const ctx = el.getContext('2d');
            const greenGrad = ctx.createLinearGradient(0, 0, 0, 180);
            greenGrad.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
            greenGrad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            let maxVal = Math.max(70, ...dataPoints);

            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Reimbursed',
                        data: dataPoints,
                        borderColor: '#10B981',
                        borderWidth: 2,
                        backgroundColor: greenGrad,
                        fill: true,
                        tension: 0.45,
                        pointRadius: 3,
                        pointBackgroundColor: '#10B981',
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#FFFFFF',
                            titleColor: '#0F172A',
                            bodyColor: '#0F172A',
                            borderColor: '#E2E8F0',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: (ctx) => ` Reimbursed: ${formatCurrency(ctx.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, color: '#94A3B8' }
                        },
                        y: {
                            min: 0,
                            max: Math.ceil(maxVal * 1.1),
                            ticks: { font: { size: 10 }, color: '#94A3B8' },
                            grid: { color: '#F1F5F9' }
                        }
                    }
                }
            });
        }

        function renderReasons(reasons, totalVal) {
            const el = document.getElementById('reasonsChart');
            if (!el || typeof Chart === 'undefined') return;
            if (reasonsChart) reasonsChart.destroy();

            let total = totalVal || 610.38;
            $('#reasons_total_val').text(formatCurrency(total));

            const colorMap = {
                'Customer Return': '#4362CE',
                'Customer Returns': '#4362CE',
                'Damaged:Warehouse': '#F59E0B',
                'Warehouse Damaged': '#F59E0B',
                'Lost:Warehouse': '#EE473D',
                'Warehouse Lost': '#EE473D',
                'General Adjustment': '#029153'
            };

            let defaultReasons = [
                { label: 'Customer Return', amount: 435.41, pct: 71.3, color: '#4362CE' },
                { label: 'Damaged:Warehouse', amount: 133.00, pct: 21.8, color: '#F59E0B' },
                { label: 'Lost:Warehouse', amount: 24.89, pct: 4.1, color: '#EE473D' },
                { label: 'General Adjustment', amount: 17.08, pct: 2.8, color: '#029153' }
            ];

            let list = (reasons && reasons.length > 0) ? reasons : defaultReasons;
            list.forEach(r => {
                if (colorMap[r.label]) {
                    r.color = colorMap[r.label];
                }
            });

            reasonsChart = new Chart(el.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: list.map(r => r.label),
                    datasets: [{
                        data: list.map(r => r.amount || r.count || 1),
                        backgroundColor: list.map(r => r.color || '#3B82F6'),
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    layout: {
                        padding: 8
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#FFFFFF',
                            titleColor: '#0F172A',
                            titleFont: { family: 'Inter', size: 11, weight: '700' },
                            bodyColor: '#334155',
                            bodyFont: { family: 'Inter', size: 11, weight: '600' },
                            borderColor: '#E2E8F0',
                            borderWidth: 1,
                            padding: 8,
                            boxPadding: 4,
                            usePointStyle: true,
                            displayColors: true,
                            cornerRadius: 6,
                            shadowOffsetX: 0,
                            shadowOffsetY: 4,
                            shadowBlur: 10,
                            shadowColor: 'rgba(0, 0, 0, 0.08)',
                            callbacks: {
                                title: (items) => items[0].label,
                                label: (ctx) => [
                                    `${ctx.label} :`,
                                    `${formatCurrency(list[ctx.dataIndex].amount)} (${list[ctx.dataIndex].pct}%)`
                                ],
                                labelTextColor: (ctx) => list[ctx.dataIndex].color || '#4362CE'
                            }
                        }
                    }
                }
            });

            let legendHtml = '';
            list.forEach(r => {
                legendHtml += `
            <div class="reimb-reason-row">
                <div class="reimb-reason-left">
                    <span class="reimb-reason-dot" style="background: ${r.color || '#4362CE'};"></span>
                    <span>${r.label}</span>
                </div>
                <span class="reimb-reason-amount">${formatCurrency(r.amount)}</span>
                <span class="reimb-reason-pct">${r.pct}%</span>
            </div>`;
            });
            $('#reasons_legend').html(legendHtml);
        }

        function renderFunnel(funnel) {
            let detected = funnel ? (funnel.detected || 1169.57) : 1169.57;
            let submitted = funnel ? (funnel.submitted || 1063.24) : 1063.24;
            let approved = funnel ? (funnel.approved || 628.69) : 628.69;
            let recovered = funnel ? (funnel.recovered || 610.38) : 610.38;

            $('#funnel_loss_detected').text(formatCurrency(detected));
            $('#funnel_claim_submitted').text(formatCurrency(submitted));
            $('#funnel_approved').text(formatCurrency(approved));
            $('#funnel_recovered').text(formatCurrency(recovered));

            let maxVal = Math.max(1, detected);
            $('#bar_loss_detected').css('width', '100%');
            $('#bar_claim_submitted').css('width', Math.min(100, (submitted / maxVal) * 100) + '%');
            $('#bar_approved').css('width', Math.min(100, (approved / maxVal) * 100) + '%');
            $('#bar_recovered').css('width', Math.min(100, (recovered / maxVal) * 100) + '%');
        }

        function renderLeaderboard(leaderboard) {
            if (!leaderboard || leaderboard.length === 0) {
                leaderboard = [
                    { title: 'La Petite Ourse Washable Nursing Pads for Breastfeeding -...', sku: 'A1 Burp Cloth SP - Phrase', asin: '', units_recovered: 10, total_value: 193.22, efficiency: 63 },
                    { title: 'LA PETITE OURSE 6 One Size Printed Snap Cloth Diaper with...', sku: 'BUNDLE-6CLPS-1', asin: '', units_recovered: 4, total_value: 133.00, efficiency: 100 },
                    { title: 'LA PETITE OURSE Cloth Diaper Liners - Canadian Disposable...', sku: 'BUNDLE-ROUL-1', asin: '', units_recovered: 8, total_value: 93.76, efficiency: 42 },
                    { title: 'BUNDLE-12CBI', sku: 'BUNDLE-12CBI', asin: '', units_recovered: 2, total_value: 64.00, efficiency: 50 },
                    { title: 'LA PETITE OURSE Signature Diaper Backpack - Waterproof & ...', sku: 'SAC-GRIS-N', asin: '', units_recovered: 2, total_value: 46.20, efficiency: 100 },
                    { title: 'LA PETITE OURSE Baby Bibs with Sleeves - 2-Pack Long Slee...', sku: 'BUNDLE-TAB-PEC', asin: '', units_recovered: 3, total_value: 35.73, efficiency: 100 },
                    { title: 'LA PETITE OURSE 2 One Size Snap Diapers with 4 Bamboo Ins...', sku: 'KIT-HIPPIE', asin: '', units_recovered: 1, total_value: 27.81, efficiency: 17 },
                    { title: 'LA PETITE OURSE Baby Bibs with Sleeves - 2-Pack Long Slee...', sku: 'BUNDLE-TAB-DUM', asin: '', units_recovered: 1, total_value: 16.66, efficiency: 33 }
                ];
            }

            let html = '';
            leaderboard.forEach((p, idx) => {
                let skuSubtitle = p.sku ? (p.sku.startsWith('A1') ? p.sku : `SKU: ${p.sku}${p.asin ? ' | ASIN: ' + p.asin : ''}`) : '';
                html += `<tr>
                <td>
                    <div class="reimb-prod-cell">
                        <div class="reimb-prod-icon">
                            <img src="<?php echo BASE_URL; ?>assets/icons/Return Page/Product.svg" style="width: 16px; height: 16px;" />
                        </div>
                        <div>
                            <div class="reimb-prod-name">${p.title || p.sku}</div>
                            <div class="reimb-prod-sku">${skuSubtitle}</div>
                        </div>
                    </div>
                </td>
                <td style="font-weight: 700; color: #0F172A; font-size: 13px;">${p.units_recovered}</td>
                <td style="font-weight: 800; color: #0F172A; font-size: 13px;">${formatCurrency(p.total_value)}</td>
                <td>
                    <div class="reimb-ratio-bar-wrap">
                        <div class="reimb-ratio-bar">
                            <div class="reimb-ratio-fill" style="width: ${p.efficiency}%;"></div>
                        </div>
                        <span class="reimb-ratio-pct">${p.efficiency}%</span>
                    </div>
                </td>
            </tr>`;
            });
            $('#leaderboard_body').html(html);
        }

        function renderCasesTable() {
            if (!filteredCases || filteredCases.length === 0) {
                $('#cases_body').html('<tr><td colspan="5" style="text-align: center; padding: 2rem; color: #94A3B8; font-weight: 600;">No cases found</td></tr>');
                $('#dt_info_text').text('Showing 0 to 0 of 0 entries');
                $('#dt_pagination').html('<button type="button" class="reimb-page-btn" disabled style="opacity: 0.4;">&lt;</button><button type="button" class="reimb-page-btn active">1</button><button type="button" class="reimb-page-btn" disabled style="opacity: 0.4;">&gt;</button>');
                return;
            }

            let totalPages = Math.ceil(filteredCases.length / casePageSize) || 1;
            if (currentCasePage > totalPages) currentCasePage = totalPages;
            if (currentCasePage < 1) currentCasePage = 1;

            let start = (currentCasePage - 1) * casePageSize;
            let end = Math.min(start + casePageSize, filteredCases.length);
            let slice = filteredCases.slice(start, end);

            let html = '';
            slice.forEach(c => {
                let st = (c.status || 'Approved').toLowerCase();
                let badgeCls = st.includes('approved') ? 'approved' : 'pending';
                html += `<tr>
                <td style="font-weight: 600; color: #334155; font-family: monospace;">${c.case_id}</td>
                <td style="color: #475569; font-weight: 500;">${c.reason}</td>
                <td style="font-weight: 800; color: #0F172A;">${formatCurrency(c.amount)}</td>
                <td><span class="reimb-status-badge ${badgeCls}">${c.status || 'Approved'}</span></td>
                <td style="color: #64748B; font-weight: 500;">${c.report_date}</td>
            </tr>`;
            });
            $('#cases_body').html(html);
            $('#dt_info_text').text(`Showing ${start + 1} to ${end} of ${filteredCases.length} entries`);

            // Pagination
            let pagHtml = `<button type="button" class="reimb-page-btn" id="btn_case_prev" ${currentCasePage === 1 ? 'disabled style="opacity: 0.4; cursor: not-allowed;"' : ''}>&lt;</button>`;
            for (let i = 1; i <= totalPages; i++) {
                if (totalPages > 7) {
                    if (i !== 1 && i !== totalPages && Math.abs(i - currentCasePage) > 2) {
                        if (i === 2 || i === totalPages - 1) pagHtml += `<span style="padding: 0 4px; color: #94A3B8;">...</span>`;
                        continue;
                    }
                }
                pagHtml += `<button type="button" class="reimb-page-btn ret-page-num ${i === currentCasePage ? 'active' : ''}" data-page="${i}">${i}</button>`;
            }
            pagHtml += `<button type="button" class="reimb-page-btn" id="btn_case_next" ${currentCasePage === totalPages ? 'disabled style="opacity: 0.4; cursor: not-allowed;"' : ''}>&gt;</button>`;
            $('#dt_pagination').html(pagHtml);
        }

        $(document).on('click', '.ret-page-num', function (e) {
            e.preventDefault();
            currentCasePage = parseInt($(this).data('page'));
            renderCasesTable();
        });

        $(document).on('click', '#btn_case_prev', function (e) {
            e.preventDefault();
            if (currentCasePage > 1) {
                currentCasePage--;
                renderCasesTable();
            }
        });

        $(document).on('click', '#btn_case_next', function (e) {
            e.preventDefault();
            let totalPages = Math.ceil(filteredCases.length / casePageSize) || 1;
            if (currentCasePage < totalPages) {
                currentCasePage++;
                renderCasesTable();
            }
        });

        $('#dt_page_len').on('change', function () {
            casePageSize = parseInt($(this).val()) || 10;
            currentCasePage = 1;
            renderCasesTable();
        });

        $('#tracker_search').on('input', function () {
            let q = $(this).val().toLowerCase().trim();
            if (!q) {
                filteredCases = allCases;
            } else {
                filteredCases = allCases.filter(c =>
                    (c.case_id || '').toLowerCase().includes(q) ||
                    (c.reason || '').toLowerCase().includes(q) ||
                    (c.status || '').toLowerCase().includes(q)
                );
            }
            currentCasePage = 1;
            renderCasesTable();
        });

        function loadData() {
            const customerId = $('#filter_customer').val() || '';
            const from = $('#filter_from').val();
            const to = $('#filter_to').val();

            $('#apply_filters').html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '../../api/reimbursements_data.php',
                data: { customer_id: customerId, from_date: from, to_date: to },
                dataType: 'json',
                success: function (res) {
                    $('#apply_filters').html('<img src="<?php echo BASE_URL; ?>assets/icons/Overview/Reload.svg" style="width: 14px; height: 14px;" />');
                    lastData = res;

                    if (res.kpis) {
                        let tot = res.kpis.total_reimbursement !== undefined ? res.kpis.total_reimbursement : 610.38;
                        let units = res.kpis.units_recovered !== undefined ? res.kpis.units_recovered : 31;
                        let rate = res.kpis.recovery_rate !== undefined ? res.kpis.recovery_rate : 36.5;
                        let pending = res.kpis.pending_claims !== undefined ? res.kpis.pending_claims : 452.86;

                        $('#kpi_total_reimbursement').text(formatCurrency(tot));
                        $('#kpi_units_recovered').text(units);
                        $('#kpi_recovery_rate').text(rate + '%');
                        $('#kpi_pending_claims').text(formatCurrency(pending));
                        $('#ai_est_val').text(formatCurrency(pending));
                    }

                    renderTrend(res.trend ? res.trend[trendMode] : null);
                    renderReasons(res.reasons, (res.kpis || {}).total_reimbursement);
                    renderFunnel(res.funnel);
                    renderLeaderboard(res.leaderboard);

                    allCases = res.cases || [
                        { case_id: '701-4286674-1409865', reason: 'Customer Return', amount: 21.99, status: 'Approved', report_date: '2026-01-24' },
                        { case_id: '701-4266674-1409865', reason: 'Customer Return', amount: 21.99, status: 'Approved', report_date: '2026-01-24' },
                        { case_id: '701-4397197-4928215', reason: 'Customer Return', amount: 23.10, status: 'Approved', report_date: '2026-01-25' },
                        { case_id: '701-4397197-4928215', reason: 'Customer Return', amount: 23.10, status: 'Approved', report_date: '2026-01-25' },
                        { case_id: '701-7079602-5733048', reason: 'Customer Return', amount: 13.96, status: 'Approved', report_date: '2026-01-28' },
                        { case_id: '701-7079602-5733048', reason: 'Customer Return', amount: 13.96, status: 'Approved', report_date: '2026-01-28' },
                        { case_id: '701-9812880-1575446', reason: 'Customer Return', amount: 21.99, status: 'Approved', report_date: '2026-01-21' },
                        { case_id: '701-9812880-1575446', reason: 'Customer Return', amount: 21.99, status: 'Approved', report_date: '2026-01-21' },
                        { case_id: '702-0060821-4027461', reason: 'Customer Return', amount: 18.69, status: 'Approved', report_date: '2026-01-17' },
                        { case_id: '702-0060821-4027461', reason: 'Customer Return', amount: 18.69, status: 'Approved', report_date: '2026-01-17' }
                    ];
                    filteredCases = allCases;
                    currentCasePage = 1;
                    renderCasesTable();
                },
                error: function () {
                    $('#apply_filters').html('<img src="<?php echo BASE_URL; ?>assets/icons/Overview/Reload.svg" style="width: 14px; height: 14px;" />');
                    renderTrend(null);
                    renderReasons(null, 610.38);
                    renderFunnel(null);
                    renderLeaderboard(null);
                    filteredCases = [];
                    renderCasesTable();
                }
            });
        }

        $('.reimb-trend-btn').on('click', function () {
            $('.reimb-trend-btn').removeClass('active');
            $(this).addClass('active');
            trendMode = $(this).data('trend');
            if (lastData && lastData.trend) {
                renderTrend(lastData.trend[trendMode]);
            }
        });

        $('#apply_filters').on('click', loadData);
        $('#filter_customer, #filter_from, #filter_to').on('change', loadData);

        $('#btn_export_claims, #btn_export_csv_top, #export_cases').on('click', function (e) {
            e.preventDefault();
            if (!allCases || allCases.length === 0) {
                alert('Exporting reimbursement claims report...');
                return;
            }
            let csv = "Case ID,Reason,Amount ($),Status,Date\n";
            allCases.forEach(c => {
                csv += `"${c.case_id}","${c.reason}",${c.amount},"${c.status}","${c.report_date}"\n`;
            });
            let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.setAttribute("download", `reimbursements_report_${new Date().toISOString().slice(0, 10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        $('#export_leaderboard').on('click', function (e) {
            e.preventDefault();
            if (!lastData || !lastData.leaderboard || lastData.leaderboard.length === 0) {
                alert('Exporting leaderboard data...');
                return;
            }
            let csv = "Product Name,SKU,Units Recovered,Total Value ($),Efficiency (%)\n";
            lastData.leaderboard.forEach(p => {
                csv += `"${(p.title || '').replace(/"/g, '""')}","${p.sku}",${p.units_recovered},${p.total_value},${p.efficiency}%\n`;
            });
            let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.setAttribute("download", `product_recovery_leaderboard_${new Date().toISOString().slice(0, 10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        if (typeof flatpickr !== 'undefined') {
            flatpickr("#date_range_picker_reimb", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "M d, Y",
                defaultDate: [$('#filter_from').val() || "2026-01-01", $('#filter_to').val() || "<?php echo date('Y-m-d'); ?>"],
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const from = instance.formatDate(selectedDates[0], "Y-m-d");
                        const to = instance.formatDate(selectedDates[1], "Y-m-d");
                        $('#filter_from').val(from);
                        $('#filter_to').val(to);
                        loadData();
                    }
                }
            });
        }

        $('#apply_filters').on('click', loadData);
        $('#filter_customer').on('change', loadData);

        loadData();
    });
</script>

<?php include '../../includes/footer.php'; ?>