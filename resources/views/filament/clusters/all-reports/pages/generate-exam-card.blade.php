@php /** @var \App\Filament\Clusters\AllReports\Pages\GenerateExamCard $this */ @endphp

<x-filament::page>
    <style>
        .exam-card-page {
            --hb-brand: #c8642c;
            --hb-brand-dark: #9f4d20;
            --hb-brand-soft: #fff4ec;
            --hb-line: rgba(200, 100, 44, 0.22);
        }

        .exam-card-panel {
            border: 1px solid var(--hb-line);
            border-radius: 1.25rem;
            background:
                radial-gradient(circle at top right, rgba(200, 100, 44, 0.18), transparent 28%),
                linear-gradient(180deg, rgba(28, 25, 23, 0.98), rgba(17, 17, 17, 0.98));
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.28);
        }

        .exam-card-title {
            color: #f8d9c4;
        }

        .exam-card-subtitle {
            color: #d1d5db;
        }

        .exam-card-label {
            color: #f4a474;
            font-weight: 700;
        }

        .exam-card-select {
            width: 100%;
            border: none;
            background: transparent;
            background-image: none !important;
            color: rgb(255 255 255);
            padding: 0.5rem 0.75rem;
            font-size: 1rem;
            outline: none;
            box-shadow: none;
            appearance: auto;
            -webkit-appearance: auto;
            -moz-appearance: auto;
        }

        .exam-card-panel :where([data-slot="input-wrapper"]) {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .exam-card-select option {
            background-color: rgb(24 24 27);
            color: rgb(255 255 255);
        }

        .exam-card-button {
            --c-400: var(--hb-brand) !important;
            --c-500: var(--hb-brand) !important;
            --c-600: var(--hb-brand-dark) !important;
        }
    </style>

    <x-filament::section class="exam-card-page exam-card-panel">
        <x-slot name="heading">
            <span class="exam-card-title">{{ __('Exam Card') }}</span>
        </x-slot>

        <x-slot name="description">
            <span class="exam-card-subtitle">{{ __('Select class, student, and exam details to print the exam attending card.') }}</span>
        </x-slot>

        <form
            wire:submit="generate"
            class="space-y-6"
            x-data="{
                classId: @entangle('class_id'),
                studentId: @entangle('student_id'),
                examType: @entangle('exam_type'),
                examId: @entangle('exam_id'),
                studentOptionsByClass: @js($studentOptionsByClass),
                examOptionsByClassType: @js($examOptionsByClassType),
                get studentOptions() {
                    return this.classId ? (this.studentOptionsByClass[this.classId] || {}) : {};
                },
                get examOptions() {
                    const key = this.classId && this.examType ? `${this.classId}|${this.examType}` : null;
                    return key ? (this.examOptionsByClassType[key] || {}) : {};
                },
                resetStudentIfMissing() {
                    if (this.studentId && !Object.prototype.hasOwnProperty.call(this.studentOptions, this.studentId)) {
                        this.studentId = null;
                    }
                },
                resetExamIfMissing() {
                    if (this.examId && !Object.prototype.hasOwnProperty.call(this.examOptions, this.examId)) {
                        this.examId = null;
                    }
                },
            }"
            x-effect="resetStudentIfMissing(); resetExamIfMissing();"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="exam-card-label block text-sm mb-2">Class</label>
                    <x-filament::input.wrapper>
                        <select x-model="classId" required class="exam-card-select">
                            <option value="">Select Class</option>
                            @foreach($this->classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </x-filament::input.wrapper>
                    @error('class_id') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="exam-card-label block text-sm mb-2">Student</label>
                    <x-filament::input.wrapper>
                        <select x-model="studentId" required class="exam-card-select">
                            <option value="">Select Student</option>
                            <template x-for="(studentLabel, optionValue) in studentOptions" :key="optionValue">
                                <option :value="optionValue" x-text="studentLabel"></option>
                            </template>
                        </select>
                    </x-filament::input.wrapper>
                    @error('student_id') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="exam-card-label block text-sm mb-2">Exam Type</label>
                    <x-filament::input.wrapper>
                        <select x-model="examType" required class="exam-card-select">
                            <option value="mid_term">Mid-term Exam</option>
                            <option value="final">Final Exam</option>
                        </select>
                    </x-filament::input.wrapper>
                    @error('exam_type') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="exam-card-label block text-sm mb-2">Exam</label>
                    <x-filament::input.wrapper>
                        <select x-model="examId" class="exam-card-select">
                            <option value="">Latest Matching Exam</option>
                            <template x-for="(examLabel, optionValue) in examOptions" :key="optionValue">
                                <option :value="optionValue" x-text="examLabel"></option>
                            </template>
                        </select>
                    </x-filament::input.wrapper>
                    @error('exam_id') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <x-filament::button type="submit" icon="heroicon-o-printer" class="exam-card-button">
                    {{ __('Generate Exam Card') }}
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament::page>
