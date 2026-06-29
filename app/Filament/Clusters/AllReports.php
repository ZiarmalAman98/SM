<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use App\Filament\Clusters\AllReports\Pages;

class AllReports extends Cluster
{
	protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

	protected static ?string $slug = 'all-reports';

	protected static ?string $navigationLabel = 'All Reports';

	public static function getNavigationGroup(): string
	{
		return 'Reports';
	}

    public static function getPages(): array
    {
        return [
            Pages\PrintBiography::route('/'),
            Pages\CartSwaneh::route('/cart-swaneh'),
            Pages\StudentInformationReport::route('/student-information-report'),
            Pages\FinancialSummaryReport::route('/financial-summary-report'),
            Pages\ExamResultsReport::route('/exam-results-report'),
            Pages\GenerateExamCard::route('/exam-card'),
        ];
    }
}

