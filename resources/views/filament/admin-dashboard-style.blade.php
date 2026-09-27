<style>
/* Aman Private School — Modern Admin Dashboard */
.fi-dashboard-page {
    --aman-primary: #24aae1;
    --aman-ink: #0f172a;
    --aman-muted: #64748b;
    --aman-border: #e2e8f0;
    --aman-surface: #ffffff;
}

.dark .fi-dashboard-page {
    --aman-ink: #f8fafc;
    --aman-muted: #94a3b8;
    --aman-border: rgba(148, 163, 184, .18);
    --aman-surface: #0f172a;
}

.fi-dashboard-page .fi-wi {
    margin-bottom: 1.25rem;
}

.fi-dashboard-page .fi-wi-stats-overview .fi-wi-stats-overview-stat {
    border: 1px solid var(--aman-border);
    border-radius: 18px;
    background: var(--aman-surface);
    box-shadow: 0 8px 28px rgba(15, 23, 42, .055);
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    overflow: hidden;
}

.fi-dashboard-page .fi-wi-stats-overview .fi-wi-stats-overview-stat:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 34px rgba(15, 23, 42, .09);
    border-color: rgba(36, 170, 225, .35);
}

.fi-dashboard-page .fi-wi-stats-overview .fi-wi-stats-overview-stat-icon {
    border-radius: 12px;
}

.fi-dashboard-page .fi-wi-stats-overview .fi-wi-stats-overview-stat-value {
    letter-spacing: -.025em;
    font-weight: 750;
}

.fi-dashboard-page .fi-wi-stats-overview .fi-wi-stats-overview-stat-description {
    color: var(--aman-muted);
}

.fi-dashboard-page .fi-section,
.fi-dashboard-page .fi-ta-ctn,
.fi-dashboard-page .fi-wi-chart {
    border: 1px solid var(--aman-border);
    border-radius: 18px;
    box-shadow: 0 8px 28px rgba(15, 23, 42, .045);
}

.fi-dashboard-page .fi-wi-chart {
    background: var(--aman-surface);
    overflow: hidden;
}

.fi-dashboard-page .fi-wi-chart .fi-wi-header {
    padding-bottom: .5rem;
}

.fi-dashboard-page .fi-ta-header {
    border-bottom: 1px solid var(--aman-border);
}

.fi-dashboard-page .fi-ta-table {
    border-radius: 14px;
    overflow: hidden;
}

.fi-dashboard-page .fi-ta-row {
    transition: background-color .15s ease;
}

.fi-dashboard-page .fi-ta-row:hover {
    background: rgba(36, 170, 225, .035);
}

.fi-dashboard-page .fi-btn {
    border-radius: 11px;
}

.fi-dashboard-page .fi-input {
    border-radius: 11px;
}

@media (max-width: 767px) {
    .fi-dashboard-page .fi-wi-stats-overview {
        gap: .75rem;
    }

    .fi-dashboard-page .fi-wi-stats-overview .fi-wi-stats-overview-stat {
        border-radius: 15px;
    }
}

/* Reduce visual noise from Filament's default dashboard spacing */
.fi-dashboard-page .fi-wi-grid {
    row-gap: 1rem;
}
</style>