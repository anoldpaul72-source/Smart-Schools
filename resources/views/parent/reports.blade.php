@extends('layouts.app')

@section('title', __('STUDENT PROGRESS REPORT') . ' | Smart-Results')

@section('no_global_header', true)

@section('styles')
<style>
    body {
        background-color: #f1f5f9 !important;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .parent-portal-wrapper {
        max-width: 1040px;
        margin: 20px auto 40px auto;
        padding: 0 15px;
    }

    /* Top Bar Card */
    .parent-top-card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        padding: 14px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .parent-welcome-text {
        font-size: 14px;
        color: #475569;
    }

    .parent-welcome-text strong {
        color: #0f172a;
        font-weight: 800;
        letter-spacing: 0.3px;
    }

    .top-actions-group {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    /* Language Switcher */
    .parent-lang-toggle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .parent-lang-toggle a {
        text-decoration: none;
        color: #64748b;
        transition: color 0.15s;
    }

    .parent-lang-toggle a.active-lang {
        color: #0284c7;
        font-weight: 800;
    }

    .parent-lang-toggle a:hover {
        color: #0284c7;
    }

    /* Download PDF Button */
    .btn-download-report {
        background-color: #0284c7;
        color: #ffffff !important;
        border: none;
        border-radius: 6px;
        padding: 9px 18px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s, transform 0.1s;
        box-shadow: 0 1px 2px rgba(2, 132, 199, 0.2);
    }

    .btn-download-report:hover {
        background-color: #0369a1;
        transform: translateY(-1px);
    }

    /* SMS Report Button */
    .btn-sms-report {
        background-color: #059669;
        color: #ffffff !important;
        border: none;
        border-radius: 6px;
        padding: 9px 18px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s, transform 0.1s;
        box-shadow: 0 1px 2px rgba(5, 150, 105, 0.2);
    }

    .btn-sms-report:hover {
        background-color: #047857;
        transform: translateY(-1px);
    }

    /* Parent SMS Modal */
    .parent-sms-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    .parent-sms-modal-card {
        background: #ffffff;
        width: 92%;
        max-width: 480px;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
    }

    /* Logout Link */
    .btn-parent-logout {
        background: none;
        border: none;
        color: #dc2626;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        padding: 4px;
        transition: opacity 0.2s;
    }

    .btn-parent-logout:hover {
        text-decoration: underline;
        opacity: 0.85;
    }

    /* Change Password Link */
    .btn-parent-password {
        background-color: #ffffff;
        color: #334155;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-parent-password:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #0284c7;
    }

    /* Filter Card */
    .parent-filter-card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        padding: 14px 24px;
        margin-top: 16px;
    }

    .filter-inner-form {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .filter-label {
        font-size: 14px;
        font-weight: 700;
        color: #334155;
    }

    .filter-select {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 8px 36px 8px 12px;
        font-size: 14px;
        font-family: inherit;
        color: #1e293b;
        background-color: #ffffff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 16px;
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
        outline: none;
        min-width: 200px;
    }

    .filter-select:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
    }

    .btn-view-report {
        background-color: #1e293b;
        color: #ffffff !important;
        border: none;
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-view-report:hover {
        background-color: #0f172a;
    }

    /* Main Big Title */
    .report-main-title {
        font-size: 28px;
        font-weight: 900;
        color: #1e293b;
        text-align: center;
        margin: 28px 0 22px 0;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* Main Student Report Card */
    .student-main-report-card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        border: 1px solid #e2e8f0;
        padding: 30px 35px;
        margin-bottom: 30px;
    }

    .student-name-title {
        font-size: 24px;
        font-weight: 900;
        color: #0284c7;
        margin: 0 0 8px 0;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .student-meta-details {
        font-size: 14px;
        color: #475569;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .student-meta-details .highlight {
        color: #0284c7;
        font-weight: 800;
    }

    .meta-divider {
        color: #94a3b8;
        font-weight: 300;
    }

    .report-solid-divider {
        height: 3px;
        background-color: #0284c7;
        width: 100%;
        margin-top: 14px;
        margin-bottom: 24px;
        border-radius: 2px;
    }

    /* No Marks Notice */
    .no-marks-state {
        text-align: center;
        color: #94a3b8;
        font-style: italic;
        font-size: 14.5px;
        padding: 30px 0 25px 0;
    }

    /* Academic Marks Table */
    .marks-results-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0 25px 0;
    }

    .marks-results-table th {
        background-color: #f8fafc;
        color: #334155;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 11px 14px;
        border-bottom: 2px solid #e2e8f0;
        text-align: left;
    }

    .marks-results-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #1e293b;
    }

    .marks-results-table tbody tr:hover {
        background-color: #fafbfc;
    }

    .grade-badge {
        display: inline-block;
        font-weight: 800;
        font-size: 13px;
        padding: 2px 10px;
        border-radius: 4px;
        text-align: center;
    }

    .grade-a { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .grade-b { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .grade-c { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
    .grade-d { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
    .grade-e { background: #fed7aa; color: #c2410c; border: 1px solid #fdba74; }
    .grade-s { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
    .grade-f { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

    .level-indicator-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 20px;
    }
    .level-badge-a {
        background: #faf5ff;
        color: #7e22ce;
        border: 1.5px solid #d8b4fe;
    }
    .level-badge-o {
        background: #f0fdf4;
        color: #15803d;
        border: 1.5px solid #86efac;
    }

    /* ATTENDANCE RECORD PER SOMO (USER REQUEST) */
    .attendance-per-subject-box {
        margin-top: 30px;
        border-top: 1px solid #e2e8f0;
        padding-top: 24px;
    }

    .attendance-box-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 16px;
    }

    .attendance-header-info h3 {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    .attendance-header-info p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    .overall-rate-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 6px 14px;
        border-radius: 8px;
    }

    .overall-rate-badge .rate-txt {
        font-size: 18px;
        font-weight: 900;
        color: #16a34a;
    }

    .overall-rate-badge .sessions-txt {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }

    .subject-att-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 13.5px;
    }

    .subject-att-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 10px 12px;
        border-bottom: 1px solid #e2e8f0;
        border-top: 1px solid #e2e8f0;
    }

    .subject-att-table td {
        padding: 11px 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .subject-att-table tr:hover td {
        background: #fcfdfe;
    }

    .att-count-present {
        display: inline-block;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12.5px;
    }

    .att-count-absent {
        display: inline-block;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12.5px;
    }

    .absent-has {
        background: #fee2e2;
        color: #dc2626;
    }

    .absent-none {
        background: #f1f5f9;
        color: #94a3b8;
    }

    .progress-bar-container {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 140px;
    }

    .bar-bg {
        flex: 1;
        height: 7px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.4s ease;
    }

    .fill-good { background: #16a34a; }
    .fill-mid  { background: #eab308; }
    .fill-low  { background: #ef4444; }

    .rate-percent-num {
        font-size: 12.5px;
        font-weight: 800;
        color: #334155;
        min-width: 38px;
        text-align: right;
    }

    .status-badge-chip {
        font-size: 11.5px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        display: inline-block;
        text-align: center;
        white-space: nowrap;
    }

    .chip-green  { background: #dcfce7; color: #15803d; }
    .chip-blue   { background: #e0f2fe; color: #0369a1; }
    .chip-yellow { background: #fef9c3; color: #854d0e; }
    .chip-red    { background: #fee2e2; color: #b91c1c; }

    /* DAILY PERIOD-BY-PERIOD ATTENDANCE STYLES */
    .daily-period-box {
        margin-top: 30px;
        border-top: 2px solid #e2e8f0;
        padding-top: 24px;
        background: #ffffff;
    }

    .daily-period-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .daily-period-title h3 {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 4px 0;
    }

    .daily-period-title p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    .daily-date-picker-form {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .daily-date-picker-form input[type="date"] {
        border: none;
        background: transparent;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
    }

    .btn-pick-date {
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-pick-date:hover {
        background: #1d4ed8;
    }

    .week-pills-container {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .week-pill {
        flex: 1;
        min-width: 90px;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 8px 10px;
        border-radius: 8px;
        text-decoration: none;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        transition: all 0.2s;
    }

    .week-pill:hover {
        border-color: #2563eb;
        background: #eff6ff;
    }

    .week-pill.active-pill {
        background: #2563eb;
        border-color: #2563eb;
        color: white;
        box-shadow: 0 3px 8px rgba(37, 99, 235, 0.25);
    }

    .week-pill.active-pill .pill-day,
    .week-pill.active-pill .pill-date,
    .week-pill.active-pill .pill-status {
        color: white !important;
    }

    .pill-day {
        font-size: 12px;
        font-weight: 800;
        color: #334155;
        text-transform: uppercase;
    }

    .pill-date {
        font-size: 11px;
        color: #64748b;
        margin-top: 2px;
    }

    .pill-status {
        font-size: 10px;
        font-weight: 700;
        margin-top: 4px;
        padding: 1px 6px;
        border-radius: 10px;
    }

    .pill-status-present { background: #dcfce7; color: #15803d; }
    .pill-status-absent  { background: #fee2e2; color: #b91c1c; }
    .pill-status-normal  { background: #f1f5f9; color: #64748b; }

    .daily-stats-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin-bottom: 22px;
    }

    .stat-chip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 14px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .stat-chip-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .stat-chip-value {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .periods-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 14px;
    }

    .period-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px;
        background: #ffffff;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 10px;
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .period-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    .period-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 8px;
    }

    .period-num-badge {
        font-size: 12px;
        font-weight: 800;
        color: #0369a1;
        background: #e0f2fe;
        padding: 3px 8px;
        border-radius: 6px;
    }

    .period-time-badge {
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
    }

    .period-subject-title {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin: 2px 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .period-teacher-txt {
        font-size: 12px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .period-status-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 800;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        box-sizing: border-box;
    }

    .period-status-present {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }

    .period-status-absent {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    .period-status-permission {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .period-status-scheduled {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .period-status-weekend {
        background: #f8fafc;
        color: #94a3b8;
        border: 1px dashed #cbd5e1;
    }

    /* FINANCIAL STATUS BOX (EXACT MATCH TO SCREENSHOT) */
    .financial-status-card {
        border: 1.5px dashed #93c5fd;
        background: #f8fafc;
        border-radius: 8px;
        padding: 16px 22px;
        margin-top: 26px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .fin-left-details {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }

    .fin-title-heading {
        font-size: 13.5px;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
    }

    .badge-account-status {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.4px;
        padding: 4px 10px;
        border-radius: 4px;
        display: inline-block;
        text-transform: uppercase;
    }

    .status-owing-badge {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .status-cleared-badge {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .fin-right-columns {
        display: flex;
        align-items: center;
        gap: 28px;
        flex-wrap: wrap;
    }

    .fin-metric-column {
        text-align: right;
    }

    .metric-label-top {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 2px;
    }

    .metric-value-bottom {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .metric-val-paid {
        color: #16a34a;
    }

    .metric-val-due {
        color: #dc2626;
    }

    /* ========================================================================= */
    /* OFFICIAL EXECUTIVE A-LEVEL PROGRESS REPORT CARD STYLING (NECTA COMPLIANT) */
    /* ========================================================================= */
    .alevel-report-document {
        background: #ffffff;
        border: 2.5px solid #0f2e5a;
        outline: 3px solid #e0e7ff;
        outline-offset: 4px;
        border-radius: 12px;
        box-shadow: 0 12px 35px -5px rgba(15, 23, 42, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04);
        padding: 36px 42px;
        margin: 24px 0 35px 0;
        color: #0f172a;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        position: relative;
    }

    /* Watermark background seal */
    .alevel-report-document::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 480px;
        height: 480px;
        background: radial-gradient(circle, rgba(15, 46, 90, 0.025) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
        z-index: 0;
    }

    .alevel-document-inner {
        position: relative;
        z-index: 1;
    }

    /* Header & Letterhead */
    .alevel-header-container {
        text-align: center;
        border-bottom: 2px solid #0f2e5a;
        padding-bottom: 20px;
        margin-bottom: 24px;
    }

    .alevel-crest-wrapper {
        display: flex;
        justify-content: center;
        margin-bottom: 10px;
    }

    .alevel-gov-title {
        font-size: 15px;
        font-weight: 900;
        letter-spacing: 1.5px;
        color: #0b1f3a;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .alevel-ministry-title {
        font-size: 12.5px;
        font-weight: 800;
        letter-spacing: 1px;
        color: #1e3a8a;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .alevel-school-title {
        font-size: 22px;
        font-weight: 900;
        letter-spacing: 0.8px;
        color: #0f2e5a;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .alevel-school-contact-strip {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        font-size: 12px;
        color: #475569;
        margin-top: 6px;
    }

    .alevel-contact-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 2px 10px;
        border-radius: 20px;
        font-weight: 600;
    }

    /* Executive Banner Ribbon */
    .alevel-banner-box {
        background: linear-gradient(135deg, #0f2e5a 0%, #1e40af 100%);
        color: #ffffff;
        border-radius: 8px;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 16px;
        box-shadow: 0 4px 12px rgba(15, 46, 90, 0.15);
        border: 1.5px solid #3b82f6;
    }

    .alevel-report-heading {
        font-size: 14px;
        font-weight: 900;
        letter-spacing: 0.8px;
        color: #ffffff;
        text-transform: uppercase;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alevel-term-year-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .alevel-badge-term {
        background: #ffffff;
        color: #0f2e5a;
        font-weight: 800;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 5px;
        letter-spacing: 0.3px;
    }

    .alevel-badge-year {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        font-weight: 700;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 5px;
    }

    /* Sections */
    .alevel-section {
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .alevel-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .alevel-sec-header {
        font-size: 13.5px;
        font-weight: 900;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: #0f2e5a;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 8px;
        border-left: 3.5px solid #2563eb;
        padding-left: 8px;
    }

    /* Student Profile Identity Grid (Replaces plain bullet lists) */
    .alevel-profile-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 12px;
    }

    .alevel-profile-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 11px 14px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .alevel-profile-card-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .alevel-profile-card-value {
        font-size: 14.5px;
        font-weight: 800;
        color: #0f172a;
    }

    .alevel-comb-tag {
        display: inline-block;
        background: #e0e7ff;
        color: #3730a3;
        font-weight: 900;
        font-size: 14px;
        padding: 1px 9px;
        border-radius: 4px;
        border: 1px solid #c7d2fe;
    }

    .alevel-rank-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
        padding: 1px 8px;
        border-radius: 4px;
        font-weight: 800;
    }

    /* Academic Results Table */
    .alevel-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
        margin-top: 8px;
        border-radius: 6px;
        overflow: hidden;
    }

    .alevel-table th {
        background: #0f2e5a;
        color: #ffffff;
        border: 1px solid #1e3a8a;
        padding: 9px 10px;
        font-weight: 800;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .alevel-table td {
        border: 1px solid #cbd5e1;
        padding: 9px 10px;
        vertical-align: middle;
    }

    .alevel-table tbody tr:nth-child(even) {
        background-color: #f8fafc;
    }

    .alevel-table tbody tr:hover {
        background-color: #f1f5f9;
    }

    .badge-sub-type {
        font-size: 10.5px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: inline-block;
    }

    .sub-type-principal {
        background: #e0e7ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
    }

    .sub-type-subsidiary {
        background: #fae8ff;
        color: #86198f;
        border: 1px solid #f0abfc;
    }

    /* Executive KPI Dashboard Widgets (Replaces summary bullets) */
    .alevel-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 14px;
        margin-top: 8px;
    }

    .alevel-kpi-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        position: relative;
    }

    .alevel-kpi-title {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 6px;
    }

    .alevel-kpi-value {
        font-size: 22px;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
    }

    .alevel-kpi-sub {
        font-size: 11px;
        color: #64748b;
        margin-top: 5px;
    }

    .kpi-division {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .kpi-division .alevel-kpi-value {
        color: #1e40af;
    }

    .kpi-points {
        border-color: #6366f1;
        background: #eef2ff;
    }

    .kpi-points .alevel-kpi-value {
        color: #4338ca;
    }

    .kpi-average {
        border-color: #0ea5e9;
        background: #f0f9ff;
    }

    .kpi-average .alevel-kpi-value {
        color: #0369a1;
    }

    .kpi-rank {
        border-color: #f59e0b;
        background: #fffbeb;
    }

    .kpi-rank .alevel-kpi-value {
        color: #b45309;
    }

    /* Official Grading Scale Reference Bar */
    .alevel-grading-key-bar {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 9px 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        font-size: 11.5px;
        margin-top: 14px;
    }

    /* Behaviour & Conduct Matrix */
    .alevel-conduct-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 10px;
        margin-top: 8px;
    }

    .alevel-conduct-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 9px 14px;
        font-size: 13px;
    }

    .conduct-badge {
        font-weight: 800;
        padding: 2px 9px;
        border-radius: 4px;
        font-size: 12px;
    }

    .conduct-a {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }

    .conduct-b {
        background: #dbeafe;
        color: #1d4ed8;
        border: 1px solid #93c5fd;
    }

    .conduct-c {
        background: #fef9c3;
        color: #a16207;
        border: 1px solid #fde047;
    }

    /* Remarks & Stamp Section */
    .alevel-remarks-container {
        display: flex;
        justify-content: space-between;
        align-items: stretch;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .alevel-remarks-left {
        flex: 1;
        min-width: 320px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .alevel-remark-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
    }

    .alevel-remark-header {
        font-size: 12px;
        font-weight: 800;
        color: #0f2e5a;
        text-transform: uppercase;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .alevel-remark-body {
        font-size: 13px;
        color: #334155;
        line-height: 1.5;
        font-style: italic;
        margin-bottom: 8px;
    }

    .alevel-sign-date-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11.5px;
        color: #64748b;
        border-top: 1px dashed #cbd5e1;
        padding-top: 6px;
    }

    .alevel-stamp-box {
        width: 190px;
        min-height: 170px;
        border: 2px dashed #94a3b8;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 12px;
        background: #fafafa;
    }

    /* Next Term Directives & Payment Slip */
    .alevel-notice-box {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 14px 18px;
        margin-top: 8px;
    }

    .alevel-notice-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #e2e8f0;
        font-size: 13px;
    }

    .alevel-notice-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .alevel-date-badge {
        background: #e0e7ff;
        color: #3730a3;
        font-weight: 800;
        padding: 2px 10px;
        border-radius: 4px;
        border: 1px solid #c7d2fe;
    }

    .alevel-fee-badge {
        background: #dcfce7;
        color: #15803d;
        font-weight: 800;
        padding: 2px 10px;
        border-radius: 4px;
        border: 1px solid #86efac;
    }

    .alevel-control-number {
        font-family: monospace;
        font-weight: 900;
        font-size: 14px;
        letter-spacing: 1px;
        color: #b45309;
        background: #fefce8;
        padding: 2px 10px;
        border-radius: 4px;
        border: 1px solid #fde047;
    }

    /* Print Styles for PDF Generation */
    @media print {
        header, footer, .parent-top-card, .parent-filter-card, .btn-download-report, .parent-lang-toggle, .btn-parent-logout, .btn-parent-password, .report-main-title {
            display: none !important;
        }

        body {
            background: #ffffff !important;
            padding: 0 !important;
        }

        .parent-portal-wrapper {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .student-main-report-card, .alevel-report-document {
            border: 2px solid #000 !important;
            box-shadow: none !important;
            padding: 15px 20px !important;
            margin: 0 !important;
            page-break-after: avoid !important;
        }

        .alevel-table th, .marks-results-table th {
            background: #e2e8f0 !important;
            color: #000000 !important;
            border: 1px solid #000000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .alevel-table td, .marks-results-table td {
            border: 1px solid #000000 !important;
        }

        .financial-status-card {
            border: 1.5px dashed #999 !important;
            background: #fdfdfd !important;
        }
    }
</style>
@endsection


@section('content')
<div class="parent-portal-wrapper">

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">✅</span>
            <div style="flex: 1;">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">⚠️</span>
            <div style="flex: 1;">{{ session('error') }}</div>
        </div>
    @endif

    @if(!$selectedStudent)
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 40px; text-align: center; margin-top: 30px;">
            <div style="font-size: 44px; margin-bottom: 12px;">👪</div>
            <h3 style="font-size: 18px; font-weight: 800; color: #1e293b;">{{ __('No Students Linked to Your Account') }}</h3>
            <p style="color: #64748b; font-size: 14px; margin-top: 8px;">
                {{ __('Please contact your school administrator to link your parent account to your student profile.') }}
            </p>
        </div>
    @else
        <!-- TOP BAR (CARD 1) -->
        <div class="parent-top-card">
            <div class="parent-welcome-text">
                {{ __('Logged in as Parent:') }} <strong>{{ strtoupper(Auth::user()->name) }}</strong>
            </div>

            <div class="top-actions-group">
                <!-- Language Switcher -->
                <div class="parent-lang-toggle" title="{{ __('Choose Language') }}">
                    <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'active-lang' : '' }}">🇬🇧 EN</a>
                    <span style="color: #cbd5e1;">|</span>
                    <a href="{{ route('lang.switch', 'sw') }}" class="{{ app()->getLocale() == 'sw' ? 'active-lang' : '' }}">🇹🇿 SW</a>
                </div>

                <!-- Receive Report via SMS -->
                <button type="button" class="btn-sms-report" onclick="openParentSmsModal()" title="{{ __('Receive Report via SMS') }}">
                    <span>📱</span>
                    <span>{{ __('Receive Report via SMS') }}</span>
                </button>

                <!-- Download Report (PDF) -->
                <button type="button" class="btn-download-report" onclick="window.print()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    {{ __('Download Report (PDF)') }}
                </button>

                <!-- Change Password -->
                <a href="{{ route('password.change') }}" class="btn-parent-password" title="{{ __('Change Password') }}">
                    <span>🔒</span>
                    <span>{{ __('Change Password') }}</span>
                </a>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                    @csrf
                    <button type="submit" class="btn-parent-logout">{{ __('Logout') }}</button>
                </form>
            </div>
        </div>

        <!-- FILTER BAR (CARD 2) -->
        <div class="parent-filter-card">
            @if($children->count() > 1)
                <!-- Quick Child Switcher Pills -->
                <div style="margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span style="font-size: 13px; font-weight: 800; color: #64748b; text-transform: uppercase;">{{ __('Your Children:') }}</span>
                    @foreach($children as $child)
                        <a href="{{ route('parent.reports', ['student_id' => $child->id, 'report_type' => $selectedReportType]) }}"
                           style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 20px; text-decoration: none; font-weight: 700; font-size: 13px; border: 1.5px solid {{ $selectedStudent->id == $child->id ? '#0284c7' : '#cbd5e1' }}; background: {{ $selectedStudent->id == $child->id ? '#f0f9ff' : '#ffffff' }}; color: {{ $selectedStudent->id == $child->id ? '#0284c7' : '#475569' }};">
                            <span>👤 {{ $child->student_name }}</span>
                            <span style="background: {{ $selectedStudent->id == $child->id ? '#0284c7' : '#e2e8f0' }}; color: {{ $selectedStudent->id == $child->id ? '#ffffff' : '#334155' }}; font-size: 11px; padding: 2px 8px; border-radius: 12px; font-weight: 800;">{{ $child->class_name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

            <form method="GET" action="{{ route('parent.reports') }}" class="filter-inner-form">
                @if($availableClasses->count() > 1)
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="filter-label">{{ __('Select Class:') }}</span>
                        <select name="class_name" class="filter-select" onchange="this.form.submit()">
                            @foreach($availableClasses as $cls)
                                <option value="{{ $cls }}" {{ $selectedClass == $cls ? 'selected' : '' }}>
                                    🏫 {{ $cls }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($children->count() > 1)
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="filter-label">{{ __('Select Student:') }}</span>
                        <select name="student_id" class="filter-select" onchange="this.form.submit()">
                            @foreach($children as $child)
                                <option value="{{ $child->id }}" {{ $selectedStudent->id == $child->id ? 'selected' : '' }}>
                                    {{ $child->student_name }} ({{ $child->class_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                @endif

                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="filter-label">{{ __('Select Report Type:') }}</span>
                    <select name="report_type" class="filter-select" onchange="this.form.submit()">
                        @foreach(['Annual Examination', 'Terminal Examination', 'Midterm Test', 'Monthly Test', 'Weekly Test', 'Joint / Pre-Mock', 'Regional Mock', 'Pre-Necta', 'NECTA'] as $type)
                            <option value="{{ $type }}" {{ $selectedReportType == $type ? 'selected' : '' }}>
                                {{ __($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn-view-report">{{ __('View Report') }}</button>
            </form>
        </div>

        @if($isALevel)
            <!-- ========================================================================= -->
            <!-- RASMI: KADI YA MWANAFUNZI BINAFSI YA MAENDELEO YA TAALUMA NA TABIA (A-LEVEL) -->
            <!-- ========================================================================= -->
            <div class="alevel-report-document">
                <div class="alevel-document-inner">
                    <!-- HEADER YA SERIKALI NA SHULE -->
                    <div class="alevel-header-container">
                        <div class="alevel-crest-wrapper">
                            <!-- Tanzania Coat of Arms Style National Emblem -->
                            <svg width="78" height="78" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="47" stroke="#0f2e5a" stroke-width="2.5" fill="#f8fafc"/>
                                <circle cx="50" cy="50" r="43" stroke="#d97706" stroke-width="1.2" stroke-dasharray="3 2" fill="none"/>
                                <path d="M50 12 L55 26 L70 26 L58 35 L62 49 L50 40 L38 49 L42 35 L30 26 L45 26 Z" fill="#b45309"/>
                                <path d="M20 50 Q50 32 80 50 Q50 68 20 50 Z" fill="#15803d"/>
                                <rect x="33" y="52" width="34" height="26" rx="4" fill="#0f2e5a"/>
                                <path d="M38 64 L50 56 L62 64 L50 72 Z" fill="#facc15"/>
                                <path d="M18 78 C35 89 65 89 82 78" stroke="#0b1f3a" stroke-width="3" fill="none"/>
                            </svg>
                        </div>

                        <div class="alevel-gov-title">{{ __('JAMHURI YA MUUNGANO WA TANZANIA') }}</div>
                        <div class="alevel-ministry-title">
                            {{ __('OFISI YA RAIS - TAWALA ZA MIKOA NA SERIKALI ZA MITAA (TAMISEMI)') }}<br>
                            {{ __('WIZARA YA ELIMU, SAYANSI NA TEKNOLOJIA') }}
                        </div>
                        <div class="alevel-school-title">{{ strtoupper($schoolName) }}</div>
                        
                        <div class="alevel-school-contact-strip">
                            <span class="alevel-contact-pill">📍 {{ __('S.L.P.') }} {{ $schoolAddress }}</span>
                            <span class="alevel-contact-pill">📞 {{ __('Simu:') }} {{ $schoolPhone }}</span>
                            <span class="alevel-contact-pill">✉️ {{ __('Barua Pepe:') }} {{ $schoolEmail }}</span>
                        </div>

                        <!-- Regal Banner Ribbon -->
                        <div class="alevel-banner-box">
                            <h2 class="alevel-report-heading">
                                <span>📜</span>
                                <span>{{ __('KADI YA MATOKEO YA MAENDELEO YA TAALUMA NA TABIA (A-LEVEL)') }}</span>
                            </h2>
                            <div class="alevel-term-year-group">
                                <span class="alevel-badge-term">📅 {{ __('Muhula:') }} <strong>{{ $currentTerm }}</strong></span>
                                <span class="alevel-badge-year">🎓 {{ __('Mwaka:') }} <strong>{{ $academicYear }}</strong></span>
                                <span class="alevel-badge-year" style="background: rgba(245, 158, 11, 0.25); border-color: #f59e0b; color: #fef3c7;">ACSEE</span>
                            </div>
                        </div>
                    </div>

                    <!-- 1. TAARIFA BINAFSI ZA MWANAFUNZI -->
                    <div class="alevel-section">
                        <h3 class="alevel-sec-header">
                            <span>1.</span> {{ __('TAARIFA ZA MWANAFUNZI NA USAJILI (STUDENT PROFILE)') }}
                        </h3>
                        <div class="alevel-profile-grid">
                            <!-- Card 1: Name -->
                            <div class="alevel-profile-card">
                                <div class="alevel-profile-card-label">👤 {{ __('Jina Kamili la Mwanafunzi') }}</div>
                                <div class="alevel-profile-card-value" style="color: #0f2e5a; font-size: 15px;">
                                    {{ strtoupper($selectedStudent->student_name) }}
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                                    {{ __('Jinsia:') }} <strong>{{ $selectedStudent->gender ?? $selectedStudent->sex ?? 'M' }}</strong> &bull; {{ __('Hali:') }} <span style="color: #16a34a; font-weight: 700;">{{ __('Amesajiliwa') }}</span>
                                </div>
                            </div>

                            <!-- Card 2: Reg Number -->
                            <div class="alevel-profile-card">
                                <div class="alevel-profile-card-label">🆔 {{ __('Namba ya Mtihani / Usajili') }}</div>
                                <div class="alevel-profile-card-value" style="font-family: monospace; font-size: 15px; color: #1e40af;">
                                    {{ $selectedStudent->reg_number ?: ('S.0123/00' . $selectedStudent->id) }}
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                                    {{ __('Kituo cha Mtihani:') }} <strong>{{ $schoolName }}</strong>
                                </div>
                            </div>

                            <!-- Card 3: Class & Combination -->
                            <div class="alevel-profile-card">
                                <div class="alevel-profile-card-label">🎓 {{ __('Darasa na Mchepuo (Combination)') }}</div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-top: 2px;">
                                    <span class="alevel-comb-tag" title="{{ \App\Models\Student::COMBINATIONS[$combination] ?? '' }}">
                                        {{ $combination }}
                                    </span>
                                    <span style="font-weight: 800; font-size: 13.5px; color: #1e293b;">
                                        {{ $isForm5 ? __('Kidato cha V (Form 5)') : __('Kidato cha VI (Form 6)') }}
                                    </span>
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 3px; font-style: italic;">
                                    {{ \App\Models\Student::COMBINATIONS[$combination] ?? __('Advance Stream') }}
                                </div>
                            </div>

                            <!-- Card 4: Class Standing -->
                            <div class="alevel-profile-card" style="background: #fffbeb; border-color: #fde68a;">
                                <div class="alevel-profile-card-label" style="color: #92400e;">🏆 {{ __('Nafasi Darasani (Class Rank)') }}</div>
                                <div class="alevel-profile-card-value" style="color: #b45309; font-size: 15px;">
                                    <span class="alevel-rank-tag">
                                        🥇 {{ __('Nafasi ya') }} <strong>{{ $studentRank }}</strong>
                                    </span>
                                    <span style="font-size: 12.5px; color: #78350f; font-weight: 700; margin-left: 4px;">
                                        {{ __('kati ya wanafunzi') }} {{ $totalStudentsInClass }}
                                    </span>
                                </div>
                                <div style="font-size: 11px; color: #92400e; margin-top: 3px;">
                                    {{ __('Tathmini ya darasa nzima') }} ({{ $combination }})
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. MATOKEO YA MITIHANI NA ALAMA -->
                    <div class="alevel-section">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 8px;">
                            <h3 class="alevel-sec-header" style="margin-bottom: 0;">
                                <span>2.</span> {{ __('MATOKEO YA MITIHANI NA ALAMA (ACADEMIC RESULTS)') }}
                            </h3>
                            <span style="font-size: 11.5px; color: #64748b;">
                                {{ __('Mtihani:') }} <strong>{{ $currentTerm }}</strong> &bull; {{ __('Mfumo Rasmi wa NECTA (ACSEE)') }}
                            </span>
                        </div>

                        <div style="overflow-x: auto; border: 1px solid #cbd5e1; border-radius: 8px;">
                            <table class="alevel-table">
                                <thead>
                                    <tr>
                                        <th style="width: 38px; text-align: center;">Na.</th>
                                        <th>{{ __('Somo (Subject)') }}</th>
                                        <th style="text-align: center; width: 160px;">{{ __('Aina ya Somo') }}</th>
                                        <th style="text-align: center; width: 85px;">{{ __('Alama (%)') }}</th>
                                        <th style="text-align: center; width: 90px;">{{ __('Daraja') }}</th>
                                        <th style="text-align: center; width: 110px;">{{ __('Pointi') }}</th>
                                        <th>{{ __('Maoni ya Mwalimu wa Somo') }}</th>
                                        <th style="text-align: center; width: 95px;">{{ __('Sahihi') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($aLevelSubjects as $subRow)
                                        @php
                                            $isPrincipal = $subRow['type'] !== 'Subsidiary';
                                            $grd = strtoupper(trim($subRow['grade']));
                                            $gradeColors = [
                                                'A' => ['bg' => '#dcfce7', 'text' => '#15803d', 'border' => '#86efac'],
                                                'B' => ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'border' => '#93c5fd'],
                                                'C' => ['bg' => '#fef9c3', 'text' => '#a16207', 'border' => '#fde047'],
                                                'D' => ['bg' => '#ffedd5', 'text' => '#c2410c', 'border' => '#fdba74'],
                                                'E' => ['bg' => '#fed7aa', 'text' => '#9a3412', 'border' => '#fb923c'],
                                                'S' => ['bg' => '#f3e8ff', 'text' => '#6b21a8', 'border' => '#d8b4fe'],
                                                'F' => ['bg' => '#fee2e2', 'text' => '#b91c1c', 'border' => '#fca5a5'],
                                            ];
                                            $badgeStyle = $gradeColors[$grd] ?? ['bg' => '#f1f5f9', 'text' => '#334155', 'border' => '#cbd5e1'];
                                        @endphp
                                        <tr>
                                            <td style="text-align: center; font-weight: 800; color: #64748b;">{{ $subRow['number'] }}</td>
                                            <td>
                                                <div style="font-weight: 800; font-size: 13.5px; color: #0f2e5a;">{{ $subRow['name'] }}</div>
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="badge-sub-type {{ $isPrincipal ? 'sub-type-principal' : 'sub-type-subsidiary' }}">
                                                    {{ $isPrincipal ? '⭐ Principal Subject' : '🔹 Subsidiary' }}
                                                </span>
                                            </td>
                                            <td style="text-align: center; font-weight: 900; font-size: 14.5px; color: #0f172a;">
                                                {{ $subRow['marks'] !== '—' && $subRow['marks'] !== 'N/A' ? $subRow['marks'] . '%' : '—' }}
                                            </td>
                                            <td style="text-align: center;">
                                                @if(isset($gradeColors[$grd]))
                                                    <span style="display: inline-block; min-width: 32px; padding: 2px 8px; border-radius: 4px; font-weight: 900; font-size: 13px; background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['text'] }}; border: 1.5px solid {{ $badgeStyle['border'] }};">
                                                        {{ $grd }}
                                                    </span>
                                                @else
                                                    <span style="font-weight: 700; color: #64748b;">{{ $subRow['grade'] }}</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                @if($isPrincipal && $subRow['points'] !== '—' && is_numeric($subRow['points']))
                                                    <span style="display: inline-block; padding: 2px 8px; background: #e0e7ff; color: #3730a3; border-radius: 4px; font-weight: 800; border: 1px solid #c7d2fe; font-size: 12px;">
                                                        {{ $subRow['points'] }} {{ __('pt') }}
                                                    </span>
                                                @elseif(!$isPrincipal && in_array($grd, ['A','B','C','D','E','S']))
                                                    <span style="display: inline-block; padding: 2px 7px; background: #fdf4ff; color: #86198f; border-radius: 4px; font-weight: 700; border: 1px solid #f5d0fe; font-size: 11px;">
                                                        {{ __('Pass (Sub)') }}
                                                    </span>
                                                @else
                                                    <span style="color: #94a3b8;">—</span>
                                                @endif
                                            </td>
                                            <td style="font-size: 12px; color: #334155; line-height: 1.4;">
                                                {{ $subRow['remarks'] }}
                                            </td>
                                            <td style="text-align: center; font-style: italic; font-size: 12px; color: #475569;">
                                                {{ $subRow['signature'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- NECTA ACSEE Grading Scale Legend Bar -->
                        <div class="alevel-grading-key-bar">
                            <span style="font-weight: 800; color: #0f2e5a; text-transform: uppercase;">📊 {{ __('NECTA ACSEE Scale:') }}</span>
                            <span style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; border-radius: 3px; padding: 1px 6px; font-weight: 700;">A: 80–100 (1 pt)</span>
                            <span style="background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; border-radius: 3px; padding: 1px 6px; font-weight: 700;">B: 70–79 (2 pts)</span>
                            <span style="background: #fef9c3; color: #a16207; border: 1px solid #fde047; border-radius: 3px; padding: 1px 6px; font-weight: 700;">C: 60–69 (3 pts)</span>
                            <span style="background: #ffedd5; color: #c2410c; border: 1px solid #fdba74; border-radius: 3px; padding: 1px 6px; font-weight: 700;">D: 50–59 (4 pts)</span>
                            <span style="background: #fed7aa; color: #9a3412; border: 1px solid #fb923c; border-radius: 3px; padding: 1px 6px; font-weight: 700;">E: 40–49 (5 pts)</span>
                            <span style="background: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe; border-radius: 3px; padding: 1px 6px; font-weight: 700;">S: 35–39 (6 pts)</span>
                            <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 3px; padding: 1px 6px; font-weight: 700;">F: 0–34 (7 pts)</span>
                        </div>
                    </div>

                    <!-- 3. MUHTASARI WA UFAULU (EXECUTIVE PERFORMANCE DASHBOARD) -->
                    <div class="alevel-section">
                        <h3 class="alevel-sec-header">
                            <span>3.</span> {{ __('MUHTASARI WA MATOKEO NA UFAULU (PERFORMANCE SUMMARY)') }}
                        </h3>
                        <div class="alevel-kpi-grid">
                            <!-- KPI 1: Points -->
                            <div class="alevel-kpi-card kpi-points">
                                <div class="alevel-kpi-title">🎯 {{ __('Jumla ya Pointi') }}</div>
                                <div class="alevel-kpi-value">{{ $totalPrincipalPoints }} <small style="font-size: 13px; font-weight: 700;">{{ __('Pointi') }}</small></div>
                                <div class="alevel-kpi-sub">{{ __('Kutoka Masomo 3 ya Mchepuo') }}</div>
                            </div>

                            <!-- KPI 2: Division -->
                            <div class="alevel-kpi-card kpi-division">
                                <div class="alevel-kpi-title">🏆 {{ __('Daraja la Ufaulu') }}</div>
                                <div class="alevel-kpi-value" style="display: flex; align-items: center; gap: 8px;">
                                    <span>{{ $division }}</span>
                                    @if(in_array($division, ['Division I', 'I']))
                                        <span style="font-size: 18px;">🌟</span>
                                    @endif
                                </div>
                                <div class="alevel-kpi-sub">{{ __('Kiwango cha NECTA ACSEE') }}</div>
                            </div>

                            <!-- KPI 3: GPA / Average -->
                            <div class="alevel-kpi-card kpi-average">
                                <div class="alevel-kpi-title">📈 {{ __('Wastani wa Alama (Average)') }}</div>
                                <div class="alevel-kpi-value">{{ $overallAverage }}%</div>
                                <div class="alevel-kpi-sub">{{ __('Wastani wa masomo yote') }}</div>
                            </div>

                            <!-- KPI 4: Class Standing -->
                            <div class="alevel-kpi-card kpi-rank">
                                <div class="alevel-kpi-title">🥇 {{ __('Nafasi Darasani (Rank)') }}</div>
                                <div class="alevel-kpi-value">{{ $studentRank }} <small style="font-size: 14px; font-weight: 700; color: #78350f;">/ {{ $totalStudentsInClass }}</small></div>
                                <div class="alevel-kpi-sub">{{ __('Katika Mchepuo wa') }} {{ $combination }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. TATHMINI YA TABIA NA NIDHAMU (BEHAVIOUR & CONDUCT) -->
                    <div class="alevel-section">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; gap: 8px; margin-bottom: 8px;">
                            <h3 class="alevel-sec-header" style="margin-bottom: 0;">
                                <span>4.</span> {{ __('TATHMINI YA TABIA NA NIDHAMU (BEHAVIOUR & CONDUCT)') }}
                            </h3>
                            <span style="font-size: 11.5px; color: #64748b;">
                                <strong>A</strong> = {{ __('Bora Sana') }} &bull; <strong>B</strong> = {{ __('Nzuri') }} &bull; <strong>C</strong> = {{ __('Wastani') }} &bull; <strong>D</strong> = {{ __('Dhaifu') }}
                            </span>
                        </div>

                        <div class="alevel-conduct-grid">
                            <div class="alevel-conduct-item">
                                <span>⏰ <strong>1. {{ __('Uhudhuriaji na Kuwahi') }}</strong></span>
                                <span class="conduct-badge conduct-{{ strtolower($conductGrades['attendance'] ?? 'a') }}">{{ $conductGrades['attendance'] ?? 'A' }}</span>
                            </div>
                            <div class="alevel-conduct-item">
                                <span>⚡ <strong>2. {{ __('Kazi Binafsi na Jitihada') }}</strong></span>
                                <span class="conduct-badge conduct-{{ strtolower($conductGrades['effort'] ?? 'b') }}">{{ $conductGrades['effort'] ?? 'B' }}</span>
                            </div>
                            <div class="alevel-conduct-item">
                                <span>🛡️ <strong>3. {{ __('Utii wa Sheria za Shule') }}</strong></span>
                                <span class="conduct-badge conduct-{{ strtolower($conductGrades['obedience'] ?? 'a') }}">{{ $conductGrades['obedience'] ?? 'A' }}</span>
                            </div>
                            <div class="alevel-conduct-item">
                                <span>🤝 <strong>4. {{ __('Ushirikiano na Wenzake') }}</strong></span>
                                <span class="conduct-badge conduct-{{ strtolower($conductGrades['cooperation'] ?? 'a') }}">{{ $conductGrades['cooperation'] ?? 'A' }}</span>
                            </div>
                            <div class="alevel-conduct-item">
                                <span>✨ <strong>5. {{ __('Usafi Binafsi na Mazingira') }}</strong></span>
                                <span class="conduct-badge conduct-{{ strtolower($conductGrades['cleanliness'] ?? 'a') }}">{{ $conductGrades['cleanliness'] ?? 'A' }}</span>
                            </div>
                            <div class="alevel-conduct-item">
                                <span>❤️ <strong>6. {{ __('Malezi, Maadili na Nidhamu') }}</strong></span>
                                <span class="conduct-badge conduct-{{ strtolower($conductGrades['morals'] ?? 'a') }}">{{ $conductGrades['morals'] ?? 'A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 5. MAONI YA UONGOZI NA WALIMU & MUHURI RASMI -->
                    <div class="alevel-section">
                        <h3 class="alevel-sec-header">
                            <span>5.</span> {{ __('MAONI YA UONGOZI WA SHULE NA WALIMU (LEADERSHIP REMARKS)') }}
                        </h3>
                        <div class="alevel-remarks-container">
                            <div class="alevel-remarks-left">
                                <!-- Class Teacher Remarks -->
                                <div class="alevel-remark-card">
                                    <div class="alevel-remark-header">
                                        <span>✍️</span> {{ __('Maoni ya Mwalimu wa Darasa (Class Teacher):') }}
                                    </div>
                                    <div class="alevel-remark-body">
                                        "{{ $classTeacherRemarks }}"
                                    </div>
                                    <div class="alevel-sign-date-row">
                                        <span><strong>{{ __('Sahihi:') }}</strong> <span style="font-family: cursive; font-size: 15px; color: #0f2e5a; padding-left: 6px;">Mwl. wa Darasa</span></span>
                                        <span><strong>{{ __('Tarehe:') }}</strong> <span style="font-weight: 700; color: #0f172a;">{{ $reportDate }}</span></span>
                                    </div>
                                </div>

                                <!-- Head of School Remarks -->
                                <div class="alevel-remark-card">
                                    <div class="alevel-remark-header">
                                        <span>🏛️</span> {{ __('Maoni ya Mkuu wa Shule (Head of School):') }}
                                    </div>
                                    <div class="alevel-remark-body">
                                        "{{ $headOfSchoolRemarks }}"
                                    </div>
                                    <div class="alevel-sign-date-row">
                                        <span><strong>{{ __('Sahihi:') }}</strong> <span style="font-family: cursive; font-size: 15px; color: #0f2e5a; padding-left: 6px;">Mkuu wa Shule</span></span>
                                        <span><strong>{{ __('Tarehe:') }}</strong> <span style="font-weight: 700; color: #0f172a;">{{ $reportDate }}</span></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Official Circular School Seal Box -->
                            <div class="alevel-stamp-box">
                                <svg width="125" height="125" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Double outer concentric rings -->
                                    <circle cx="70" cy="70" r="66" stroke="#1e3a8a" stroke-width="2" stroke-dasharray="4 2"/>
                                    <circle cx="70" cy="70" r="62" stroke="#1e3a8a" stroke-width="1.5"/>
                                    <circle cx="70" cy="70" r="44" stroke="#1e3a8a" stroke-width="1.2"/>
                                    <!-- Circular text path -->
                                    <path id="sealTextPath" d="M 26,70 A 44,44 0 1,1 114,70 A 44,44 0 1,1 26,70" fill="none"/>
                                    <text font-size="8" font-weight="900" fill="#1e3a8a" letter-spacing="1.2">
                                        <textPath href="#sealTextPath" startOffset="50%" text-anchor="middle">
                                            {{ strtoupper($schoolName) }} &bull; TANZANIA &bull;
                                        </textPath>
                                    </text>
                                    <!-- Center Emblem -->
                                    <g transform="translate(48, 48)">
                                        <circle cx="22" cy="22" r="18" fill="#eff6ff" stroke="#3b82f6" stroke-width="1"/>
                                        <text x="22" y="19" font-size="7" font-weight="800" text-anchor="middle" fill="#1e3a8a">OFFICIAL</text>
                                        <text x="22" y="27" font-size="8" font-weight="900" text-anchor="middle" fill="#2563eb">SEAL</text>
                                        <path d="M 12,32 L 32,32" stroke="#1e3a8a" stroke-width="0.8"/>
                                    </g>
                                </svg>
                                <span style="font-size: 10.5px; font-weight: 800; color: #1e3a8a; margin-top: 6px; text-transform: uppercase;">
                                    {{ __('Muhuri Rasmi wa Shule') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 6. MAELEKEZO YA MUHULA UJAO NA MALIPO -->
                    <div class="alevel-section">
                        <h3 class="alevel-sec-header">
                            <span>6.</span> {{ __('MAELEKEZO YA MUHULA UJAO NA MALIPO (NEXT TERM DIRECTIVES)') }}
                        </h3>
                        <div class="alevel-notice-box">
                            <div class="alevel-notice-row">
                                <span>📅 <strong>{{ __('Tarehe ya Kufunga Shule:') }}</strong></span>
                                <span class="alevel-date-badge">{{ $closingDate }}</span>
                            </div>
                            <div class="alevel-notice-row">
                                <span>🏫 <strong>{{ __('Tarehe ya Kufungua Shule:') }}</strong></span>
                                <span class="alevel-date-badge" style="background: #ecfdf5; color: #065f46; border-color: #a7f3d0;">
                                    {{ $reopeningDate }}
                                </span>
                            </div>
                            <div class="alevel-notice-row">
                                <span>💳 <strong>{{ __('Ada na Michango ya Muhula Ujao:') }}</strong></span>
                                <span class="alevel-fee-badge">
                                    TZS {{ number_format($remainingBalance > 0 ? $remainingBalance : ($totalFees > 0 ? $totalFees : 70000), 2) }}
                                </span>
                            </div>
                            <div class="alevel-notice-row">
                                <span>🔢 <strong>{{ __('Kumbukumbu ya Malipo (Government Control Number):') }}</strong></span>
                                <span class="alevel-control-number">{{ $controlNumber }}</span>
                            </div>
                            <div class="alevel-notice-row" style="align-items: flex-start;">
                                <div style="font-size: 12.5px; color: #475569; line-height: 1.5; background: #ffffff; border: 1px solid #e2e8f0; border-left: 3.5px solid #f59e0b; padding: 10px 14px; border-radius: 6px; width: 100%;">
                                    💡 <strong>{{ __('Ujumbe Muhimu kwa Mzazi / Mlezi:') }}</strong><br>
                                    {{ __('Mzazi/Mlezi anahimizwa kufuatilia maendeleo ya mwanafunzi wakati wa likizo, kuhakikisha anafanya kazi zote alizopewa za likizo na kulipa ada/michango kwa wakati kupitia namba ya kumbukumbu (Control Number) kabla ya tarehe ya kufungua shule.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- CENTERED HEADING FOR O-LEVEL -->
            <h1 class="report-main-title">
                {{ __('O-LEVEL STUDENT PROGRESS REPORT') }}
            </h1>

            <!-- MAIN STUDENT REPORT (CARD 3) -->
            <div class="student-main-report-card">
                <!-- Student Title & Metadata -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 8px;">
                    <h2 class="student-name-title" style="margin: 0;">{{ strtoupper($selectedStudent->student_name) }}</h2>
                    <span class="level-indicator-badge level-badge-o">
                        📚 Ordinary Level (O-Level)
                    </span>
                </div>
                <div class="student-meta-details">
                    <span>{{ __('Class:') }} <span class="highlight">{{ $selectedStudent->class_name }}</span></span>
                    <span class="meta-divider">|</span>
                    <span>{{ __('Level:') }} <span class="highlight" style="color: #15803d; font-weight: 800;">O-Level (Form 1 - 4)</span></span>
                    <span class="meta-divider">|</span>
                    <span>{{ __('Assessment:') }} <span class="highlight">{{ $selectedReportType }}</span></span>
                    <span class="meta-divider">|</span>
                    <span>{{ __('Date Done:') }} <span class="highlight">{{ $dateDone }}</span></span>
                </div>

                <!-- Blue Solid Divider Line -->
                <div class="report-solid-divider"></div>

                <!-- Academic Marks Section -->
                @if($marks->isEmpty())
                    <div class="no-marks-state">
                        {{ __('No marks have been recorded for :assessment yet.', ['assessment' => $selectedReportType]) }}
                    </div>
                @else
                    <div style="overflow-x: auto; margin-bottom: 25px;">
                        <table class="marks-results-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>{{ __('Subject') }}</th>
                                    <th style="text-align: center;">{{ __('Marks (/100)') }}</th>
                                    <th style="text-align: center;">{{ __('Grade') }}</th>
                                    <th>{{ __('Remarks') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($marks as $idx => $m)
                                    @php
                                        [$calcGrade, $calcRemarks] = \App\Models\Mark::calculateGrade((float)$m->marks, false);
                                        $rowGrade = $calcGrade;
                                        $rowRemarks = $m->remarks ?: $calcRemarks;
                                    @endphp
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td><strong>{{ $m->subject ? $m->subject->subject_name : 'Subject' }}</strong></td>
                                        <td style="text-align: center; font-weight: 800; font-size: 15px;">{{ number_format($m->marks, 1) }}</td>
                                        <td style="text-align: center;">
                                            <span class="grade-badge grade-{{ strtolower($rowGrade) }}">{{ $rowGrade }}</span>
                                        </td>
                                        <td>{{ $rowRemarks }}</td>
                                        <td>{{ \Carbon\Carbon::parse($m->exam_date)->format('d M, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #f8fafc; font-weight: 800;">
                                    <td colspan="2" style="padding: 12px 14px;">{{ __('Average / Overall Grade') }}</td>
                                    <td style="text-align: center; color: #0284c7; font-size: 16px;">
                                        {{ $average ? number_format($average, 1) . '%' : 'N/A' }}
                                    </td>
                                    <td style="text-align: center; font-size: 16px;">
                                        <span class="grade-badge grade-{{ strtolower($overallGrade) }}">{{ $overallGrade }}</span>
                                    </td>
                                    <td colspan="2" style="font-weight: 600; color: #64748b;">
                                        @if($overallGrade == 'A') {{ __('Excellent Performance') }}
                                        @elseif($overallGrade == 'B') {{ __('Very Good Performance') }}
                                        @elseif($overallGrade == 'C') {{ __('Good Performance') }}
                                        @elseif($overallGrade == 'D') {{ __('Pass / Satisfactory') }}
                                        @elseif($overallGrade == 'F') {{ __('Fail / Needs Improvement') }}
                                        @else -
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <!-- Grading Scale Key -->
                        @if($isALevel ?? false)
                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-wrap: wrap; margin-top: 10px; font-size: 11px;">
                            <span style="font-weight: 800; color: #4338ca;">🎓 {{ __('A-Level Grading Scale (ACSEE):') }}</span>
                            <span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 2px 7px; border-radius: 4px; font-weight: 700;">A: 80–100</span>
                            <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 2px 7px; border-radius: 4px; font-weight: 700;">B: 70–79</span>
                            <span style="background: #fefce8; color: #a16207; border: 1px solid #fef08a; padding: 2px 7px; border-radius: 4px; font-weight: 700;">C: 60–69</span>
                            <span style="background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; padding: 2px 7px; border-radius: 4px; font-weight: 700;">D: 50–59</span>
                            <span style="background: #fed7aa; color: #9a3412; border: 1px solid #fb923c; padding: 2px 7px; border-radius: 4px; font-weight: 700;">E: 40–49</span>
                            <span style="background: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe; padding: 2px 7px; border-radius: 4px; font-weight: 700;">S: 35–39</span>
                            <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 2px 7px; border-radius: 4px; font-weight: 700;">F: 0–34</span>
                        </div>
                        @else
                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-wrap: wrap; margin-top: 10px; font-size: 11px;">
                            <span style="font-weight: 800; color: #475569;">📚 {{ __('O-Level Grading Scale (CSEE):') }}</span>
                            <span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 2px 7px; border-radius: 4px; font-weight: 700;">A: 75–100</span>
                            <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 2px 7px; border-radius: 4px; font-weight: 700;">B: 60–74</span>
                            <span style="background: #fefce8; color: #a16207; border: 1px solid #fef08a; padding: 2px 7px; border-radius: 4px; font-weight: 700;">C: 45–59</span>
                            <span style="background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; padding: 2px 7px; border-radius: 4px; font-weight: 700;">D: 30–44</span>
                            <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 2px 7px; border-radius: 4px; font-weight: 700;">F: 0–29</span>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif


            <!-- DAILY PERIOD-BY-PERIOD ATTENDANCE TRACKER (USER EXPLICIT REQUEST) -->
            <div class="daily-period-box">
                <div class="daily-period-header">
                    <div class="daily-period-title">
                        <h3>
                            <span>🕒</span> {{ __('Mahudhurio ya Kila Kipindi Kila Siku') }}
                        </h3>
                        <p>{{ __('Fuatilia uwepo wa mtoto wako darasani kwa kila kipindi katika siku husika') }}</p>
                    </div>

                    <!-- Date picker form -->
                    <form method="GET" action="{{ route('parent.reports') }}" class="daily-date-picker-form">
                        @if(request('class_name'))
                            <input type="hidden" name="class_name" value="{{ request('class_name') }}">
                        @endif
                        @if(request('student_id'))
                            <input type="hidden" name="student_id" value="{{ request('student_id') }}">
                        @endif
                        @if(request('report_type'))
                            <input type="hidden" name="report_type" value="{{ request('report_type') }}">
                        @endif
                        <span style="font-size: 12px; font-weight: 700; color: #475569;">📅 {{ __('Chagua Tarehe:') }}</span>
                        <input type="date" name="attendance_date" value="{{ $selectedAttendanceDate }}" required>
                        <button type="submit" class="btn-pick-date">{{ __('Angalia') }}</button>
                    </form>
                </div>

                <!-- Recent School Days Strip (Quick Day Switcher) -->
                @if(!empty($recentSchoolDays))
                    <div class="week-pills-container">
                        @foreach($recentSchoolDays as $day)
                            <a href="{{ route('parent.reports', array_merge(request()->query(), ['attendance_date' => $day['date']])) }}"
                               class="week-pill {{ $day['is_active'] ? 'active-pill' : '' }}">
                                <span class="pill-day">{{ __($day['day_name']) }}</span>
                                <span class="pill-date">{{ $day['day_number'] }}</span>
                                @if($day['is_today'])
                                    <span class="pill-status" style="background: #e0f2fe; color: #0369a1; font-weight: 800;">{{ __('Leo') }}</span>
                                @elseif($day['status'] === 'Absent')
                                    <span class="pill-status pill-status-absent">❌ {{ __('Alikosa') }}</span>
                                @elseif($day['status'] === 'Present')
                                    <span class="pill-status pill-status-present">✅ {{ __('Alikuwepo') }}</span>
                                @else
                                    <span class="pill-status pill-status-normal">{{ __('Kawaida') }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Daily Stats Banner -->
                <div class="daily-stats-strip">
                    <div class="stat-chip">
                        <span class="stat-chip-label">📅 {{ __('Siku na Tarehe') }}</span>
                        <span class="stat-chip-value" style="font-size: 14px; color: #0284c7;">
                            {{ __($dayOfWeek) }}, {{ date('d M Y', strtotime($selectedAttendanceDate)) }}
                        </span>
                    </div>

                    <div class="stat-chip">
                        <span class="stat-chip-label">🎯 {{ __('Kiwango cha Mahudhurio') }}</span>
                        <span class="stat-chip-value" style="color: {{ $dailyRate >= 80 ? '#16a34a' : ($dailyRate >= 50 ? '#eab308' : '#dc2626') }};">
                            {{ $dailyRate }}%
                        </span>
                    </div>

                    <div class="stat-chip">
                        <span class="stat-chip-label">✅ {{ __('Vipindi Alivyohudhuria') }}</span>
                        <span class="stat-chip-value" style="color: #16a34a;">
                            {{ $dailyPresentCount }} / {{ $dailyTotalCount }} {{ __('Vipindi') }}
                        </span>
                    </div>

                    <div class="stat-chip">
                        <span class="stat-chip-label">❌ {{ __('Vipindi Alivyokosa') }}</span>
                        <span class="stat-chip-value" style="color: {{ $dailyAbsentCount > 0 ? '#dc2626' : '#64748b' }};">
                            {{ $dailyAbsentCount }} {{ __('Vipindi') }}
                        </span>
                    </div>
                </div>

                <!-- Periods Timeline Grid -->
                <div class="periods-grid">
                    @forelse($dailyPeriods as $period)
                        <div class="period-card">
                            <div>
                                <div class="period-card-top">
                                    <span class="period-num-badge">
                                        {{ __('Kipindi') }} {{ $period['period_number'] }}
                                    </span>
                                    <span class="period-time-badge">
                                        ⏰ {{ $period['time_slot'] }}
                                    </span>
                                </div>

                                <div class="period-subject-title">
                                    📖 {{ $period['subject_name'] }}
                                </div>

                                <div class="period-teacher-txt">
                                    👨‍🏫 {{ $period['teacher_name'] }}
                                </div>

                                @if(!empty($period['recorder_name']))
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
                                        ✍️ {{ __('Imerekodiwa na') }}: {{ $period['recorder_name'] }}
                                        @if(!empty($period['recorded_at']))
                                            ({{ $period['recorded_at'] }})
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div>
                                @if($period['status'] === 'Present')
                                    <span class="period-status-badge period-status-present">
                                        ✅ {{ __('Alihudhuria (Present)') }}
                                    </span>
                                @elseif($period['status'] === 'Absent')
                                    <span class="period-status-badge period-status-absent">
                                        ❌ {{ __('Alikosa (Absent)') }}
                                    </span>
                                @elseif(in_array($period['status'], ['Permission', 'Late']))
                                    <span class="period-status-badge period-status-permission">
                                        ⚠️ {{ __('Ruhusa / Chelewa') }}
                                    </span>
                                @elseif($period['status'] === 'Weekend')
                                    <span class="period-status-badge period-status-weekend">
                                        🏖️ {{ __('Mapumziko ya Wikendi') }}
                                    </span>
                                @else
                                    <span class="period-status-badge period-status-scheduled">
                                        ⏳ {{ __('Imepangwa (Scheduled)') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1; text-align: center; padding: 25px; color: #94a3b8; background: #f8fafc; border-radius: 8px;">
                            {{ __('Hakuna vipindi vilivyopatikana kwa siku hii.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ATTENDANCE RECORD FOR EACH SUBJECT (USER EXPLICIT REQUEST) -->
            <div class="attendance-per-subject-box">
                <div class="attendance-box-top">
                    <div class="attendance-header-info">
                        <h3>
                            <span>📅</span> {{ __('Rekodi ya Mahudhurio kwa Kila Somo') }}
                        </h3>
                        <p>{{ __('Fuatilia mahudhurio ya mwanafunzi darasani kwa kila somo katika kipindi hiki') }}</p>
                    </div>

                    <div class="overall-rate-badge">
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: #15803d; text-transform: uppercase;">
                                {{ __('Jumla ya Mahudhurio:') }}
                            </div>
                            <div class="rate-txt">{{ $overallAttendanceRate }}%</div>
                        </div>
                        <div class="sessions-txt">
                            ({{ $totalAttendedSessions }}/{{ $totalPlannedSessions }} {{ __('Vipindi') }})
                        </div>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="subject-att-table">
                        <thead>
                            <tr>
                                <th style="width: 35px; text-align: center;">#</th>
                                <th>{{ __('Somo (Subject)') }}</th>
                                <th style="text-align: center;">{{ __('Vipindi Vilivyofundishwa') }}</th>
                                <th style="text-align: center;">{{ __('Alivyohudhuria (Present)') }}</th>
                                <th style="text-align: center;">{{ __('Alivyokosa (Absent)') }}</th>
                                <th>{{ __('Kiwango cha Mahudhurio') }}</th>
                                <th style="text-align: center;">{{ __('Hali (Status)') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjectAttendances as $index => $item)
                                <tr>
                                    <td style="text-align: center; color: #94a3b8; font-weight: 600;">{{ $index + 1 }}</td>
                                    <td>
                                        <div style="font-weight: 800; color: #1e293b;">
                                            {{ $item['subject_name'] }}
                                        </div>
                                    </td>
                                    <td style="text-align: center; font-weight: 700; color: #334155;">
                                        {{ $item['total'] }}
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="att-count-present">{{ $item['present'] }}</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="att-count-absent {{ $item['absent'] > 0 ? 'absent-has' : 'absent-none' }}">
                                            {{ $item['absent'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress-bar-container">
                                            <div class="bar-bg">
                                                <div class="bar-fill {{ $item['rate'] >= 85 ? 'fill-good' : ($item['rate'] >= 70 ? 'fill-mid' : 'fill-low') }}"
                                                     style="width: {{ $item['rate'] }}%;"></div>
                                            </div>
                                            <span class="rate-percent-num">{{ $item['rate'] }}%</span>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($item['rate'] >= 90)
                                            <span class="status-badge-chip chip-green">{{ __('Nzuri Sana') }}</span>
                                        @elseif($item['rate'] >= 75)
                                            <span class="status-badge-chip chip-blue">{{ __('Nzuri') }}</span>
                                        @elseif($item['rate'] >= 60)
                                            <span class="status-badge-chip chip-yellow">{{ __('Wastani') }}</span>
                                        @else
                                            <span class="status-badge-chip chip-red">{{ __('Inahitaji Uangalizi') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 25px; color: #94a3b8;">
                                        {{ __('Hakuna rekodi za mahudhurio zilizopatikana kwa mwanafunzi huyu.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- FINANCIAL STATUS BOX (EXACT MATCH TO SCREENSHOT) -->
            <div class="financial-status-card">
                <div class="fin-left-details">
                    <div class="fin-title-heading">
                        <span>🗂️</span> {{ __('FINANCIAL STATUS') }} ({{ __('ACADEMIC YEAR:') }} {{ $academicYear }})
                    </div>
                    <div class="badge-account-status {{ $isOwing ? 'status-owing-badge' : 'status-cleared-badge' }}">
                        {{ __('ACCOUNT STATUS:') }} {{ $isOwing ? __('OWING') : __('CLEARED') }}
                    </div>
                </div>

                <div class="fin-right-columns">
                    <div class="fin-metric-column">
                        <div class="metric-label-top">{{ __('TOTAL FEE REQUIRED') }}</div>
                        <div class="metric-value-bottom">{{ number_format($totalFees, 2) }} TZS</div>
                    </div>

                    <div class="fin-metric-column">
                        <div class="metric-label-top">{{ __('TOTAL PAID') }}</div>
                        <div class="metric-value-bottom metric-val-paid">{{ number_format($paidAmount, 2) }} TZS</div>
                    </div>

                    <div class="fin-metric-column">
                        <div class="metric-label-top">{{ __('BALANCE DUE') }}</div>
                        <div class="metric-value-bottom metric-val-due">{{ number_format($remainingBalance, 2) }} TZS</div>
                    </div>
                </div>
            </div>

        </div>
    @endif

</div>

<!-- MODAL YA MZAZI KUJITUMIA RIPOTI KWA SMS -->
@if($selectedStudent)
<div id="parentSmsModal" class="parent-sms-modal-backdrop">
    <div class="parent-sms-modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 26px;">📱</span>
                <div>
                    <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0f172a;">{{ __('Receive Report via SMS') }}</h3>
                    <p style="margin: 2px 0 0 0; font-size: 12px; color: #64748b;">{{ __('Direct text message to your phone') }}</p>
                </div>
            </div>
            <button type="button" onclick="closeParentSmsModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>
        </div>

        <form method="POST" action="{{ route('parent.reports.send_sms') }}" onsubmit="handleParentSmsSubmit()">
            @csrf
            <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
            <input type="hidden" name="report_type" value="{{ $selectedReportType }}">

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 16px;">
                <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">{{ __('Student:') }}</div>
                <div style="font-size: 15px; font-weight: 800; color: #0f172a;">{{ $selectedStudent->student_name }} ({{ $selectedStudent->class_name }})</div>
                <div style="font-size: 12px; color: #0284c7; font-weight: 700; margin-top: 4px;">{{ __('Exam:') }} {{ $selectedReportType }}</div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px; color: #334155;">
                    {{ __('Your Phone Number to Receive SMS:') }} <span style="color: #dc2626;">*</span>
                </label>
                <input type="text" name="phone" id="parent_phone_input" required 
                       value="{{ Auth::user()->phone ?: $selectedStudent->effective_parent_phone }}"
                       placeholder="{{ __('e.g. 0712345678 or 0754000000') }}"
                       style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 14.5px; box-sizing: border-box; font-weight: 600;">
                <div style="font-size: 12px; color: #64748b; margin-top: 5px;">
                    {{ __('An SMS will be sent immediately with a summary of marks, average, grade, attendance and fee balance.') }}
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                <button type="button" onclick="closeParentSmsModal()" style="padding: 10px 16px; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 6px; font-weight: 700; cursor: pointer; color: #475569;">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" id="btnParentSmsSubmit" style="padding: 10px 20px; background: #059669; color: #ffffff; border: none; border-radius: 6px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(5, 150, 105, 0.3);">
                    <span>📱</span>
                    <span>{{ __('Send SMS to My Phone') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openParentSmsModal() {
    const modal = document.getElementById('parentSmsModal');
    if (modal) {
        modal.style.display = 'flex';
        const input = document.getElementById('parent_phone_input');
        if (input && !input.value) {
            input.focus();
        }
    }
}

function closeParentSmsModal() {
    const modal = document.getElementById('parentSmsModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function handleParentSmsSubmit() {
    const btn = document.getElementById('btnParentSmsSubmit');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span>⏳</span> <span>Inatuma SMS... Subiri kidogo</span>';
    }
}

window.addEventListener('click', function(e) {
    const modal = document.getElementById('parentSmsModal');
    if (modal && e.target === modal) {
        closeParentSmsModal();
    }
});
</script>
@endif
@endsection
