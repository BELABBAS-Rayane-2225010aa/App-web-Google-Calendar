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
                <h1 class="card-title">Ajouter événement</h1>

                <form action="{{route('booking.store')}}" method="POST">
                    @csrf
                    <label for="name">Titre</label>
                    <textarea name="name" id="" cols="60" rows="3" required></textarea>
                    <br>
                    <label for="start">Insérez une date de debut d'événement</label>
                    <input type="date" name="start_date" required>
                    <input type="time" name="start_time" required>
                    <br>
                    <label for="start">Insérez une date de fin d'événement</label>
                    <input type="date" name="end_date" >
                    <input type="time" name="end_time">
                    <br>
                    <input type="submit" value="Envoyer">
                </form>
            </div>
        </div>
    </div>
@endsection
