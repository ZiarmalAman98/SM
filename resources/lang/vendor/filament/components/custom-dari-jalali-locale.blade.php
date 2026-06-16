<script>
    document.addEventListener('alpine:init', () => {
        if (window.dayjs) {
            dayjs.locale('fa', {
                months: ['حمل', 'ثور', 'جوزا', 'سرطان', 'اسد', 'سنبله', 'میزان', 'عقرب', 'قوس', 'جدی',
                    'دلو', 'حوت'
                ],
                monthsShort: ['حمل', 'ثور', 'جوزا', 'سر', 'اسد', 'سن', 'میز', 'عق', 'قو', 'جد', 'دل',
                    'حو'
                ],
                weekdays: ['شنبه', 'یک‌شنبه', 'دو‌شنبه', 'سه‌شنبه', 'چهار‌شنبه', 'پنج‌شنبه', 'جمعه'],
                weekdaysShort: ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'],
                weekdaysMin: ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'],
                formats: {
                    LT: 'HH:mm',
                    LTS: 'HH:mm:ss',
                    L: 'YYYY/MM/DD',
                    LL: 'D MMMM YYYY',
                    LLL: 'D MMMM YYYY HH:mm',
                    LLLL: 'dddd، D MMMM YYYY HH:mm',
                },
                weekStart: 6, // Saturday
            });
        }
    });
</script>
