<x-popend name="show_course">
    <div class="flex flex-col justify-between">
        <div class="flex-1 overflow-y-auto">
            <div class="h-32 grid grid-cols-3 gap-2 border-slate-300 shrink-0 overflow-clip rounded-md mx-2.5 bg-white dark:bg-zinc-950">
                <img class="col-span-1 h-full object-cover" src="{{ asset('svg/course_image_default.svg') }}"
                    alt="default_course_img">

                <div class="col-span-2 flex flex-col justify-center">
                    <p class="text-lg font-semibold text-body-800 select-none whitespace-nowrap text-ellipsis overflow-hidden"
                        ng-bind="show_course.name">
                    </p>
                    <p class="flex items-center select-none">
                        <span class="text-sm text-body-400 pr-2 border-r border-r-slate-[var(--body-300)]"
                            ng-bind="show_course.code">
                        </span>
                        <span class="text-sm text-body-300 pl-2 border-l border-l-slate-[var(--body-300)]">
                            <span ng-bind="show_course.units"></span> units
                        </span>
                    </p>
                </div>
            </div>

            <div class="h-32 p-2 flex flex-col gap-2 shrink-0">
                <p class="text-sm font-semibold text-body-300">Marks distribution</p>
                
                <div ng-cloak ng-show="show_course.has_practical != 0" class="flex flex-col gap-3">
                    <div class="grid grid-cols-5">
                        <span class="col-span-1 p-3 flex center font-bold bg-orange-200 dark:bg-orange-900">
                            20%
                        </span>
                        <span class="col-span-1 p-3 flex center font-bold bg-blue-200">
                            20%
                        </span>
                        <span
                            class="col-span-3 p-3 flex center font-bold bg-green-200 dark:bg-green-800 rounded rounded-r-full">
                            60%
                        </span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1 font-semibold text-body-500 text-sm">
                            <div class="w-3 h-3 bg-orange-200 dark:bg-orange-900 rounded-full">
                            </div>
                            test
                        </div>
                        <div class="flex items-center gap-1 font-semibold text-body-500 text-sm">
                            <div class="w-3 h-3 bg-blue-200 rounded-full"></div>
                            practical
                        </div>
                        <div class="flex items-center gap-1 font-semibold text-body-500 text-sm">
                            <div class="w-3 h-3 bg-green-200 rounded-full"></div>
                            exam
                        </div>
                    </div>
                </div>
                
                <div ng-cloak ng-show="show_course.has_practical == 0" class="flex-col gap-3">
                    <div class="grid grid-cols-10">
                        <span class="col-span-3 p-3 flex center font-bold bg-orange-200 dark:bg-orange-900">30%</span>
                        <span
                            class="col-span-7 p-3 flex center font-bold bg-green-200 dark:bg-green-800 rounded-r-full">70%</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1 font-semibold text-body-500 text-sm">
                            <div class="w-3 h-3 bg-accent-200 rounded-full"></div>
                            test
                        </div>
                        <div class="flex items-center gap-1 font-semibold text-body-500 text-sm">
                            <div class="w-3 h-3 bg-green-200 rounded-full"></div>
                            exam
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="p-2 flex flex-col gap-2 shrink-0">
                <p class="text-sm font-semibold text-body-300">Course Description</p>
                <div class=" rounded overflow-y-auto p-1 text-sm text-body-500" ng-bind="show_course.outline">

                </div>
            </div>

        </div>
        <div>
            <button type="button" ng-click="editCourse(show_course)" class="btn btn-primary">Edit Course Details</button>
        </div>
    </div>
</x-popend>
