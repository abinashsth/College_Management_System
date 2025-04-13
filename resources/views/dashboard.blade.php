@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Students Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-blue-500">
        <div class="p-4 flex items-center">
            <div class="rounded-full bg-blue-100 p-3 mr-4">
                <i class="fas fa-user-graduate text-blue-500 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500 uppercase">Total Students</div>
                <div class="text-2xl font-bold">{{ \App\Models\Student::count() }}</div>
                <div class="text-xs text-green-500">
                    <i class="fas fa-arrow-up"></i> 5% from last month
                </div>
            </div>
        </div>
    </div>

    <!-- Classes Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-green-500">
        <div class="p-4 flex items-center">
            <div class="rounded-full bg-green-100 p-3 mr-4">
                <i class="fas fa-chalkboard text-green-500 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500 uppercase">Active Classes</div>
                <div class="text-2xl font-bold">{{ \App\Models\Classes::count() }}</div>
                <div class="text-xs text-green-500">
                    <i class="fas fa-arrow-up"></i> 2% from last month
                </div>
            </div>
        </div>
    </div>

    <!-- Exams Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-purple-500">
        <div class="p-4 flex items-center">
            <div class="rounded-full bg-purple-100 p-3 mr-4">
                <i class="fas fa-file-alt text-purple-500 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500 uppercase">Upcoming Exams</div>
                <div class="text-2xl font-bold">
                    @php
                        try {
                            $upcomingExams = \App\Models\Exam::where('exam_date', '>=', now())->count();
                            echo $upcomingExams;
                        } catch (\Exception $e) {
                            echo '0';
                        }
                    @endphp
                </div>
                <div class="text-xs text-yellow-500">
                    <i class="fas fa-arrow-right"></i> No change from last month
                </div>
            </div>
        </div>
    </div>

    <!-- Users Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-orange-500">
        <div class="p-4 flex items-center">
            <div class="rounded-full bg-orange-100 p-3 mr-4">
                <i class="fas fa-users text-orange-500 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500 uppercase">Total Users</div>
                <div class="text-2xl font-bold">{{ \App\Models\User::count() }}</div>
                <div class="text-xs text-green-500">
                    <i class="fas fa-arrow-up"></i> 3% from last month
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Students -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden col-span-1 lg:col-span-2">
        <div class="border-b px-4 py-3 flex justify-between items-center">
            <h3 class="font-semibold">Recent Students</h3>
            <a href="{{ route('students.index') }}" class="text-blue-500 text-sm hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        try {
                            $recentStudents = \App\Models\Student::latest()->limit(5)->get();
                        } catch (\Exception $e) {
                            $recentStudents = collect();
                        }
                    @endphp
                    @forelse($recentStudents as $student)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=random" alt="{{ $student->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->student_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @php
                                try {
                                    echo $student->class ? $student->class->class_name : 'N/A';
                                } catch (\Exception $e) {
                                    echo 'N/A';
                                }
                            @endphp
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No students found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upcoming Exams -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b px-4 py-3 flex justify-between items-center">
            <h3 class="font-semibold">Upcoming Exams</h3>
            <a href="{{ route('exams.index') }}" class="text-blue-500 text-sm hover:underline">View All</a>
        </div>
        <div class="p-4">
            @php
                try {
                    $upcomingExams = \App\Models\Exam::where('exam_date', '>=', now())->orderBy('exam_date')->limit(5)->get();
                } catch (\Exception $e) {
                    $upcomingExams = collect();
                }
            @endphp
            @forelse($upcomingExams as $exam)
            <div class="mb-4 border-b pb-4 last:border-0 last:pb-0">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $exam->title }}</h4>
                        <div class="text-sm text-gray-500">
                            @php
                                try {
                                    echo $exam->subject ? $exam->subject->name : 'N/A';
                                } catch (\Exception $e) {
                                    echo 'N/A';
                                }
                            @endphp
                        </div>
                    </div>
                    <div class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $exam->exam_type }}</div>
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    {{ \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') }}
                </div>
                <div class="mt-1 flex items-center text-sm text-gray-500">
                    <i class="fas fa-clock mr-2"></i>
                    {{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('h:i A') : 'N/A' }} 
                    - 
                    {{ $exam->end_time ? \Carbon\Carbon::parse($exam->end_time)->format('h:i A') : 'N/A' }}
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 py-4">No upcoming exams</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
