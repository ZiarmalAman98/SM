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
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Father Name (Optional) -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Father Name (Optional)</label>
                    <x-filament::input.wrapper>
                        <input wire:model="father_name" type="text" class="fi-input block w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.600)]" placeholder="Father Name (Optional)" />
                    </x-filament::input.wrapper>
                </div>
            </div>

            <!-- Generate Button -->
            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" color="primary" size="lg">
                    Generate Appreciation Letter
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament::page>
