<?php

namespace App\Http\Controllers;

use App\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class PublicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id=Auth::id();
        $publications = DB::table('publications')
            ->select('id', 'titolo', 'dataPubblicazione', 'pdf', 'immagine', 'multimedia', 'tipo', 'tags', 'coautori')
            ->where('idUser', '=', $id)->get();
        return view ('publications.index', ['publications' => $publications]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('publications.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $val = $request->validate([
            'titolo' => 'required|max:255',
            'dataPubblicazione' => '',
            'pdf' => 'mimes:application/pdf, application/x-pdf,application/acrobat, applications/vnd.pdf, text/pdf, text/x-pdf|max:10000',
            'immagine' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'multimedia' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tipo' => 'required',
            'visibilita' => '',
            'tags' => 'max:255',
            'coautori' => 'max:255',
            'idUser' => ''
        ]);
        Publication::create([
            'titolo' => $request['titolo'],
            'dataPubblicazione' => date('Y-m-d H:i:s'),
            'pdf' => '',
            'immagine' => '',
            'multimedia' => '',
            'tipo' => $request['tipo'],
            'visibilita' => $request['visibilita'],
            'tags' => $request['tags'],
            'coautori' => $request['coautori'],
            'idUser' => Auth::id()
        ]);
        /*$publication=new Publication;
        $publication->titolo = $request->get('title');
        $publication->dataPubblicazione = $request->timestamps();
        if(Input::hasFile('pdf'))
            $publication->pdf = Input::file('pdf');
        if(Input::hasFile('immagine'))
            $publication->immagine = Input::file('immagine');
        if(Input::hasFile('multimedia'))
            $publication->multimedia = Input::file('multimedia');
        $publication->tipo = $request->get('tipo');
        $publication->idUser = Auth::id();
        $publication->save();*/
        return redirect('/home/user');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $publications = DB::table('publications')
            ->select('id', 'titolo', 'dataPubblicazione', 'pdf', 'immagine', 'multimedia', 'tipo', 'visibilita', 'tags', 'coautori', 'idUser')
            ->where('id', '=', $id)
            ->where('idUser', '=', Auth::id())->get();
        return view('publications.edit', ['publications' => $publications]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idPublication)
    {
        $publication=Publication::find($idPublication);
        $this->validate($request, [
            'titolo' => 'required|max:255',
            'dataPubblicazione' => '',
            'pdf' => 'mimes:application/pdf, application/x-pdf,application/acrobat, applications/vnd.pdf, text/pdf, text/x-pdf|max:10000',
            'immagine' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'multimedia' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tipo' => 'required',

            'visibilita' => '',
            'tags' => 'required',
            'coautori' => 'required',
            'idUser' => ''
        ]);

        $publication->titolo = $request->get('titolo');
        $publication->dataPubblicazione = date('Y-m-d H:i:s');
        $publication->pdf = null;
        $publication->immagine = null;
        $publication->multimedia = null;
        $publication->tipo = $request->get('tipo');
        $publication->tags = $request->get('tags');
        $publication->coautori = $request->get('coautori');
        $publication->idUser = Auth::id();
        $publication->save();
        return redirect('/home/user');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
