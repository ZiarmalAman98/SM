<style>
/* Aman Private School — Professional Admin Dashboard */
.fi-dashboard-page {
    --aman-blue: #2563eb;
    --aman-cyan: #06b6d4;
    --aman-green: #10b981;
    --aman-orange: #f59e0b;
    --aman-red: #ef4444;
    --aman-ink: #0f172a;
    --aman-muted: #64748b;
    --aman-border: #e5e7eb;
    --aman-card: #ffffff;
    background: #f6f8fc;
}

.dark .fi-dashboard-page {
    --aman-ink: #f8fafc;
    --aman-muted: #94a3b8;
    --aman-border: rgba(148,163,184,.16);
    --aman-card: #111827;
    background: #080d18;
}

/* Page rhythm */
.fi-dashboard-page .fi-wi-grid {
    gap: 1rem;
}

.fi-dashboard-page .fi-wi {
    margin-bottom: 0;
}

/* Welcome hero */
.aman-dashboard-hero {
    position: relative;
    overflow: hidden;
    margin-bottom: 1.25rem;
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 24px;
    background:
        radial-gradient(circle at 85% 20%, rgba(34,211,238,.35), transparent 28%),
        radial-gradient(circle at 70% 100%, rgba(37,99,235,.45), transparent 36%),
        linear-gradient(135deg, #0f172a 0%, #172554 48%, #0369a1 100%);
    color: white;
    box-shadow: 0 20px 50px rgba(15,23,42,.18);
}

.aman-dashboard-hero:after {
    content: "";
    position: absolute;
    width: 240px;
    height: 240px;
    right: -90px;
    top: -110px;
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 50%;
    box-shadow: 0 0 0 35px rgba(255,255,255,.035), 0 0 0 70px rgba(255,255,255,.02);
}

.aman-dashboard-hero .aman-hero-inner {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    padding: 2rem;
}

.aman-dashboard-hero .aman-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: .55rem;
    padding: .4rem .7rem;
    border: 1px solid rgba(255,255,255,.16);
    border-radius: 999px;
    background: rgba(255,255,255,.08);
    color: #bae6fd;
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
}

.aman-dashboard-hero h1 {
    margin: 0;
    color: #fff;
    font-size: clamp(1.6rem, 3vw, 2.35rem);
    font-weight: 800;
    letter-spacing: -.04em;
}

.aman-dashboard-hero p {
    margin-top: .6rem;
    color: rgba(255,255,255,.72);
    font-size: .92rem;
}

.aman-dashboard-hero .aman-date {
    margin-top: .9rem;
    color: rgba(255,255,255,.55);
    font-size: .75rem;
    font-weight: 600;
}

.aman-dashboard-hero .aman-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .7rem;
}

.aman-dashboard-hero .aman-action {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    border-radius: 13px;
    padding: .78rem 1rem;
    font-size: .82rem;
    font-weight: 750;
    text-decoration: none;
    transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
}

.aman-dashboard-hero .aman-action:hover {
    transform: translateY(-2px);
}

.aman-dashboard-hero .aman-action-primary {
    background: #fff;
    color: #0f172a;
    box-shadow: 0 8px 22px rgba(0,0,0,.16);
}

.aman-dashboard-hero .aman-action-secondary {
    border: 1px solid rgba(255,255,255,.18);
    background: rgba(255,255,255,.08);
    color: #fff;
    backdrop-filter: blur(10px);
}

/* KPI cards */
.fi-dashboard-page .fi-wi-stats-overview {
    gap: 1rem;
}

.fi-dashboard-page .fi-wi-stats-overview-stat {
    position: relative;
    overflow: hidden;
    min-height: 138px;
    border: 1px solid var(--aman-border) !important;
    border-radius: 20px !important;
    background: var(--aman-card) !important;
    box-shadow: 0 8px 30px rgba(15,23,42,.055) !important;
    transition: transform .18s ease, box-shadow .18s ease;
}

.fi-dashboard-page .fi-wi-stats-overview-stat:before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 3px;
    background: linear-gradient(90deg, var(--aman-blue), var(--aman-cyan));
}

.fi-dashboard-page .fi-wi-stats-overview-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 38px rgba(15,23,42,.10) !important;
}

.fi-dashboard-page .fi-wi-stats-overview-stat-value {
    font-size: 1.45rem !important;
    font-weight: 800 !important;
    letter-spacing: -.035em;
}

.fi-dashboard-page .fi-wi-stats-overview-stat-label {
    font-weight: 700 !important;
}

.fi-dashboard-page .fi-wi-stats-overview-stat-description {
    color: var(--aman-muted) !important;
    font-size: .75rem !important;
}

/* Charts / tables / calendar */
.fi-dashboard-page .fi-section,
.fi-dashboard-page .fi-wi-chart,
.fi-dashboard-page .fi-ta-ctn {
    overflow: hidden;
    border: 1px solid var(--aman-border) !important;
    border-radius: 20px !important;
    background: var(--aman-card) !important;
    box-shadow: 0 8px 30px rgba(15,23,42,.045) !important;
}

.fi-dashboard-page .fi-wi-chart .fi-wi-header,
.fi-dashboard-page .fi-section-header {
    padding-bottom: .8rem;
}

.fi-dashboard-page .fi-wi-chart canvas {
    max-height: 310px;
}

.fi-dashboard-page .fi-ta-header {
    border-bottom: 1px solid var(--aman-border);
}

.fi-dashboard-page .fi-ta-row {
    transition: background .15s ease;
}

.fi-dashboard-page .fi-ta-row:hover {
    background: rgba(37,99,235,.035);
}

.fi-dashboard-page .fi-btn,
.fi-dashboard-page .fi-input,
.fi-dashboard-page .fi-select {
    border-radius: 11px;
}

/* Calendar */
.fi-dashboard-page .fc {
    border-radius: 16px;
    overflow: hidden;
}

.fi-dashboard-page .fc .fc-toolbar-title {
    font-size: 1.05rem;
    font-weight: 800;
}

.fi-dashboard-page .fc .fc-button {
    border-radius: 9px;
}

/* RTL-friendly spacing */
[dir="rtl"] .aman-dashboard-hero .aman-hero-inner {
    flex-direction: row-reverse;
}

/* Mobile */
@media (max-width: 767px) {
    .aman-dashboard-hero .aman-hero-inner {
        flex-direction: column;
        align-items: flex-start;
        padding: 1.35rem;
    }

    [dir="rtl"] .aman-dashboard-hero .aman-hero-inner {
        flex-direction: column;
        align-items: flex-start;
    }

    .aman-dashboard-hero .aman-actions {
        width: 100%;
    }

    .aman-dashboard-hero .aman-action {
        flex: 1;
        justify-content: center;
    }

    .fi-dashboard-page .fi-wi-stats-overview-stat {
        min-height: 125px;
        border-radius: 16px !important;
    }
}
</style>