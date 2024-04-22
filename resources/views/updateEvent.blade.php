@extends('layouts.app')

@section('content')
    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h1 class="card-title">Modifier événement </h1>

                <form action="{{route('events.update', ['id' => $event['id']])}}" method="POST">
                    @csrf
                    @method('PUT')
                    <label for="name">Titre</label>
                    <textarea name="name" id="" cols="60" rows="3">{{ $event['summary'] }}</textarea>
                    <br>
                    <label for="start">Insérez une date de début d'événement</label>
                    <input type="date" name="start_date" value="{{ date('Y-m-d', strtotime($event['start'])) }}">
                    <input type="time" name="start_time" value="{{ date('H:i', strtotime($event['start'])) }}">
                    <br>
                    <label for="end">Insérez une date de fin d'événement</label>
                    <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime($event['end'])) }}">
                    <input type="time" name="end_time" value="{{ date('H:i', strtotime($event['end'])) }}">
                    <br>
                    <input type="submit" value="Mettre à jour">
                </form>
                <form action="{{route('events.delete', ['id' => $event['id']])}}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="submit" value="Supprimer">
                </form>
            </div>
        </div>
    </div>
@endsection
