@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Attività recenti</div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @foreach($publications as $p)
                        <h1>{{ $p->titolo }}</h2><br>
                        <h2>{{ $p->descrizione}}</h1>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
