<?php
/**
 * Created by PhpStorm.
 * User: guilh
 * Date: 18/05/2019
 * Time: 17:33
 */

namespace App\Http\Controllers;


use App\Http\Resources\UserResource;
use App\Models\AccountConfirmation;
use App\Models\PasswordForgotten;
use App\Models\Ranking;
use App\Models\Summoner;
use App\Models\User;
use App\Models\UserRankings;
use App\Services\RiotApiService;
use App\Services\UserService;
use App\Services\UserSessionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var UserSessionService
     */
    private $userSessionService;

    private $riotApiService;

    public function __construct(
        UserService $userService,
        UserSessionService $userSessionService,
        RiotApiService $riotApiService)
    {
        $this->riotApiService = $riotApiService;
        $this->userService = $userService;
        $this->userSessionService = $userSessionService;
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            return response('Email já cadastrado!', 400);
        }

        $user = $this->userService->register($request->all());

        return new UserResource($user);
    }

    public function validateEmail(Request $request)
    {
        $user = $request->user();
        $this->userService->sendUserEmailValidate($user);
    }

    public function activate(Request $request, string $token)
    {

        if ($this->userService->activate($token)) {
            return response(null, 204);
        }

        return response('Error on email validation', 400);
    }

    public function login(Request $request)
    {
        /** valida se foram enviados usuário e senha corretamente */
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        /** Verifica se o usuário existe e se a senha está correta */
        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response('Usuário ou senha inválidos', 401);
        }

        $session = $this->userSessionService->start($user, $request);

        $summoner = Summoner::where('userId', $user->id)->first();
        if ($summoner) {
            $summoner = $this->riotApiService->summonerByAccountId($summoner->accountId);
            Summoner::where('summonerId', $summoner->id)
                ->update([
                    'name' => $summoner->name,
                    'summonerLevel' => $summoner->summonerLevel,
                    'revisionDate' => $summoner->revisionDate,
                    'profileIconId' => $summoner->profileIconId
                ]);
            $summoner = Summoner::where('userId', $user->id)->first();

            $userRankingsFromRiot = $this->riotApiService->rankingsOf($summoner->summonerId);
            UserRankings::where('userId', $user->id)->delete();
            foreach ($userRankingsFromRiot as $urr) {
                $ranking = Ranking::where('rank', $urr['rank'])
                    ->where('tier', $urr['tier'])
                    ->first();

                $userRanking = new UserRankings;
                $userRanking->userId = $user->id;
                $userRanking->rankingId = $ranking->id;
                $userRanking->queueType = $urr['queueType'];
                $userRanking->save();
            }
        }

        $userRankings = UserRankings::where('userId', $user->id)
            ->join('rankings', 'user_rankings.rankingId', '=', 'rankings.id')
            ->select('rankings.*', 'user_rankings.*')
            ->get();


        return [
            'access_token' => $session->access_token,
            'email_verified' => (bool)$user->valid,
            'account_verified' => (bool)$user->confirmed,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'summoner' => $summoner ? $summoner : null,
            'ranking' => $userRankings ? $userRankings : null
        ];
    }

    public function forgot(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response('Email não encontrado!', 400);
        }
        $this->userService->sendEmailPasswordForgotten($user);
    }

    public function recovery(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'password' => 'required'
        ]);

        $passwordForgotten = PasswordForgotten::where('token', $request->input('token'))
            ->where('created_at', '>=', (new Carbon('now'))->subMinute(10))
            ->first();
        if (!$passwordForgotten) {
            return response('Token inválido', 400);
        }
        $user = $passwordForgotten->user()->first();

        if (!$user) {
            return response('Token inválido', 400);
        }
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return response('Senha atualizada com sucesso!', 204);
    }

    public function registerAccount(Request $request)
    {
        $this->validate($request, [
            'summoner' => 'required'
        ]);

        $user = $request->user();

        $summoner = $this->riotApiService->summonerByName($request->input('summoner'));
        if (!$summoner) {
            return response('Invocador não encontrado!', 400);
        }
        $icons = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 27, 26, 28];
        $filteredIcons = array_filter($icons, function ($icon) use ($summoner) {
            return $icon !== $summoner->profileIconId;
        });

        $iconId = $filteredIcons[array_rand($filteredIcons)];

        $confirmation = AccountConfirmation::create([
            'user_id' => $user->id,
            'icon_id' => $iconId
        ]);

        if (!$confirmation) {
            return response('Erro ao criar confirmação', 400);
        }

        return response([
            'iconId' => $iconId
        ], 200);
    }

    public function confirmAccount(Request $request)
    {
        $this->validate($request, [
            'summoner' => 'required',
            'iconId' => 'required'
        ]);

        $user = $request->user();

        $summoner = $this->riotApiService->summonerByName($request->input('summoner'));
        if (!$summoner) {
            return response('Invocador não encontrado!', 400);
        }

        $accountConfirmation = AccountConfirmation::where('user_id', $user->id)
            ->where('created_at', '>=', (new Carbon('now'))->subMinute(10))
            ->where('icon_id', $request->input('iconId'))
            ->first();

        if (!$accountConfirmation) {
            return response('Erro ao confirmar conta', 400);
        }


        if ($accountConfirmation->icon_id !== $summoner->profileIconId) {
            return response('Erro ao confirmar conta. Icone incorreto, tente novamente', 400);
        }

        User::where('id', $user->id)->update(['confirmed' => true]);
        $summoner = Summoner::create([
            'summonerId' => $summoner->id,
            'accountId' => $summoner->accountId,
            'puuid' => $summoner->puuid,
            'userId' => $user->id,
            'name' => $summoner->name,
            'summonerLevel' => $summoner->summonerLevel,
            'revisionDate' => $summoner->revisionDate,
            'profileIconId' => $summoner->profileIconId
        ]);
        AccountConfirmation::where('user_id', $user->id)->delete();

        return response(json_encode($summoner), 200);
    }

    public function registerSummoner(Request $request)
    {
        $summonerName = $request->input('summoner_name');
        $summoner = $this->riotApiService->summonerByName($summonerName);
        return response(json_encode($summoner));
    }

    public function teste(Request $request)
    {
        $summoner = $this->riotApiService->summonerByName('T97 Cruciatus');
        $entries = $this->riotApiService->rankingsOf($summoner->id);

        return response($entries);
    }
}
