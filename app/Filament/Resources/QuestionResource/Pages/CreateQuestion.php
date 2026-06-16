<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Models\Question;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\QuestionResource;
use Illuminate\Database\Eloquent\Model;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure user_id is set for each question
        if (isset($data['questions'])) {
            foreach ($data['questions'] as &$question) {
                $question['user_id'] = auth()->id();
            }
        } else {
            // Fallback for single question creation
            $data['user_id'] = auth()->id();
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Handle multiple questions creation
        if (isset($data['questions'])) {
            $createdQuestions = [];

            foreach ($data['questions'] as $questionData) {
                $question = $this->createQuestionWithChoices($questionData);
                $createdQuestions[] = $question;
            }

            // Return the first question as the created record
            return $createdQuestions[0] ?? new Question();
        }

        // Fallback to single question creation
        return $this->createQuestionWithChoices($data);
    }

    protected function createQuestionWithChoices(array $questionData): Question
    {
        $question = Question::create([
            'user_id' => $questionData['user_id'],
            'language_id' => $questionData['language_id'],
            'subject_id' => $questionData['subject_id'],
            'difficulty_id' => $questionData['difficulty_id'],
            'type' => $questionData['type'],
            'question_text' => $questionData['question_text'],
            'explanation' => $questionData['explanation'] ?? null,
        ]);

        // Create choices if they exist in the data
        if (isset($questionData['choices']) && is_array($questionData['choices'])) {
            foreach ($questionData['choices'] as $choice) {
                $question->choices()->create($choice);
            }
        } elseif (in_array($questionData['type'], ['multiple_choice', 'true_false'])) {
            // Create default choices if none provided
            $this->createDefaultChoices($question, $questionData['type']);
        }

        return $question;
    }

    protected function createDefaultChoices(Question $question, string $type): void
    {
        $defaultChoices = match ($type) {
            'true_false' => [
                ['choice_text' => __('True'), 'is_correct' => true],
                ['choice_text' => __('False'), 'is_correct' => false],
            ],
            default => [
                ['choice_text' => '', 'is_correct' => true],
                ['choice_text' => '', 'is_correct' => false],
                ['choice_text' => '', 'is_correct' => false],
                ['choice_text' => '', 'is_correct' => false],
            ],
        };

        foreach ($defaultChoices as $choice) {
            $question->choices()->create($choice);
        }
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        if (isset($this->data['questions'])) {
            $count = count($this->data['questions']);
            return $count > 1
                ? __(':count questions created successfully', ['count' => $count])
                : __('Question created successfully');
        }

        return __('Question created successfully');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Only send notification if not handling multiple questions
        if (!isset($this->data['questions'])) {
            Notification::make()
                ->title(__('Question created successfully'))
                ->success()
                ->send();
        }
    }
}
