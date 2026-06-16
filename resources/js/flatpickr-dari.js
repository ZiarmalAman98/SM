/**
 * Replace Iranian Solar-Hijri month names with Afghan/Dari ones
 * Works for both ->locale('fa') and ->locale('ps')
 */
document.addEventListener("DOMContentLoaded", () => {
    if (!window.dayjs) return;

    const months = [
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

    window.dayjs.updateLocale("fa", { months, monthsShort: months });
    window.dayjs.updateLocale("ps", { months, monthsShort: months });
});
