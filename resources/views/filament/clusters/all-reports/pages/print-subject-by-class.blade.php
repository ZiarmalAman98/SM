@php /** @var \App\Filament\Clusters\AllReports\Pages\PrintSubjectByClass $this */ @endphp

<x-filament::page>
	<x-filament::section>
		<x-slot name="heading">
			{{ __('Print One Subject (Class)') }}
		</x-slot>

		<x-slot name="description">
			{{ __('Select the branch, class, subject, and exam type to generate the report.') }}
		</x-slot>

		<form wire:submit="generate">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<x-filament::input.wrapper>
					<x-filament::input.select wire:model.live="branch_id" label="{{ __('Branch') }}" required>
						<option value="">{{ __('Select Branch') }}</option>
						@foreach(\App\Models\Branch::all() as $branch)
							<option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
						@endforeach
					</x-filament::input.select>
				</x-filament::input.wrapper>

				<x-filament::input.wrapper>
					<x-filament::input.select wire:model.live="class_id" label="{{ __('Class') }}" required>
						<option value="">{{ __('Select Class') }}</option>
						@foreach($this->classes as $class)
							<option value="{{ $class->id }}">{{ $class->class_name }}</option>
						@endforeach
					</x-filament::input.select>
				</x-filament::input.wrapper>

				<x-filament::input.wrapper>
					<x-filament::input.select wire:model.live="subject_id" label="{{ __('Subject') }}" required>
						<option value="">{{ __('Select Subject') }}</option>
						@foreach($this->subjects as $subject)
							<option value="{{ $subject->id }}">{{ $subject->name }}</option>
						@endforeach
					</x-filament::input.select>
				</x-filament::input.wrapper>

				<x-filament::input.wrapper>
					<x-filament::input.select wire:model="exam_type" label="{{ __('Exam Type') }}" required>
						<option value="">{{ __('Select Exam Type') }}</option>
						<option value="mid_term">{{ __('Mid Term') }}</option>
						<option value="final">{{ __('Final') }}</option>
					</x-filament::input.select>
				</x-filament::input.wrapper>
			</div>

			<div class="flex justify-end mt-6">
				<x-filament::button type="submit" icon="heroicon-o-printer">
					{{ __('Generate') }}
				</x-filament::button>
			</div>
		</form>
	</x-filament::section>
</x-filament::page>


