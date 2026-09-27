<style>
    .fi-simple-layout {
        min-height: 100vh;
        background:
            radial-gradient(circle at 12% 14%, rgba(18, 173, 128, .20), transparent 24rem),
            radial-gradient(circle at 88% 82%, rgba(243, 178, 61, .16), transparent 22rem),
            linear-gradient(135deg, #f4fbf9 0%, #f7fafc 52%, #eaf4f8 100%);
    }

    .fi-simple-main {
        width: min(100% - 2rem, 29rem);
        padding: 2.25rem;
        border: 1px solid rgba(8, 45, 77, .10);
        border-radius: 1.25rem;
        background: rgba(255, 255, 255, .94);
        box-shadow: 0 24px 60px rgba(8, 45, 77, .13);
        backdrop-filter: blur(12px);
    }

    .fi-logo {
        margin-bottom: 1.35rem;
    }

    .fi-simple-header-heading {
        color: #082d4d;
        font-weight: 800;
    }

    .fi-simple-header-subheading::before {
        content: 'Aman Private School Administration · ';
        color: #099b75;
        font-weight: 700;
    }

    .fi-btn-color-primary {
        border-radius: .65rem;
        background: #099b75;
        box-shadow: 0 10px 20px rgba(9, 155, 117, .20);
    }

    .fi-btn-color-primary:hover { background: #067858; }

    .fi-input-wrp {
        border-radius: .65rem;
    }

    @media (max-width: 640px) {
        .fi-simple-main { padding: 1.5rem; border-radius: 1rem; }
    }
</style>
