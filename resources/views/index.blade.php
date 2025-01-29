<x-layout>
    <x-slot:head>
        <title>Course Finder</title>
    </x-slot:head>


    <div class="container mx-auto my-8 grid grid-cols-[3fr_9fr] gap-4" x-data="courseFinder()">
        <aside>
            <form action="" x-ref="form" x-on:submit.prevent="fetchCourses()">
                {{-- <div class="p-4"> --}}
                <h2>Filters</h2>
                <label>

                    <span class="sr-only">Search </span>
                    <input type="search" name="search"
                        class=" border px-4 py-3 rounded shadow w-full"
                        placeholder="Search for courses">

                </label>

                {{-- </div> --}}
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

                    for (const key in filters) {
                        url.searchParams.append(key, filters[key]);
                    }

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            this.courses = data.data;
                        });
                },
                getFilters() {
                    const form = this.$refs.form;
                    const formData = new FormData(form);

                    return Object.fromEntries(formData);
                }
            }
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>

</x-layout>
