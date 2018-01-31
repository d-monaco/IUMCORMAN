@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">
                    titolo sezione
                </div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form>
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th>Publication title</th>
                                    <th>Date of sharing</th>
                                    <th>Type of publication</th>
                                    <th>Publication tags</th>
                                    <th>Coauthors</th>
                                    <th>Condividi</th>
                                    <th>prova</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($suePubblicazioni as $sp)
                                    @foreach($idGruppo as $id)
                                        <tr>
                                            <td>
                                                {{ $sp->titolo }}
                                            </td>
                                            <td>
                                                {{ $sp->dataPubblicazione }}
                                            </td>
                                            <td>
                                                {{ $sp->tipo }}                                                
                                            </td>
                                            <td>
                                                {{ $sp->tags }}
                                            </td>
                                            <td>
                                                {{ $sp->coautori }}
                                            </td>
                                            <td>
                                                <a href="{{route('groups.aggiungi', [$id, $sp->id] )}}"><button type="button" class="btn btn-primary">Add</button></a>
                                            </td>
                                            <td>boh</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
