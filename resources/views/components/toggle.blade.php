@props(['name', 'label'])
<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <label class="group flex gap-2">
        <input type="checkbox" name="{{ $name }}" value="1" hidden>
        <span class="shrink-0 rounded-full bg-black h-[1rem] w-[2rem] p-[2px] inline-block mt-1">
            <span
                class="rounded-full bg-white aspect-square h-full block transform group-has-[input:checked]:left-full group-has-[input:checked]:translate-x-[-100%] relative">
            </span>
        </span>
        <span>{{ $label }}</span>
    </label>
</div>
