@php
    $isALevel = $isALevel ?? false;
    $isSpecial = false;
    $specialTitle = null;
    $specialIcon = null;
    $specialBg = '#f0fdf4';
    $specialBorder = '#bbf7d0';
    $specialColor = '#166534';

    if ($isALevel) {
        if ($day === 'Wednesday' && in_array($p, [8, 9])) {
            $isSpecial = true;
            $specialTitle = 'GS Seminar / Symposium (13:00 - 14:20)';
            $specialIcon = '🎓';
            $specialBg = '#faf5ff';
            $specialBorder = '#d8b4fe';
            $specialColor = '#6b21a8';
        } elseif ($day === 'Thursday' && in_array($p, [8, 9])) {
            $isSpecial = true;
            $specialTitle = 'Research & Subject Club (13:00 - 14:20)';
            $specialIcon = '🗣️';
            $specialBg = '#fffbeb';
            $specialBorder = '#fde68a';
            $specialColor = '#b45309';
        } elseif ($day === 'Friday' && in_array($p, [6, 7])) {
            $isSpecial = true;
            $specialTitle = 'Science / Lab Practicals (11:40 - 13:00)';
            $specialIcon = '🔬';
            $specialBg = '#eff6ff';
            $specialBorder = '#bfdbfe';
            $specialColor = '#1e40af';
        } elseif ($day === 'Friday' && in_array($p, [8, 9])) {
            $isSpecial = true;
            $specialTitle = 'Sports & Health (13:00 - 14:20)';
            $specialIcon = '⚽';
            $specialBg = '#ecfdf5';
            $specialBorder = '#a7f3d0';
            $specialColor = '#047857';
        } elseif ($p === 10) {
            $isSpecial = true;
            $specialTitle = 'Advance Discussion & Tests (15:00 - 17:00)';
            $specialIcon = '📝';
            $specialBg = '#eef2ff';
            $specialBorder = '#c7d2fe';
            $specialColor = '#3730a3';
        }
    } else {
        if ($day === 'Wednesday' && in_array($p, [8, 9])) {
            $isSpecial = true;
            $specialTitle = 'Religion (13:00 - 14:20)';
            $specialIcon = '📖';
            $specialBg = '#faf5ff';
            $specialBorder = '#d8b4fe';
            $specialColor = '#6b21a8';
        } elseif ($day === 'Thursday' && in_array($p, [8, 9])) {
            $isSpecial = true;
            $specialTitle = 'Debate / Club (13:00 - 14:20)';
            $specialIcon = '🗣️';
            $specialBg = '#fffbeb';
            $specialBorder = '#fde68a';
            $specialColor = '#b45309';
        } elseif ($day === 'Friday' && in_array($p, [6, 7, 8, 9])) {
            $isSpecial = true;
            $specialTitle = 'Sports & Games (11:40 - 14:20)';
            $specialIcon = '⚽';
            $specialBg = '#ecfdf5';
            $specialBorder = '#a7f3d0';
            $specialColor = '#047857';
        } elseif ($p === 10) {
            $isSpecial = true;
            $specialTitle = 'Discussion & Examinations (15:00 - 17:00)';
            $specialIcon = '📝';
            $specialBg = '#eef2ff';
            $specialBorder = '#c7d2fe';
            $specialColor = '#3730a3';
        }
    }
@endphp

<td>
    @if(isset($timetableMatrix[$day][$p]))
        @php
            $slot = $timetableMatrix[$day][$p];
            $subLower = strtolower($slot['subject'] ?? '');
            $cellBg = $specialBg;
            $cellBorder = $specialBorder;
            $cellColor = $specialColor;

            if (str_contains($subLower, 'religion') || str_contains($subLower, 'dini')) {
                $cellBg = '#faf5ff'; $cellBorder = '#d8b4fe'; $cellColor = '#6b21a8';
            } elseif (str_contains($subLower, 'debate') || str_contains($subLower, 'club') || str_contains($subLower, 'mjadala')) {
                $cellBg = '#fffbeb'; $cellBorder = '#fde68a'; $cellColor = '#b45309';
            } elseif (str_contains($subLower, 'sport') || str_contains($subLower, 'michezo')) {
                $cellBg = '#ecfdf5'; $cellBorder = '#a7f3d0'; $cellColor = '#047857';
            } elseif (str_contains($subLower, 'discussion') || str_contains($subLower, 'examination') || str_contains($subLower, 'majadiliano')) {
                $cellBg = '#eef2ff'; $cellBorder = '#c7d2fe'; $cellColor = '#3730a3';
            } elseif (str_contains($subLower, 'general studies') || str_contains($subLower, 'seminar')) {
                $cellBg = '#f5f3ff'; $cellBorder = '#c4b5fd'; $cellColor = '#5b21b6';
            } elseif (str_contains($subLower, 'practical') || str_contains($subLower, 'laboratory') || str_contains($subLower, 'research')) {
                $cellBg = '#eff6ff'; $cellBorder = '#93c5fd'; $cellColor = '#1e40af';
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
                @if($isSpecial){{ $specialIcon }} @endif{{ __($slot['subject']) }}
            </div>
            <span class="slot-teacher">👤 {{ $slot['teacher'] }}</span>
            <button type="button" class="btn-edit" onclick="handleEdit('{{ $day }}', {{ $p }}, {{ $slot['subject_id'] }}, {{ $slot['teacher_id'] }})">✏️ {{ __('Edit') }}</button>
        </div>
    @elseif($isSpecial)
        <div class="slot-box" style="background: {{ $specialBg }}; border-color: {{ $specialBorder }}; border-style: dashed;">
            <div class="slot-subject" style="color: {{ $specialColor }}; font-size: 11px;">
                {{ $specialIcon }} {{ __($specialTitle) }}
            </div>
            <span class="slot-teacher" style="color: {{ $specialColor }}; font-style: italic; font-size: 10px;">{{ __('School Routine') }}</span>
            <button type="button" class="btn-edit" style="background-color: {{ $specialColor }};" onclick="handleEdit('{{ $day }}', {{ $p }}, '', '')">+ {{ __('Assign') }}</button>
        </div>
    @else
        <button type="button" class="btn-add" onclick="handleEdit('{{ $day }}', {{ $p }}, '', '')">+ {{ __('Add') }}</button>
    @endif
</td>
