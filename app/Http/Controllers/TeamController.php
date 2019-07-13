<?php


namespace App\Http\Controllers;


use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{

    public function index(Request $request)
    {

        $size = $request->query('size', 2);
        $page = $request->query('page', 1);
        $owner = $request->query('owner', null);

        if ($owner !== 'null' && $owner !== null) {
            $teams = Team::where('owner', $owner)
                ->with(['initialRanking'])
                ->orderBy('name')
                ->paginate($size, ['*'], 'page', $page);
            return response($teams, 200);
        }
        $teams = Team::orderBy('name')
            ->with(['initialRanking'])
            ->paginate($size, ['*'], 'page', $page);
        return response($teams, 200);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:teams|min:4|max:30',
            'abbreviation' => 'required|unique:teams|min:3|max:5',
            'minRanking' => 'required'
        ]);

        $user = $request->user();

        $team = Team::create([
            'name' => $request->input('name'),
            'abbreviation' => $request->input('abbreviation'),
            'minRanking' => $request->input('minRanking'),
            'owner' => $user->id
        ]);

        if ($team) {
            return response($team, 201);
        }
        return response('Erro ao criar equipe', 400);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $team = Team::where('id', $id)->first();

        if ($team->owner !== $user->id) {
            return response('Você não tem permissão para excluir essa equipe!', 403);
        }

        $team->delete();
        return response('Time excluido com sucesso!', 204);
    }

    public function show(Request $request, $teamId)
    {
        $team = Team::where('id', $teamId)
            ->with(['vacancies', 'initialRanking'])
            ->first();
        if (!$team) {
            return response('Equipe não encontrada!', 404);
        }
        return response($team, 200);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|min:4|max:30',
            'abbreviation' => 'required|min:3|max:5',
            'minRanking' => 'required'
        ]);

        $team = Team::where('id', $id)->first();
        $user = $request->user();

        if (!$team) {
            return response('Equipe não encontrada!', 404);
        }

        if ($team->owner !== $user->id) {
            return response('Você não tem permissão para editar essa equipe!', 403);
        }

        if($request->input('name')) {
            $team->name = $request->input('name');
        }
        if($request->input('abbreviation')) {
            $team->abbreviation = $request->input('abbreviation');
        }
        if($request->input('minRanking')) {
            $team->minRanking = $request->input('minRanking');
        }
        $team->save();

        return response($team, 200);
    }
}