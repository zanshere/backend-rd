@props(['name', 'class' => 'w-4 h-4'])

@php
    $icons = [
        'star' => 'star',
        'trophy' => 'trophy',
        'crown' => 'crown',
        'check' => 'check',
        'lock-closed' => 'lock',
        'bookmark' => 'bookmark',
        'phone' => 'phone',
        'chat-bubble-left-right' => 'message-circle',
        'envelope' => 'mail',
        'support' => 'phone',
        'alert-circle' => 'alert-circle',
        'check-circle' => 'check-circle-2',
        'x' => 'x',
        'loader' => 'loader-2'
    ];

    $lucideName = $icons[$name] ?? $name;
@endphp

<i
    data-lucide="{{ $lucideName }}"
    class="lucide lucide-{{ $lucideName }} {{ $class }}"
    {{ $attributes }}
></i>
