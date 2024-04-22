@extends('layouts.app')

@section('content')
    <div class="container">
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
        <h1>Liste de tout les événements</h1>
        @foreach($events as $event)
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">
                        <a href="{{ route('events.update', ['id' => $event['id']]) }}">
                            {{ $event['summary'] }}
                        </a>
                    </h2>
                    <p>Start time: {{ $event['start'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
@endsection
