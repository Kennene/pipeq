<!DOCTYPE html>
<html lang="pl">

<head>
    @extends('layouts.head')
    @vite(['resources/css/administrator.css'])
    <title>Administrator</title>
</head>

<body>

    @include('topbar')

    <div class="row g-2 mx-2 my-2">
  
        <div class="col-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-center h2">{{ __("administrator.schedules") }}</h5>
                    <form method="POST" action="">
                        @csrf
                    
                        <table class="form-table table table-sm">

                            @foreach($destinations as $destination)
                                <tr><td colspan="4" class="fw-bold text-center h3">{{ $destination->name }}</td></tr>
                                <tr>
                                    <th scope="col">{{ __("administrator.day") }}</th>
                                    <th scope="col">{{ __("administrator.is_closed") }}</th>
                                    <th scope="col">{{ __("administrator.open_time") }}</th>
                                    <th scope="col">{{ __("administrator.close_time") }}</th>
                                </tr>

                                @foreach($destination->schedules as $schedule)
                                    <tr>
                                        <td><input type="text" class="form-control text-muted text-right" value="{{ __("day.".$schedule->day_of_week) }}" readonly></td>
                                        <td class="text-center"><input class="form-check-input" type="checkbox" {{ $schedule->checked() }}></td>
                                        <td> <input type="time" class="form-control" value="{{ $schedule->open_time }}"></td>
                                        <td> <input type="time" class="form-control" value="{{ $schedule->close_time }}"></td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>

                        <div class="mt-3 bg-info">
                            <button type="submit" class="btn btn-primary btn float-right">Save</button>
                            <button type="reset" class="btn btn-secondary btn float-right mr-2">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-center h2">{{ __("administrator.users") }}</h5>
                    <form method="POST" action="">
                        @csrf

                        <table class="form-table table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __("administrator.user") }}</th>
                                    <th scope="col">{{ __("administrator.role_name") }}</th>
                                    <th scope="col">{{ __("administrator.role_id") }}</th>
                                    <th scope="col">{{ __("administrator.role_description") }}</th>
                                </tr>
                            </thead>
                        
                            @foreach($users as $user)
                                <tr>
                                    <td><input type="text" class="form-control text-right" value="{{ $user->user_name }}"></td>
                                    <td>
                                        <select class="form-control text-left">
                                            @foreach($roles as $role)
                                                @if($user->role_id == $role->id)
                                                    <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
                                                @else
                                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control text-muted" value="{{ $user->role_id }}" readonly></td>
                                    <td><textarea type="text" class="form-control text-muted" readonly>{{ $user->description }}</textarea></td>
                                </tr>
                            @endforeach
                        </table>

                        <div class="mt-3 bg-info">
                            <button type="submit" class="btn btn-primary btn float-right">Save</button>
                            <button type="reset" class="btn btn-secondary btn float-right mr-2">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-center h2">{{ __("administrator.tickets_history") }}</h5>
                    <table class="form-table table table-bordered table-striped table-hover table-sm caption-top">
                        <caption>{{ __("administrator.tickets_order") }}</caption>
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">ticket_id</th>
                                <th scope="col">ticket_nr</th>
                                <th scope="col">Status</th>
                                <th scope="col">Destination</th>
                                <th scope="col">Workstation</th>
                                <th scope="col">{{ __("administrator.created") }}</th>
                                <th scope="col">{{ __("administrator.updated_when") }}</th>
                                <th scope="col">{{ __("administrator.user") }}</th>
                            </tr>
                        </thead>
                        @foreach($tickets_history as $log)
                            <tr>
                                @foreach($log as $key => $value)
                                    <td>{{ $value }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>

</html>