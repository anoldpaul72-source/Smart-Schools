@extends('layouts.app')

@section('title', 'Teacher Timetable | Smart-Results')

@section('content')
<div style="margin-bottom: 25px;">
    <h1 style="font-size: 26px; font-weight: 800;">My Teaching Schedule & Timetable</h1>
    <p style="color: var(--text-muted); font-size: 14px;">Review your allocated classroom instruction periods across the academic week</p>
</div>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Day of Week</th>
                <th>Period</th>
                <th>Class / Form</th>
                <th>Subject</th>
                <th>School</th>
            </tr>
        </thead>
        <tbody>
            @forelse($slots as $slot)
                <tr>
                    <td><b>{{ $slot->day_of_week }}</b></td>
                    <td>Period {{ $slot->period_number }}</td>
                    <td>{{ $slot->class_name }}</td>
                    <td>{{ $slot->subject ? $slot->subject->subject_name : 'Subject' }}</td>
                    <td>{{ $slot->school_name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">
                        No timetable slots currently configured for your account. Please consult the Academic Master or Administrator.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
