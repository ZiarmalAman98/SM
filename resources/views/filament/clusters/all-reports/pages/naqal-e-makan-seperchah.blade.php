<x-filament::page>
    <x-filament::section>
        <form wire:submit="generate">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Branch -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Branch') }}</label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="branch_id" required>
                            <option value="">{{ __('Select Branch') }}</option>
                            @foreach($this->branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Class -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Class') }}</label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="class_id" required>
                            <option value="">{{ __('Select Class') }}</option>
                            @foreach($this->classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Academic Year -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Academic Year') }}</label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="academic_year">
                            <option value="">{{ __('All Years') }}</option>
                            @foreach($this->academicYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Student -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Student') }}</label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model="student_id" required>
                            <option value="">{{ __('Select Student') }}</option>
                            @foreach($this->students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" color="primary" size="lg">
                    {{ __('Generate') }}
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament::page>


