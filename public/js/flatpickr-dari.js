// Replace Iranian month names with Afghan (Dari) ones for Jalali pickers.
document.addEventListener("alpine:init", () => {
    const dariMonths = [
        "حمل",
        "ثور",
        "جوزا",
        "سرطان",
        "اسد",
        "سنبله",
        "میزان",
        "عقرب",
        "قوس",
        "جدی",
        "دلو",
        "حوت",
    ];

    const patch = () => {
        // Wait until Flatpickr is present.
        if (window.flatpickr?.l10ns?.fa?.months) {
            window.flatpickr.l10ns.fa.months.longhand = dariMonths;
            window.flatpickr.l10ns.fa.months.shorthand = dariMonths;
        } else {
            setTimeout(patch, 50);
        }
    };

    patch();
});
