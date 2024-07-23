@php 
 $levels = \App\Models\Student::where('level', '>', 0)
    ->where('level', '<', 600)
    ->get('level')
    ->groupBy('level');
    
$students_levels = [
    '100 LEVEL' => 0,
    '200 LEVEL' => 0,
    '300 LEVEL' => 0,
    '400 LEVEL' => 0,
    '500 LEVEL' => 0
];

foreach($levels as $level => $students) {

    $students_levels["$level LEVEL"] = count($students);
}

$statistics = json_encode($students_levels);

    $activityLogs = \App\Models\ActivityLog::orderBy('created_at', 'desc')->with('user')->paginate(7);
    $countLog = count($activityLogs);
@endphp
<x-template title="Admin Dashboard" nav="home">



    <main class="columns">



        <section class="half-60">


            <div class="card">
                <div clas="card-header">
                    <div class="card-title">
                        Dashboard
                    </div>
                </div>


                <div class="card-body flex flex-col gap-5">

                    <section class="grid sm:grid-cols-2 grid-rows-1 gap-4">


                        <div class="panel rounded-lg">
                            <div class="panel-header">
                                <div class="md:flexx md:flex-col md:items-center md:gap-2">
                                    <img src="{{ asset($user->image) }}" gender="{{$user->gender}}" alt="advisor"
                                        class="aspect-square w-12 rounded-full object-cover">
                                    <div>
                                        <p class="font-semibold text-md lg:text-xl">{{ $user->name }}</p>
                                        <p class="font-medium text-red-500 text-pretty flex items-center">
                                            <x-icon name="star"/>
                                            <span>ADMINISTRATOR</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="panel-icons"></div>
                            </div>
                        </div>


                        <div
                            class="blue-card">
                            <header class="w-full flex items-center justify-between">
                                <p class="font-medium uppercase">students</p>
                                <x-icon name="groups"/>
                            </header>
                            <div>
                                <h1 class="font-medium text-4xl" ng-bind="config.count.students||0"></h1>
                                <h1 class="font-medium text-4xl"></h1>
                            </div>
                        </div>



                        <div
                            class="red-card">
                            <header class="w-full flex items-center justify-between">
                                <p class="font-medium uppercase">staff</p>
                                <x-icon name="work"/>
                            </header>
                            <div>
                                <h1 class="font-medium text-4xl" ng-bind="config.count.staffs||0"></h1>
                                <h1 class="font-medium text-4xl"></h1>
                            </div>
                        </div>
                        <div
                            class="purple-card">
                            <header class="w-full flex items-center justify-between">
                                <p class="font-medium uppercase">advisors</p>
                                <x-icon name="group"/>
                            </header>
                            <div>
                                <h1 class="font-medium text-4xl" ng-bind="config.count.advisors||0"></h1>
                                <h1 class="font-medium text-4xl"></h1>
                            </div>
                        </div>

                        <div
                            class="yellow-card">
                            <header class="w-full flex items-center justify-between">
                                <p class="font-medium uppercase">courses</p>
                                <x-icon name="book_4"/>
                            </header>
                            <div>
                                <h1 class="font-medium text-4xl" ng-bind="config.count.courses||0"></h1>
                                <h1 class="font-medium text-4xl"></h1>
                            </div>
                        </div>
                        <div
                            class="primary-card">
                            <header class="w-full flex items-center justify-between">
                                <p class="font-medium uppercase" ng-bind="config.active_session.name"></p>
                                <x-icon name="book_3"/>
                            </header>
                            <div>
                                <h1 class="font-medium text-2xl lg:text-4xl" ng-bind="config.active_session.active_semester"></h1>
                                <h1 class="font-medium text-4xl"></h1>
                            </div>
                        </div>
                    </section>



                    <div>
                        <div class="card-header">
                            <div class="card-title">Student Survey</div>
                        </div>
                        <div class="card-body min-h-[365px]">
                            <canvas data-label="Student Survey" class="flex-1 object-fit" id="barChart" width="400"
                                height="400"></canvas>
                        </div>
                    </div>
                </div>

            </div>

        </section>

        <section>
            <div class="card">

                <div class="card-body flex flex-col gap-4">
                    <x-calendar />




                   
                    <div class="card">
                        <div class="card-header">Log Activities</div>
                        <div class="card-body italic flex flex-col gap-3 min-h-[291px]">

                            @forelse ($activityLogs as $log)
                              
                                <div>
                                    <b>{{ $log->user->id === auth()->user()->id ? 'You' : $log->user->name }}</b>
                                    {{ $log->description }} <i class="fa fa-clock opacity-45"></i> <span
                                        class="text-slate-400">{{ timeago($log->created_at) }}</span>
                                </div>
                              
                            @empty
                                <p>No activity logs found.</p>
                            @endforelse
                        </div>
                    </div>

                    <x-todo />

                </div>



            </div>
        </section>

    </main>











    <script src="{{ asset('js/jchart.js') }}"></script>
    <script>
        chart('#barChart', {! $statistics !}, 'bar');
       
    </script>
</x-template>
