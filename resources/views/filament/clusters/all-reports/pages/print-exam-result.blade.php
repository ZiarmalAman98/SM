@php /** @var \App\Filament\Clusters\AllReports\Pages\PrintExamResult $this */ @endphp

<x-filament::page>
	<x-filament::section>
		<x-slot name="heading">
			{{ __('Print Exam Result') }}
		</x-slot>

		<x-slot name="description">
			{{ __('Select the student and class to generate the exam result report.') }}
		</x-slot>

		<form wire:submit="generate">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<x-filament::input.wrapper>
					<x-filament::input.select wire:model="student_id" label="{{ __('Student') }}" required>
						<option value="">{{ __('Select Student') }}</option>
						@foreach($this->students as $student)
							<option value="{{ $student->id }}">{{ $student->name }}</option>
						@endforeach
					</x-filament::input.select>
				</x-filament::input.wrapper>

				<x-filament::input.wrapper>
					<x-filament::input.select wire:model="class_id" label="{{ __('Class') }}" required>
						<option value="">{{ __('Select Class') }}</option>
						@foreach($this->classes as $class)
							<option value="{{ $class->id }}">{{ $class->class_name }}</option>
						@endforeach
					</x-filament::input.select>
				</x-filament::input.wrapper>
			</div>

			<div class="flex justify-end mt-6">
				<x-filament::button type="submit" icon="heroicon-o-printer">
					{{ __('Generate Report') }}
				</x-filament::button>
			</div>
		</form>
	</x-filament::section>
</x-filament::page>


