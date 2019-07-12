<?php


namespace App\Http\Controllers;


use App\Models\Ranking;

class RankingsController extends Controller
{
    public function index()
    {
        $rankings = Ranking::where('active', true)
            ->orderBy('level')
            ->get();

        return response($rankings, 200);
    }
}