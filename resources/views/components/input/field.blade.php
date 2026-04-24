@props([
    'label' => false,
    'id' => false,
    'name' => '',
    'type' => 'text',
    'value' => false,
    'placeholder' => false,
    'labelClass' => 'text-gray-900',
    'inputClass' => 'text-gray-900 bg-primary-50',
    'required' => false,
    'autofocus' => false,
    'autocomplete' => false,
    'maxWidth' => 'max-w-xs',
    'disabled' => false,
])

@php
    $required = $required ? 'required' : '';
    $autofocus = $autofocus ? 'autofocus' : '';
    $value = $value ? 'value="' . $value . '"' : '';
    $autocomplete = $autocomplete ? 'autocomplete="' . $autocomplete . '"' : '';
    $disabled = $disabled ? 'disabled' : '';
@endphp

<div {{ $attributes->merge(['class' => 'space-y-1 ' . $maxWidth]) }}>
    @if($label)
        <label class="block text-sm font-medium text-neutral-700" for="{{$id}}">
            {{ $label }}
        </label>
    @endif
    <input
        type="{{$type}}"
        class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-neutral-500 focus:outline-none"
        placeholder="{{$placeholder}}"
        name="{{$name}}"
        {{$required}}
        {{$autofocus}}
        {!! $value !!}
        {!! $autocomplete !!}
        {{$disabled}}
        id="{{$id}}"
    />
</div>
