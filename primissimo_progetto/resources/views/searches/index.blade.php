
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @if($users!="" && $groups!="")
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><h3>Persone</h3>
                    </div>
                    <div class="panel-body" style="max-height: 400px; overflow: auto;">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        @foreach($users as $u)
                            <div>
                                <div><img style="float: left;" src="http://via.placeholder.com/75x75"></div>
                                <div style="margin-left: 85px;"><h4><b>{{$u->name." ".$u->cognome}}</b></h4></div>
                                <div style="margin-left: 85px;"><h6>{{$u->affiliazione}}</h6></div>
                            <div style="margin-left: 85px;"><h6>{{$u->linea_ricerca}}</h6></div>
                            </div>
                            <hr>
                        @endforeach
                        <h6 style="text-align: center">No more results</h6>
                    </div>
                    <div class="panel-footer" style="background-color: white">
                        <form action="{{ action('SearchController@searchPeople') }}" method="GET">
                            <div class="buttonHolder" style="text-align: center;">
                                <input type="hidden" name="input" value="{{$input}}">
                                <input type="submit" value="View All">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading" ><h3>Gruppi</h3>
                    </div>
                    <div class="panel-body" style="max-height: 400px; overflow: auto;">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        @foreach($groups as $g)
                            <h4><b>{{$g->nomeGruppo}}</b></h4>
                            <h6>{{$g->descrizioneGruppo}}</h6>
                            <hr>
                        @endforeach
                        <h6 style="text-align: center">No more results</h6>
                    </div>
                    <div class="panel-footer" style="background-color: white">
                        <form action="{{ action('SearchController@searchGroups') }}" method="GET">
                            <div class="buttonHolder" style="text-align: center;">
                                <input type="hidden" name="input" value="{{$input}}">
                                <input type="submit" value="View All">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @elseif($users!="")
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading"><h3>Persone</h3>
                    </div>
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        @foreach($users as $u)
                            <div>
                                <div><img style="float: left;" src="http://via.placeholder.com/75x75"></div>
                                <div style="margin-left: 85px;"><h4><b>{{$u->name." ".$u->cognome}}</b></h4></div>
                                <div style="margin-left: 85px;"><h6>{{$u->affiliazione}}</h6></div>
                                <div style="margin-left: 85px;"><h6>{{$u->linea_ricerca}}</h6></div>
                            </div>
                            <hr>
                        @endforeach
                        <h6 style="text-align: center">No more results</h6>
                    </div>
                </div>
            </div>

        @elseif($groups!="")
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default" style="align-self: center">
                    <div class="panel-heading" ><h3>Gruppi</h3>
                    </div>
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        @foreach($groups as $g)
                            <h4><b>{{$g->nomeGruppo}}</b></h4>
                            <h6>{{$g->descrizioneGruppo}}</h6>
                            <hr>
                        @endforeach
                        <h6 style="text-align: center">No more results</h6>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
