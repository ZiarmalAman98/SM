<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Student Name</th>
            <th>Father Name</th>
            <th>Roll Number</th>
            <th>Admission No</th>
            <th>Date of Birth</th>
            <th>Gender</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $index => $student)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $student->user->name ?? '' }}</td>
            <td>{{ $student->user->father_name ?? '' }}</td>
            <td>{{ $student->roll_no ?? '' }}</td>
            <td>{{ $student->admission_no ?? '' }}</td>
            <td>{{ $student->dob ? $student->dob->format('Y-m-d') : '' }}</td>
            <td>{{ $student->gender ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>