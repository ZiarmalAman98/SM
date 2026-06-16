<!DOCTYPE html>
<html lang="en" dir="{{ request('rtl') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .page-break {
                page-break-before: always;
            }

            @page {
                size: A4;
                margin: 1.5cm;
            }
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            font-size: 11px;
            direction: {{ request('rtl') ? 'rtl' : 'ltr' }};
            text-align: {{ request('rtl') ? 'right' : 'left' }};
        }

        .header-line {
            position: relative;
            padding-bottom: 0.5rem;
        }

        .header-line::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #2563eb, #60a5fa);
            border-radius: 2px;
        }

        .option-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(1px, 1fr));
            gap: 0.2rem;
            width: 100%;
        }

        .question-number {
            width: 14px;
            height: 14px;
            border: 1px solid #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 11px;
        }

        .answer-circle {
            width: 12px;
            height: 12px;
            border: 1px solid #2563eb;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 100;
            color: #2563eb;
            margin-right: 4px;
            font-size: 9px;
            background-color: #f8fafc;
        }

        .answer-line {
            position: relative;
            border-bottom: 1px solid #94a3b8;
        }

        .answer-line::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 0;
            width: 100%;
            border-bottom: 1px dotted #cbd5e1;
        }

        .section-title {
            position: relative;
            overflow: hidden;
            padding: 0.5rem 1rem;
            background: #f1f5f9;
            border-left: 4px solid #2563eb;
        }

        .section-title::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 40px;
            background: linear-gradient(to right, transparent, #f1f5f9);
        }
    </style>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
                font-size: 12px;
            }

            .page-break {
                page-break-before: always;
            }

            @page {
                size: A4;
                margin: 1.5cm;
            }

            .student-info {
                display: flex !important;
                flex-wrap: nowrap !important;
                justify-content: space-between;
                align-items: center;
                gap: 1rem;
                width: 100%;
                font-size: 12px !important;
            }

            .student-info div {
                flex: 1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .border-b-2 {
                display: block;
                width: 100%;
                min-height: 20px;
                border-bottom: 1px solid #000 !important;
                background: transparent !important;
            }

            .header-line::after {
                height: 3px !important;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50 mt-10">
    <div class="max-w-[21cm] mx-auto bg-white shadow-lg p-8 my-6 print:shadow-none print:my-0 print:p-0">
        <!-- Enhanced Header -->
        <div class="mb-8">
            <div class="flex justify-between items-start gap-5 header-line">
                {{-- <img src="{{ asset('logo.png') }}" alt="Logo" class="object-contain" style="height: 4rem;"> --}}
                <img src="@if (isset($settings['app_logo'])) {{ asset('storage/' . $settings['app_logo']) }}
         @else
             {{ asset('logo.png') }} @endif"
                    alt="Logo" class="object-contain" style="height: 4rem;">
                <div class="flex-1 text-center">
                    <h1 class="text-3xl font-bold text-gray-800 tracking-tight">{{ $exam_name }}</h1>
                    <div class="mt-2 flex justify-center gap-6 text-sm text-gray-600">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $duration }} minutes
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            Total Score: {{ $total_score }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ App\Helpers\DateHelper::toShamsi($exam_date) }}
                        </span>
                    </div>
                </div>
                <div class="w-20"></div> <!-- Spacer for balance -->
            </div>
        </div>

        <!-- Student Information -->
        <!-- Student Information -->
        <div class="rounded-lg p-4 mb-4 student-info">
            <div>
                <label class="text-sm font-medium text-gray-600">Roll Number</label>
                <div class="border-b-2"></div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Student Name</label>
                <div class="border-b-2"></div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Class</label>
                <div class="border-b-2"></div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Date</label>
                <div class="border-b-2"></div>
            </div>
        </div>


        <!-- Questions Section -->
        @foreach ($grouped_questions as $type => $questions)
            <div class="mb-4">
                <h2 class="section-title text-sm font-semibold mb-4" style="font-size: 12px;">
                    {{ strtoupper(str_replace('_', ' ', $type)) }}
                    <span style="float: right;color: black;margin-right: 30px;">Score:
                        {{ $question_scores[$type] ?? 0 }}</span>

                </h2>

                <div class="">
                    @foreach ($questions as $index => $question)
                        <div class="p-1">
                            <div class="flex gap-2">
                                <span class="question-number shrink-0">{{ $index + 1 }}</span>
                                <div class="flex-1 mb-2">
                                    <p class="text-gray-800">{{ $question->question_text }}</p>

                                    @if ($type === 'multiple_choice')
                                        @php
                                            $shuffledChoices = collect($question->choices)->shuffle();
                                            $optionLetters = ['A', 'B', 'C', 'D'];
                                        @endphp
                                        <div class="option-grid">
                                            @foreach ($shuffledChoices as $choiceIndex => $choice)
                                                <div
                                                    class="flex items-center p-2 rounded-md hover:bg-blue-50 transition-colors">
                                                    <span
                                                        class="answer-circle">{{ $optionLetters[$choiceIndex] }}</span>
                                                    <span class="text-gray-700">{{ $choice->choice_text }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($type === 'true_false')
                                        <div class="flex gap-10" style="justify-content: space-around;">
                                            <label class="flex items-center gap-2  p-1 ">
                                                <span class="answer-circle">T</span>
                                                <span class="text-gray-700">True</span>
                                            </label>
                                            <label class="flex items-center gap-2">
                                                <span class="answer-circle">F</span>
                                                <span class="text-gray-700">False</span>
                                            </label>
                                        </div>
                                    @elseif ($type === 'fill_in_the_blank')

                                    @elseif ($type === 'written')
                                        <div class="space-y-2">
                                            @for ($i = 0; $i < 5; $i++)
                                                <div class="answer-line h-4"></div>
                                            @endfor
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach


        <!-- Print Button -->
        <div class="no-print fixed bottom-6 right-6">
            <button onclick="window.print()"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                Print
            </button>
        </div>
    </div>
</body>

</html>
