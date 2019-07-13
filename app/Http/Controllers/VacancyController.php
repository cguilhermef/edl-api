<?php


namespace App\Http\Controllers;


use App\Models\Ranking;
use App\Models\Team;
use App\Models\User;
use App\Models\Vacancies;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    public function index(Request $request)
    {

        $user = $request->user();

        if ($user) {
            return $this->vacanciesByUser($user, $request);
        }

        $size = $request->query('size', 2);
        $page = $request->query('page', 1);

        $vacancies = Vacancies::orderBy('created_at', 'desc')
            ->with(['team', 'team.initialRanking', 'team.owner', 'team.owner.summoner'])
            ->paginate($size, ['*'], 'page', $page);
        return response($vacancies, 200);
    }

    private function vacanciesByUser(User $user, Request $request)
    {
        $size = $request->query('size', 2);
        $page = $request->query('page', 1);

        $vacancies = Vacancies::orderBy('created_at', 'desc')
            ->with(['team.initialRanking', 'team.owner', 'team.owner.summoner'])
            ->paginate($size, ['*'], 'page', $page);
        return response($vacancies, 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'roleId' => 'required',
            'teamId' => 'required'
        ]);
        $user = $request->user();
        $team = Team::where('id', $request->input('teamId'))->first();

        if (!$team) {
            return response('Equipe não encontrada', 422);
        }

        if ($team->owner !== $user->id) {
            return response('Você não pode criar vagas nesta equipe!', 403);
        }

        $vacancy = Vacancies::where('roleId', $request->input('roleId'))
            ->where('teamId', $request->input('teamId'))->first();

        if ($vacancy) {
            return response('Já existe uma vaga para esta posição, nesta equipe!', 409);
        }

        $vacancy = Vacancies::create([
            'roleId' => $request->input('roleId'),
            'teamId' => $request->input('teamId')
        ]);

        return response($vacancy, 201);
    }

    public function destroy(Request $request, $vacancyId)
    {
        $user = $request->user();
        $vacancy = Vacancies::where('id', $vacancyId)->first();
        if (!$vacancy) {
            return response('Vaga não encontrada', 404);
        }

        $team = Team::where('id', $vacancy->teamId)->first();

        if ($team->owner !== $user->id) {
            return response('Você não pode criar vagas nesta equipe!', 403);
        }

        Vacancies::where('id', $vacancyId)->delete();

        return response('Vaga excluída com sucesso!', 204);
    }
}