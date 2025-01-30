<x-layout>
    <x-slot:head>
        <title>Course Finder</title>
    </x-slot:head>


    <div class="container mx-auto my-8 grid grid-cols-[1fr] lg:grid-cols-[3fr_9fr] gap-4 px-4" x-data="courseFinder()">
        <div class="lg:hidden">
            <button class="border px-4 py-2 hover:bg-black hover:text-white "
                x-on:click="showMobileMenu = !showMobileMenu">
                <span class="sr-only">Show filters</span>
                Filters
            </button>
        </div>
        <aside x-show="showMobileMenu" @class([
            'fixed top-0 left-0 w-full h-svh bg-white shadow-lg overflow-y-auto',
            'lg:static lg:!block',
        ])>
            <form
                action="" id="filter-form" x-ref="form" x-on:submit.prevent="fetchCourses()"
                x-on:change="fetchCourses()"
                class="grid gap-4 grid-cols-[1fr] bg-gray-100 p-4 relative">

                <button
                    x-on:click="showMobileMenu = false"
                    class="lg:hidden absolute text-2xl top-0 right-0 p-4 inline-block"
                    aria-label="Close filter sidebar">
                    &times;
                </button>

                {{-- <div class="p-4"> --}}
                <h2>Filters</h2>

                @foreach ($filters as $filter)
                    @if (in_array($filter['type'], ['checklist', 'radio']))
                        <x-checklist
                            :type="$filter['type']"
                            :label="$filter['label']"
                            :options="$filter['options']"
                            :name="$filter['name']" />
                    @elseif($filter['type'] === 'search')
                        <x-search
                            :label="$filter['label']"
                            :name="$filter['name']"
                            :placeholder="$filter['placeholder']" />
                    @elseif($filter['type'] === 'toggle')
                        <x-toggle
                            :label="$filter['label']"
                            :name="$filter['name']" />
                    @endif
                @endforeach
            </form>
        </aside>
        <div class="flex flex-col gap-4">

            <template x-for="course in courses" :key="course.id">
                <div class="border shadow-sm rounded flex">
                    <div class="max-w-xs shrink-0 grow-0">
                        <img class="w-full h-full object-cover rounded"
                            src="https://images.pexels.com/photos/326055/pexels-photo-326055.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2">
                    </div>
                    <div class="flex flex-col grow">
                        <div class="p-4">

                            <h3 class="card-title  text-xl font-semibold" x-text="course.name"></h3>
                            <p class="mt-4" x-text="course.description"></p>
                        </div>

                        <div class="mt-auto p-4 border-t">
                            <h4 class="text-lg font-semibold sr-only">Details</h4>
                            <ul class=" flex gap-4">
                                <li>
                                    <strong>Level:</strong>
                                    <span x-text="course.difficulty"></span>
                                </li>
                                <li>
                                    <strong>Duration:</strong>
                                    <span x-text="course.duration"></span> hours
                                </li>
                                <li>
                                    <strong>Format:</strong>
                                    <span x-text="course.format"></span>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </template>
            <div class="flex justify-center gap-2 items-center">

                <template x-for="link in links">
                    <label
                        type="submit"
                        @class([
                            "border rounded aspect-square w-[2rem] justify-center items-center text-center shrink-0 ",
                            "hidden first:flex last:flex lg:flex",
                        ])>
                        <input
                            form="filter-form"
                            type="radio" name="page" x-model="link.active" x-bind:value="link.label"
                            x-on:change="fetchCourses()"
                            class="sr-only">
                        <span x-text="link.label"></span>
                    </label>
                </template>
            </div>
        </div>
    </div>

    <script>
        window.courseFinder = () => {
            return {
                showMobileMenu: false,
                courses: [],
                links: [],
                init() {
                    this.fetchCourses();
                },
                fetchCourses() {
                    const filters = this.getFilters();
                    const url = new URL('/api/courses', window.location.origin);

                    url.search = new URLSearchParams(filters);

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            this.courses = data.data;
                            this.links = data.meta.links;
                        });
                },
                getFilters() {
                    const form = this.$refs.form;
                    const formData = new FormData(form);

                    return formData;
                }
            }
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>

</x-layout>
