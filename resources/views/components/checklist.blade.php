@props(['label', 'options', 'name', 'type'])

@php
    if ($type === 'checklist') {
        $name = $name . '[]';
    }

    $type = $type === 'checklist' ? 'checkbox' : 'radio';

@endphp

<fieldset class="border-b border-gray-200 p-4 border shadow-sm rounded bg-white ">
    <legend class="text-lg float-left">{{ $label }}</legend>
    <div class="max-h-[200px] overflow-y-auto  clear-both ">
        @foreach ($options as $option)
            <label class="flex gap-2 ">
                <span>
                    <input type="{{ $type }}" name="{{ $name }}"
                        value="{{ $option['value'] }}">
                </span>
                <span>{{ $option['label'] }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
