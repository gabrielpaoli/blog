<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LolApi\riotapi;

class LolController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('champions.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function champions()
    {
        $platform = 'la1';
        $instance = new riotapi($platform);
        $champions = $instance->getChampion();
        return view('lol.champions',compact('champions'));
    }


}