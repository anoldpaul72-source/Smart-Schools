<td>
    @if(isset($timetableMatrix[$day][$p]))
        @php
            $slot = $timetableMatrix[$day][$p];
            $subLower = strtolower($slot['subject'] ?? '');
            $cellBg = '#f0fdf4';
            $cellBorder = '#bbf7d0';
            $cellColor = '#166534';

            if (str_contains($subLower, 'general studies')) {
                $cellBg = '#f5f3ff'; $cellBorder = '#c4b5fd'; $cellColor = '#5b21b6';
            } elseif (str_contains($subLower, 'basic applied')) {
                $cellBg = '#f0fdfa'; $cellBorder = '#99f6e4'; $cellColor = '#0f766e';
            } elseif (str_contains($subLower, 'advanced math')) {
                $cellBg = '#ecfdf5'; $cellBorder = '#6ee7b7'; $cellColor = '#047857';
            } elseif (str_contains($subLower, 'economics')) {
                $cellBg = '#fff7ed'; $cellBorder = '#fdba74'; $cellColor = '#c2410c';
            }
        @endphp
        <div class="slot-box" style="background: {{ $cellBg }}; border-color: {{ $cellBorder }};">
            <div class="slot-subject" style="color: {{ $cellColor }};">
                {{ __($slot['subject']) }}
            </div>
            <span class="slot-teacher">👤 {{ $slot['teacher'] }}</span>
            <button type="button" class="btn-edit" onclick="handleEdit('{{ $day }}', {{ $p }}, {{ $slot['subject_id'] }}, {{ $slot['teacher_id'] }})">✏️ {{ __('Edit') }}</button>
        </div>
    @else
        <button type="button" class="btn-add" onclick="handleEdit('{{ $day }}', {{ $p }}, '', '')">+ {{ __('Add') }}</button>
    @endif
</td>
