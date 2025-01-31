<x-layout>
    <x-slot:head>
        <title>Course Finder</title>
    </x-slot:head>

    <div class="bg-blue-100 py-32">
        <div class="container mx-auto">
            <h1 class="text-4xl font-bold text-center">Course Finder</h1>
        </div>
    </div>

    <div class="container mx-auto my-16 md:lg-32 grid grid-cols-[1fr] lg:grid-cols-[4fr_8fr] gap-4 px-4"
        x-data="courseFinder()">
        <div class="lg:hidden">
            <button class="px-4 py-2 hover:bg-black hover:text-white bg-black text-white rounded-lg border-0 "
                x-on:click="showMobileMenu = !showMobileMenu">
                <span class="sr-only">Show filters</span>
                Filters
            </button>
        </div>
        <aside x-show="showMobileMenu" @class([
            'fixed top-0 left-0 w-full h-svh lg:h-[auto] bg-white  overflow-y-auto',
            'lg:static lg:!block',
        ])>
            <form
                action="" id="filter-form" x-ref="form" x-on:submit.prevent.debounce="fetchCourses()"
                x-on:change.debounce="fetchCourses()"
                class="grid gap-4 grid-cols-[1fr] p-4 relative">

                <button
                    x-on:click="showMobileMenu = false"
                    class="lg:hidden absolute text-2xl top-0 right-0 p-4 inline-block"
                    aria-label="Close filter sidebar">
                    &times;
                </button>

                {{-- <div class="p-4"> --}}
                <h2 class="text-2xl font-semibold flex justify-between items-center">
                    Filters
                    <button type="reset"
                        x-on:click="setTimeout(() => fetchCourses(), 0)"
                        @class([
                            'shrink-0 text-sm text-gray-500 font-medium border px-3 py-2',
                            'rounded bg-slate-500 hover:bg-blue-900',
                            'text-white hover:border-blue-900',
                            'mr-7 lg:mr-0',
                        ])>
                        Reset
                    </button>
                </h2>

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
                    @elseif($filter['type'] === 'range')
                        <x-price min-input-name="price_min" max-input-name="price_max" />
                    @endif
                @endforeach
            </form>
        </aside>
        <div class="flex flex-col gap-4">
            <template x-if="courses.length === 0">
                <div class="text-center">
                    <p class="text-2xl font-semibold">No courses found</p>
                </div>
            </template>

            <template x-for="course in courses" :key="course.id">

                <div class="border shadow-sm hover:shadow hover:bg-gray-50 rounded flex">
                    <div class="hidden  max-w-[16rem] shrink-0 grow-0">
                        <img class="w-full h-full object-cover rounded"
                            src="https://images.pexels.com/photos/326055/pexels-photo-326055.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2">
                    </div>
                    <div class="flex flex-col grow">
                        <div class="p-4">
                            <div class="flex justify-between gap-3">
                                <div>
                                    <template x-for="category in course.categories">
                                        <x-tag x-text="category.name" />
                                    </template>
                                    <x-tag x-text="course.difficulty" />
                                    <x-tag x-text="course.format" />
                                    <x-tag x-text="course.popularity" />

                                    <template x-if="course.is_free">
                                        <x-tag text="Free" />
                                    </template>

                                    <h3 class="card-title  text-xl font-semibold my-2">
                                        <span x-text="course.name"></span>
                                    </h3>
                                    <template x-if="course.instructor">
                                        <p class="text-sm">By <span x-text="course.instructor"></span></p>
                                    </template>

                                    <p class="mt-4" x-text="course.description"></p>
                                </div>
                                <div class="shrink-0">
                                    <template x-if="!course.is_free">
                                        <p class="text-lg font-semibold mt-4">
                                            <span x-text="course.price"></span>
                                        </p>
                                    </template>
                                </div>
                            </div>
                            <div class="my-2">
                                <template x-if="course.duration">
                                    <span class="text-sm text-gray-500">
                                        Duration:
                                        <span x-text="course.duration"></span> hours
                                    </span>
                                </template>
                            </div>
                            <div class="mt-4 flex items-center gap-2">
                                <span class="sr-only">Rating: </span>
                                <span class="font-semibold text-yellow-600" x-text="course.rating"></span>
                                <div class="inline-flex items-center">
                                    <template x-for="star in 5">
                                        <svg :class="{
                                            'text-yellow-500': star <= course.rating,
                                            'text-gray-300': star > course
                                                .rating
                                        }"
                                            class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.84-.197-1.54-1.118l1.286-3.957a1 1 0 00-.364-1.118L2.05 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z" />
                                        </svg>
                                    </template>
                                </div>


                            </div>

                        </div>
                    </div>
                </div>
            </template>
            <div class="flex justify-center gap-2 ">
                <template x-for="link in paginationItems" :key="link.id">
                    <label
                        :class="{
                            'bg-blue-900 text-white': link.active,
                            'aspect-square w-[2rem] hidden  lg:flex': link.isNumeric,
                        }"
                        type="submit"
                        @class([
                            'border rounded  justify-center items-center text-center shrink-0 ',
                            ' px-4 py-2 hover:bg-blue-900 hover:text-white cursor-pointer text-lg',
                        ])>

                        <input
                            form="filter-form"
                            type="radio" name="page" x-model="link.active" x-bind:value="link.page"
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
                paginationItems: [],
                paginationNext: null,
                paginationPrev: null,
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
                            this.paginationItems = this.setPaginationItems(data);
                        });
                },
                getFilters() {
                    const form = this.$refs.form;
                    const formData = new FormData(form);

                    return formData;
                },
                setPaginationItems(data) {

                    const maxItems = 3;
                    const totalPages = data.meta.last_page;
                    const currentPage = data.meta.current_page;

                    let lowerLimit = Math.max(1, currentPage - maxItems);
                    const upperLimit = Math.min(totalPages, currentPage + maxItems);

                    if (lowerLimit === upperLimit) {
                        return [];
                    }

                    const paginationItems = [];

                    if (currentPage > 1) {
                        paginationItems.push({
                            page: currentPage - 1,
                            label: 'Previous',
                            active: false,
                            id: this.getRandomId(),
                        });
                    }

                    for (let page = lowerLimit; page <= upperLimit; page++) {
                        paginationItems.push({
                            page,
                            label: page,
                            active: data.meta.current_page === page,
                            id: this.getRandomId(),
                            isNumeric: true,
                        });
                    }

                    if (totalPages > (currentPage + 1)) {
                        paginationItems.push({
                            page: currentPage + 1,
                            label: "Next",
                            active: false,
                            id: this.getRandomId(),
                        });
                    }

                    return paginationItems;
                },

                getRandomId() {
                    return Date.now() + Math.floor(Math.random() * 1000000)
                }

            }

        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>

</x-layout>
