<?php

namespace App\Http\Controllers;

use App\PaperSearcher;
use App\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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
            'descrizione' => 'max:255',
            'coautori' => 'max:255',
            'idUser' => ''
        ]);
        if($request['pdf']!=null) {
        /*if($request->hasFile('pdf')) {
            $file=$request->file('pdf');
            $extension=$file->getClientOriginalExtension();
            $fileName=$file->getClientOriginalName() . '-'. Auth::id() . "." . $extension;
            //$uploadedPath=Storage::disk('public')->put($fileName, file_get_contents($file), 'public');
            $uploadedPath=$file->move(public_path('/pdf'), $fileName);*/
            Publication::create([
                'titolo' => $request['titolo'],
                'dataPubblicazione' => date('Y-m-d H:i:s'),
                'pdf' => $request->file('pdf')->store('pdf'),
                'immagine' => '',
                'multimedia' => '',
                'tipo' => $request['tipo'],
                'visibilita' => $request['visibilita'],
                'tags' => $request['tags'],
                'descrizione' => $request['descrizione'],
                'coautori' => $request['coautori'],
                'idUser' => Auth::id()
            ]);
        } else {
             Publication::create([
                'titolo' => $request['titolo'],
                'dataPubblicazione' => date('Y-m-d H:i:s'),
                'pdf' => '',
                'immagine' => '',
                'multimedia' => '',
                'tipo' => $request['tipo'],
                'visibilita' => $request['visibilita'],
                'tags' => $request['tags'],
                'descrizione' => $request['descrizione'],
                'coautori' => $request['coautori'],
                'idUser' => Auth::id()
            ]);
        }
        return redirect('users');
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
            ->select('id', 'titolo', 'dataPubblicazione', 'pdf', 'immagine', 'multimedia', 'tipo', 'visibilita', 'tags', 'descrizione', 'coautori', 'idUser')
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
            'tags' => '',
            'coautori' => '',
            'idUser' => ''
        ]);

        $publication->titolo = $request->get('titolo');
        $publication->dataPubblicazione = date('Y-m-d H:i:s');
        $publication->pdf = null;
        $publication->immagine = null;
        $publication->multimedia = null;
        $publication->tipo = $request->get('tipo');
        $publication->visibilita = $request->get('visibilita');
        $publication->tags = $request->get('tags');
        $publication->descrizione = $request->get('descrizione');
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

    /**
     * Importa dati da dblp e li inserisce nel database
     * vengono importati fino a 10 coautori per motivi di spazio
     *
     */
    public function import(){
        $res=PaperSearcher::search(Auth::user()->name." ".Auth::user()->cognome);
        foreach($res as $paper){
            $authors=$paper['info']['authors']['author'];
            $coauthors="";
            $i=0;
            foreach ($authors as $author) {
                $i++;
                if($i==count($authors))
                    $coauthors=$coauthors.$author;
                else if($i==10){
                    $coauthors=$coauthors.$author.", e altri";
                    break;
                }
                else
                    $coauthors=$coauthors.$author.", ";
            }
            Publication::create([
                'titolo' => $paper['info']['title'],
                'dataPubblicazione' => date('Y-m-d H:i:s'),
                'pdf' => '',
                'immagine' => '',
                'multimedia' => '',
                'tipo' => $paper['info']['type'],
                'visibilita' => '0',
                'tags' => '',
                'descrizione' => '',
                'coautori' => $coauthors,
                'idUser' => Auth::id()
            ]);
        }

        return redirect('/home');
    }

}
