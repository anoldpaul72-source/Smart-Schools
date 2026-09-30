<td>
    @if(isset($timetableMatrix[$day][$p]))
        @php
            $slot = $timetableMatrix[$day][$p];
            $isEvent = !empty($slot['is_event']);
            $subLower = strtolower($slot['subject'] ?? '');
            $cellBg = '#f0fdf4';
            $cellBorder = '#bbf7d0';
            $cellColor = '#166534';

            if ($isEvent) {
                $cellBg = '#fffbeb';
                $cellBorder = '#fcd34d';
                $cellColor = '#92400e';
            } elseif (str_contains($subLower, 'general studies')) {
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
                @if($isEvent)
                    🏆 {{ __($slot['event_name'] ?? $slot['subject']) }}
                @else
                    {{ __($slot['subject']) }}
                @endif
            </div>
            @if(!empty($slot['teacher']))
                <span class="slot-teacher">👤 {{ $slot['teacher'] }}</span>
            @elseif($isEvent)
                <span class="slot-teacher" style="color: #b45309; font-weight: 600;">📌 {{ __('Event / Activity') }}</span>
            @endif
            <button type="button" class="btn-edit"
                    onclick="handleEdit('{{ $day }}', {{ $p }}, '{{ $slot['subject_id'] ?? '' }}', '{{ $slot['teacher_id'] ?? '' }}', '{{ $isEvent ? 'event' : 'subject' }}', {{ json_encode($slot['event_name'] ?? '') }})">
                ✏️ {{ __('Edit') }}
            </button>
        </div>
    @else
        <button type="button" class="btn-add" onclick="handleEdit('{{ $day }}', {{ $p }}, '', '', 'subject', '')">+ {{ __('Add') }}</button>
    @endif
</td>
