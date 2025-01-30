<x-layout>
    <x-slot:head>
        <title>Course Finder</title>
    </x-slot:head>


    <div class="container mx-auto my-8 grid grid-cols-[3fr_9fr] gap-4" x-data="courseFinder()">
        <aside>
            <form action="" x-ref="form" x-on:submit.prevent="fetchCourses()" x-on:change="fetchCourses()"
                class="grid gap-4 grid-cols-[1fr] bg-gray-100 p-4">
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
                    <div class="p-4">
                        <h3 class="card-title  text-xl font-semibold" x-text="course.name"></h3>
                        <p class="mt-4" x-text="course.description"></p>

                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
        window.courseFinder = () => {
            return {
                courses: [],
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
