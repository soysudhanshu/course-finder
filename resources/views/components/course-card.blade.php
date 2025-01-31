<div class="border shadow-sm hover:shadow hover:bg-gray-50 rounded flex">
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
                    <template x-if="course.is_certified">
                        <x-tag text="Certified" />
                    </template>

                    <template x-if="course.is_free">
                        <x-tag text="Free" />
                    </template>

                    <h3 class="card-title  text-xl font-semibold my-2">
                        <span x-text="course.name"></span>
                    </h3>
                    <template x-if="course.instructor">
                        <p class="text-sm">By <span x-text="course.instructor"></span></p>
                    </template>

                    <template x-if="!course.is_free">
                        <p class="lg:hidden text-lg font-semibold mt-4 ">
                            <span x-text="course.price"></span>
                        </p>
                    </template>

                    <p class="mt-4" x-text="course.description"></p>
                </div>
                <div class="hidden lg:block shrink-0">
                    <template x-if="!course.is_free">
                        <p class="text-lg font-semibold mt-4 ">
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
