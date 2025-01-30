@props(['label', 'name', 'placeholder'])
<label>
    <span class="sr-only">{{ $label }} </span>
    <input type="search" name="{{ $name }}"
        class=" border px-4 py-3 rounded shadow w-full"
        placeholder="{{ $placeholder }}">

</label>
