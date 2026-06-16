<table>
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Class</th>
            <th>Roll Number</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $student)
            <tr>
                <td>{{ $student->student_id ?? '' }}</td>
                <td>{{ $student->user->name ?? 'N/A' }}</td>
                <td>{{ $class->class_name ?? 'N/A' }}</td>
                <td>{{ $student->roll_number ?? '' }}</td>
                <td>Active</td>
            </tr>
        @endforeach
    </tbody>
</table>