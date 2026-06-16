<table>
    <thead>
        <tr>
            <th>Student</th>
            <th>Class</th>
            <th>Branch</th>
            <th>Date</th>
            <th>Morning</th>
            <th>Afternoon</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->student->name ?? '-' }}</td>
                <td>{{ $record->class->class_name ?? '-' }}</td>
                <td>{{ $record->branch->branch_name ?? '-' }}</td>
                <td>{{ App\Helpers\DateHelper::toShamsi($record->date) }}</td>
                <td>{{ $record->morning ? '✓' : '×' }}</td>
                <td>{{ $record->afternoon ? '✓' : '×' }}</td>
                <td>{{ ucfirst($record->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
