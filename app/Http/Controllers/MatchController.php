<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class MatchController extends Controller {

    public function index() {
        return view('index');
    }

    /**
     * Returns a list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function matches() {
        return response()->json($this->getMatches());
    }

    /**
     * Returns the state of a single match
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function match($id, Request $request) {
        $player = $request->request->get('player');
        $game = new Game(['id' => $id]);
        $game->initializeBoard($player);
        $board = Game::query()->where('id', $id)->first();

        if ($board === null) {
            return response()->json([]);
        }

        $board = $board->toArray();

        return response()->json([
            'id' => $id,
            'name' => 'Match' . $id,
            'next' => (int) $board['next'],
            'winner' => $board['winner'],
            'board' => json_decode($board['board'], false)
        ]);
    }

    /**
     * Makes a move in a match
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($id) {
        $position = Input::get('position');
        $player = Input::get('player');

        $game = new Game(['id' => $id]);
        $game->changeBoard($player, $position);
        $board = Game::query()->where('id', $id)->first()->toArray();

        return response()->json([
            'id' => $id,
            'name' => 'Match' . $id,
            'next' => $board['next'],
            'winner' => $board['winner'],
            'board' => json_decode($board['board'], false)
        ]);
    }

    /**
     * Creates a new match and returns the new list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        $game = new Game();
        $game->initializeBoard();

        return response()->json($this->getMatches());
    }

    /**
     * Deletes the match and returns the new list of matches
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        (new Game())::query()->find($id)->delete();

        return response()->json($this->getMatches());
    }

    /**
     * Return matches
     *
     * @return \Illuminate\Support\Collection
     */
    private function getMatches()
    {
        $games = Game::all()->toArray();

        foreach ($games as &$game) {
            $game['board'] = json_decode($game['board'], false);
        }

        return collect($games);
    }

}