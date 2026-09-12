<td>
    @if(isset($timetableMatrix[$day][$p]))
        @php $slot = $timetableMatrix[$day][$p]; @endphp
        <div class="slot-box">
            <div class="slot-subject">{{ $slot['subject'] }}</div>
            <span class="slot-teacher">👤 {{ $slot['teacher'] }}</span>
            <button type="button" class="btn-edit" onclick="handleEdit('{{ $day }}', {{ $p }}, {{ $slot['subject_id'] }}, {{ $slot['teacher_id'] }})">✏️ {{ __('Edit') }}</button>
        </div>
    @else
        <button type="button" class="btn-add" onclick="handleEdit('{{ $day }}', {{ $p }}, '', '')">+ {{ __('Add') }}</button>
    @endif
</td>
