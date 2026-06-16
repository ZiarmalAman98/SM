<x-filament::page>
    <x-filament::section>
        <form wire:submit="generate">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Class Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class</label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="class_id" placeholder="Select Class" required>
                            <option value="">Select Class</option>
                            @foreach($this->classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Student Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student</label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model="student_id" placeholder="Select Student" required>
                            <option value="">Select Student</option>
                            @foreach($this->students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} {{ $student->last_name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>

            <!-- Generate Button -->
            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" color="success" size="lg">
                    Generate Fee Card
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament::page>
