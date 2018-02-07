<?php

namespace App\Http\Controllers;

use App\Group;
use App\UserGroup;
use App\Admin;
use App\GroupPublication;
use App\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups=DB::table('groups')
            ->select('idGroup, titolo, descrizione')->get();
            return view('groups.index', ['groups' => $groups]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $valG=$request->validate([
            'nomeGruppo' => 'required|max:100',
            'descrizioneGruppo' => 'max:191',
            'tipoVisibilita' => 'required'
        ]);
        //qui salvo nella tabella 'gruppi' il gruppo
        Group::create([
            'nomeGruppo' => $request['nomeGruppo'],
            'descrizioneGruppo' => $request['descrizioneGruppo'],
            'tipoVisibilita' => $request['tipoVisibilita']
        ]);
        //ma poi ne recupero l'id per settare le chiavi esterne nelle due tabelle N:N 'admins' e 'usersgroups'
        $idGroup=DB::table('groups')->select('idGroup')
            ->where('nomeGruppo', '=', $request['nomeGruppo'])->first();
        $idUser=Auth::id();
        DB::table('admins')->insert([
            ['idGroup' => $idGroup->idGroup,  'idUser' => $idUser]
        ]);
        DB::table('usersgroups')->insert([
            ['idGroup' => $idGroup->idGroup,  'idUser' => $idUser]
        ]);
        return redirect('groups/'.urldecode( strval($idGroup->idGroup) ));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $publications=DB::table('groups')//prendi le publicazioni nel gruppo
            ->join('groupspublications', 'groups.idGroup', '=', 'groupspublications.idGroup')//da qui andiamo alla tabella dei post nei gruppi
            ->join('publications', 'groupspublications.idPublication', '=', 'publications.id')//per risalire alla/e pubblicazione/i
            ->join('users', 'users.id', '=', 'groupspublications.idUser')
            ->select('users.id', 'publications.titolo', 'users.name', 'users.cognome', 'groupspublications.descrizione')
            ->where('groups.idGroup', '=', $id)
            ->get();

        $adminID=DB::table('admins')//prendiamo gli admin del gruppo
            ->select('idUser')
            ->where('admins.idGroup', '=', $id);

        $admins=DB::table('users')//prendi i dati degli admin e del gruppo
            ->join('usersgroups', 'users.id', '=', 'usersgroups.idUser')
            ->join('groups', 'usersgroups.idGroup', '=', 'groups.idGroup')
            ->select('users.id', 'users.name', 'users.cognome', 'groups.idGroup', 'groups.nomeGruppo', 'groups.descrizioneGruppo')
            ->where('groups.idGroup', '=', $id)
            ->whereIn('users.id', $adminID)
            ->get();

        $users=DB::table('users')//prendi i dati degli utenti nel gruppo
            ->join('usersgroups', 'users.id', '=', 'usersgroups.idUser')
            ->select('users.id', 'users.name', 'users.cognome')
            ->where('usersgroups.idGroup', '=', $id)
            ->whereNotIn('users.id', $adminID)
            ->get();

        $code=0;
        //controlla se e nel gruppo
        $isPart=DB::table('usersgroups')
            ->select('idUser')
            ->where('usersgroups.idGroup', '=', $id)
            ->where('idUser', Auth::id())
            ->get();
        if(count($isPart)>0){
            //controlla se e un admin
            $isAdmin=DB::table('admins')
                ->select('idUser')
                ->where('idGroup', '=', $id)
                ->where('idUser', '=', Auth::id())
                ->get();
            if(count($isAdmin)>0){
                $code=2;
            }
            else{
                $code=1;
            }
        }
        else{
            //controlla richesta in pendenza
            $hasReq=DB::table('participationrequests')
                ->select('idUser')
                ->where('idUser', '=', Auth::id())
                ->where('idGroup', '=', $id)
                ->get();
            if(count($hasReq)>0)
                $code=3;
            else
                $code=0;
        }

        return view('groups.show', ['groupUsers' => $users, 'publications' => $publications, 'admins' => $admins, 'code' => $code]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        //
    }

    /**
     * Removes the user from the group
     *
     */
    public function quit($idGroup){
        //removes if admin
        DB::table('admins')
            ->where('idGroup', '=', $idGroup)
            ->where('idUser', '=', Auth::id())
            ->delete();

        //removes
        DB::table('usersgroups')
            ->where('idGroup', '=', $idGroup)
            ->where('idUser', '=', Auth::id())
            ->delete();

        //deletes group and requests if empty
        if(!(count(DB::table('admins')->where('idGroup', '=', $idGroup)->get()) > 0)){
            DB::table('participationrequests')->where('idGroup', '=', $idGroup)->delete();
            DB::table('groups')->where('idGroup', '=', $idGroup)->delete();
        }

        return redirect('home');
    }

    public function rintraccia($idGroup, $id)
    {
        //cerca le pubblicazioni gia condivise
        $subQuery = DB::table('groupspublications')->select('idPublication AS id')->where('idGroup', '=', $idGroup);
        
        $suePubblicazioni=DB::table('publications')//dalla tabella publications
            ->select('id', 'titolo', 'dataPubblicazione', 'tipo', 'tags', 'coautori')//mi prendi le cose essenziali di una pubblicazione da condividere
            ->where('idUser', '=', $id)//a patto che sia mia...
            //->where('visibilita', '=', '1')//... e che sia pubblica
            ->whereNotIn('id', $subQuery)//rimuove le pubblicazioni gia condivise
            ->get();
        return view('groups.rintraccia', ['suePubblicazioni' => $suePubblicazioni, 'idGroup' => $idGroup]);
    }

    public function aggiungi($idGroup)
    {
        $id=$_GET["pubID"];
        $descr=$_GET["descr".$id];
        DB::table('groupspublications')
            ->insert(['idUser' => Auth::id(), 'idGroup' => $idGroup, 'idPublication' => $id, 'descrizione' => $descr]);
        return redirect('groups/'.$idGroup);
    }

    /**
     * searches for partecipants for groups
     *
     */
    function searchPartecipants($idGroup){
        //cerca gli utenti gia nel gruppo per scremare il risultato
        $part=DB::table('users')
            ->join('usersgroups', 'users.id', '=', 'usersgroups.idUser')
            ->select('users.id')
            ->where('usersgroups.idGroup', '=', $idGroup);

        $users = DB::table('users')->select('id', 'name', 'cognome', 'affiliazione', 'linea_ricerca')
            ->whereNotIn('users.id', $part)
            ->get();

        return view('groups/adduser',["users" => $users, "idGroup" => $idGroup]);
    }

    function addPartecipants($idGroup){
        $id=$_GET["userID"];
        DB::table('participationrequests')
            ->insert(['idUser' => $id, 'idGroup' => $idGroup, 'fromAdmin' => true]);
        return redirect('groups/'.$idGroup);
    }

    function sendReq($idGroup){
        $id=Auth::id();
        DB::table('participationrequests')
            ->insert(['idUser' => $id, 'idGroup' => $idGroup, 'fromAdmin' => false]);
        return redirect('groups/'.$idGroup);
    }

    function promote($idGroup, $idUser){
        DB::table('admins')
            ->insert(['idGroup' => $idGroup, 'idUser' => $idUser]);
        return redirect('groups/'.$idGroup);
    }

    function getGroups(){
        $id=Auth::id();
        
        $admined=DB::table('groups')
            ->join('admins', 'admins.idGroup', '=', 'groups.idGroup')
            ->select('groups.idGroup', 'groups.nomeGruppo')
            ->where('admins.idUser', '=', $id)
            ->get();

        $other=DB::table('groups')
            ->join('usersgroups', 'usersgroups.idGroup', '=', 'groups.idGroup')
            ->select('groups.idGroup', 'groups.nomeGruppo')
            ->where('usersgroups.idUser', '=', $id)
            ->whereNotIn('groups.idGroup', $admined->pluck('idGroup'))
            ->get();    

        if( (count($admined) + count($other)) >0){
            echo '<h5 style="margin-left: 10px">Administrated groups</h5>';
            if(count($admined)>0){
                foreach ($admined as $g) {
                    echo '<li><a href="/groups/'.$g->idGroup.'">'.$g->nomeGruppo.'</a></li>';
                }
            }
            else
                echo '<h6 style="text-align: center">You administrate no groups</h6>';
            echo '<li><hr></li>';
            echo '<h5 style="margin-left: 10px">Your other groups</h5>';
            if(count($other)>0){
                foreach ($other as $g) {
                    echo '<li><a href="/groups/'.$g->idGroup.'">'.$g->nomeGruppo.'</a></li>';
                }
            }
            else{
                echo '<h6 style="text-align: center">You participate in no other groups</h6>';
            }
        }
        else{
            echo '<h5 style="text-align: center">You participate in no groups, yet</h5>';
            echo '<li><a href="/groups/create" style="text-align: center">Create Group</a></li>';
        }
    }

}
