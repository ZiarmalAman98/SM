<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher Evaluation Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.5;
            color: #333;
            background: #f9f9f9;
            padding: 20px;
        }

        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
            overflow: hidden;
        }

        .report-header {
            padding: 15px 30px;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .report-meta-inline {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            font-size: 13px;
        }

        .report-meta-inline strong {
            font-size: 16px;
            margin-bottom: 4px;
        }

        .filter-section {
            padding: 40px 20px;
            background: #f5f9ff;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        .filter-section p {
            padding: 6px;
            float: left;
            margin-right: 20px;
        }

        .data-section {
            padding: 30px;
        }

        .data-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            display: inline-block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        th,
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f2f6fc;
            font-weight: 600;
            color: #2c3e50;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .stars {
            color: #f5c518;
            font-size: 16px;
        }

        .anonymous {
            color: #999;
            font-style: italic;
        }

        .report-footer {
            padding: 20px 30px;
            background: #2c3e50;
            color: white;
            text-align: center;
            font-size: 13px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 20mm;
            }

            .filter-section p {
                padding: 6px;
                float: left;
                margin-right: 20px;
            }

            body {
                background: white;
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .report-container {
                box-shadow: none;
                max-width: 100%;
            }

            .print-button {
                display: none;
            }

            .data-section {
                break-inside: avoid-page;
            }
        }

        .print-button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            display: inline-block;
            margin: 20px 30px 0;
            float: right;
        }

        .print-button:hover {
            background: #2980b9;
        }

        @media (max-width: 768px) {
            .report-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .report-meta-inline {
                align-items: center;
            }

            .logo-section {
                margin-bottom: 10px;
            }

            .print-button {
                float: none;
                text-align: center;
                margin: 20px auto 0;
            }
        }
    </style>
</head>

<body>
    <div class="report-container">

        <!-- Compact Header -->
        <div class="report-header">
            <div class="logo-section">
                {{-- <img src="{{ asset('schools/cosmos.png') }}" alt="Logo" class="logo"> --}}
                      <img 
    src="@if(isset($settings['app_logo']))
             {{ asset('storage/' . $settings['app_logo']) }}
         @else
             {{ asset('schools/cosmos.png')}}
         @endif"
    alt="Logo" class="logo">
            </div>
            <div class="report-meta-inline">
                <strong>Teacher Evaluation Report</strong>
                <div>
                    <span><strong>Generated:</strong> {{ now()->format('F d, Y') }}</span>
                    &nbsp;|&nbsp;
                    <span><strong>Report ID:</strong> #{{ rand(10000, 99999) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <p><strong>Teacher:</strong>
                @php
                    $teacher = $responses->firstWhere('teacher_id', request('teacher_id'));
                @endphp
                {{ request()->filled('teacher_id') && $teacher && $teacher->teacher ? $teacher->teacher->name : 'All' }}
            </p>

            <p><strong>Student:</strong>
                @php
                    $student = $responses->firstWhere('student_id', request('student_id'));
                @endphp
                {{ request()->filled('student_id') && $student && $student->student ? $student->student->name : 'All' }}
            </p>

            <p><strong>Academic Year:</strong>
                {{ request()->filled('academic_year') ? e(request('academic_year')) : 'All' }}
            </p>
        </div>


        <!-- Data Section -->
        <div class="data-section">
            <h2 class="data-title">Evaluation Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Student</th>
                        <th>Question</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($responses as $response)
                        <tr>
                            <td>{{ $response->teacher->name ?? '-' }}</td>
                            <td>{{ $response->student->name ?? 'Anonymous' }}</td>
                            <td>{{ $response->question->question_text }}</td>
                            <td class="stars">{{ str_repeat('★', $response->rating) }}</td>
                            <td>{{ $response->comment ?: '-' }}</td>
                            <td>{{ $response->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">No evaluations found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="report-footer">
            <p>&copy; {{ date('Y') }} {{ $settings['app_name'] ?? config('app.name') }}. All rights reserved.</p>
            <p>This report is generated automatically and is valid without a signature.</p>
        </div>
    </div>

    <script>
        window.onload = function () {
            // Automatically trigger the print dialog
            window.print();

            // Close the tab after print or cancel
            window.onafterprint = function () {
                window.close();
            };

            // Fallback in case the user cancels the print dialog in some browsers
            setTimeout(() => {
                window.close();
            }, 500); // Small delay to ensure compatibility across browsers
        };
    </script>
</body>

</html>