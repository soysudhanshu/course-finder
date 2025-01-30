@props(['minInputName', 'maxInputName', 'min' => 0, 'max' => 10000])
<fieldset class="wrapper-range border border-gray-300 p-4 rounded-lg bg-white grid gap-4" x-data="{ enablePriceRange: !$el.querySelector('[name=free_courses_only]').checked }">
    <legend class="float-left text-xl">Price</legend>

    <x-toggle name="free_courses_only" label="Show free only courses"
        x-on:change="enablePriceRange = !$event.target.checked" />

    <div x-show="enablePriceRange" class="grid gap-4 items-center">
        <div class="flex justify-between">
            <span class="" data-min-display></span>
            <span class="" data-max-display></span>
        </div>
        <div class="jq-range-slider"></div>
        <input type="text" x-ref="min" data-min name="{{ $minInputName }}" readonly value="{{ $min }}"
            class="hidden">
        <input type="text" x-ref="max" data-max name="{{ $maxInputName }}" readonly value="{{ $max }}"
            class="hidden">
    </div>


</fieldset>

@once
    <style>
        .jq-range-slider {
            height: 10px;
        }

        .jq-range-slider .ui-slider-range {
            background: dodgerblue;
        }

        .jq-range-slider .ui-slider-handle {
            border-radius: 100%;
            background: white;
            border: 4px solid dodgerblue
        }


        .jq-range-slider .ui-state-hover,
        .jq-range-slider .ui-state-focus {
            box-shadow: 0 0 0 10px #1e90ff26;
        }
    </style>
    <script>
        window.addEventListener('load', function() {

            const sliders = $(".wrapper-range");
            sliders.each(function() {
                const slider = $(this).find('.jq-range-slider');

                const formatter = new Intl.NumberFormat('en-GB', {
                    style: 'currency',
                    currency: 'GBP',
                    minimumFractionDigits: 0
                });

                const minDisplay = $(this).find('[data-min-display]');
                const maxDisplay = $(this).find('[data-max-display]');

                const minInput = $(this).find('input[data-min]');
                const maxInput = $(this).find('input[data-max]');

                slider.slider({
                    range: true,
                    min: {{ $min }},
                    max: {{ $max }},
                    values: [minInput.val() || {{ $min }}, maxInput.val() ||
                        {{ $max }}
                    ],
                    slide: function(event, ui) {
                        minInput.val(ui.values[0]);
                        maxInput.val(ui.values[1]);

                        minDisplay.text("Min: " + formatter.format(ui.values[0]));
                        maxDisplay.text("Max: " + formatter.format(ui.values[1]));

                        minInput.get(0).dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });

                minInput.val(slider.slider("values", 0));
                maxInput.val(slider.slider("values", 1));

                minDisplay.text("Min: " + formatter.format(slider.slider("values", 0)));
                maxDisplay.text("Max: " + formatter.format(slider.slider("values", 1)));

                minInput.change();
                maxInput.change();
            });
        })
    </script>
@endonce
