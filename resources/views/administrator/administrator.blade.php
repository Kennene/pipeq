<!DOCTYPE html>
<html lang="pl">

<head>
    @extends('layouts.head')
    @vite(['resources/css/administrator.css'])
    <title>Administrator</title>
</head>

<body>
    @include('topbar')

    <div class="container-fluid">

        <!-- Destinations schedules editor -->
        <div class="row">
            <div class="col-12 mt-4">
                <h5 class="card-title fw-bold h2">{{ __("administrator.schedules") }}</h5>
            </div>
        </div>

        <div class="row">
            @foreach($destinations as $destination)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="h3">{{ $destination->name }}</span>

                        <form method="POST" action="{{ route('_updateSchedules') }}">
                            @csrf

                            <table class="form-table table table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __("administrator.day") }}</th>
                                        <th scope="col">{{ __("administrator.is_closed") }}</th>
                                        <th scope="col">{{ __("administrator.open_time") }}</th>
                                        <th scope="col">{{ __("administrator.close_time") }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($destination->schedules as $schedule)
                                    <tr>
                                        <!-- Day of week, read-only -->
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control text-muted text-right"
                                                value="{{ $schedule->name }}"
                                                readonly>
                                        </td>

                                        <!-- is_closed: hidden field + checkbox -->
                                        <td>
                                            <!-- hidden field ensures '0' if checkbox is not checked -->
                                            <input
                                                type="hidden"
                                                name="schedules[{{ $schedule->id }}][is_closed]"
                                                value="0">
                                            <input
                                                type="checkbox"
                                                class="form-check-input"
                                                name="schedules[{{ $schedule->id }}][is_closed]"
                                                value="1"
                                                {{ $schedule->checked() }}>
                                        </td>

                                        <!-- open_time -->
                                        <td>
                                            <input
                                                type="time"
                                                class="form-control"
                                                name="schedules[{{ $schedule->id }}][open_time]"
                                                value="{{ $schedule->open_time }}">
                                        </td>

                                        <!-- close_time -->
                                        <td>
                                            <input
                                                type="time"
                                                class="form-control"
                                                name="schedules[{{ $schedule->id }}][close_time]"
                                                value="{{ $schedule->close_time }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="text-right">
                                <button type="reset" class="btn btn-secondary">
                                    {{ __("administrator.clear") }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ __("administrator.submit") }}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            @endforeach
        </div>


        <div class="col-9 mt-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-left h2">{{ __('administrator.translations.title') }}</h5>
                    
                    <form method="POST" action="{{ route('_updateLanguages') }}">
                        @csrf

                        <div class="overflow-auto border-bottom" style="max-height: 50vh;">
                            <table class="form-table table table-bordered table-striped table-hover table-sm caption-top">
                                <thead class="thead-light sticky-header">
                                    <tr>
                                        <th scope="col">{{ __('administrator.translations.key') }}</th>
                                        @foreach(config('app.available_locales') as $locale => $language)
                                        <th scope="col" class="text-center">{{ $language }}</th>
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($translations as $key => $translation)
                                        <tr>
                                            <td class="text-muted text-sm">{{ $key }}</td>
                                            @foreach(config('app.available_locales') as $locale => $language)
                                                 <td>
                                                    <input
                                                        type="text"
                                                        class="form-control text-center"
                                                        value="{{ $translation[$locale] ?? '' }}"
                                                        name="translations[{{ $key }}][{{ $locale }}]"
                                                    >
                                                 </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-right mt-2">
                            <button type="reset" class="btn btn-secondary">
                                {{ __("administrator.clear") }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ __("administrator.submit") }}
                            </button>
                        </div>

                    </form>
                    
                    
                </div>
            </div>
        </div>


        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-left h2">{{ __("administrator.tickets_history") }}</h5>
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