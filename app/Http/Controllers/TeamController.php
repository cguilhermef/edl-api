<?php


namespace App\Http\Controllers;


use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();
        parse_str($request->getQueryString(), $params);
        $size = array_key_exists('size', $params) ? $params['size'] : 2;
        $page = array_key_exists('page', $params) ? $params['page'] : 1;
        $owner = array_key_exists('owner', $params) ? $params['owner'] : false;

        if ($owner) {
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
}