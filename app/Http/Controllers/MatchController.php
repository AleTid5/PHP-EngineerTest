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
     * TODO it's mocked, make this work :)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function matches() {
        return response()->json($this->fakeMatches());
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
        $game = new Game($id);
        $game->initializeBoard($player);
        $board = $game->getBoard();

        return response()->json([
            'id' => $id,
            'name' => 'Match' . $id,
            'next' => (int) $board['next'],
            'winner' => $board['winner'],
            'board' => $board['board'],
        ]);
    }

    /**
     * Makes a move in a match
     *
     * TODO it's mocked, make this work :)
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($id) {
        $position = Input::get('position');
        $player = Input::get('player');

        $game = new Game($id);
        $game->changeBoard($player, $position);
        $board = $game->getBoard();

        return response()->json([
            'id' => $id,
            'name' => 'Match' . $id,
            'next' => $board['next'],
            'winner' => $board['winner'],
            'board' => $board['board'],
        ]);
    }

    /**
     * Creates a new match and returns the new list of matches
     *
     * TODO it's mocked, make this work :)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        $game = new Game();
        $game->getAllGames();
        return response()->json($this->fakeMatches());
    }

    /**
     * Deletes the match and returns the new list of matches
     *
     * TODO it's mocked, make this work :)
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        return response()->json($this->fakeMatches()->filter(function($match) use($id){
            return $match['id'] != $id;
        })->values());
    }

    /**
     * Creates a fake array of matches
     *
     * @return \Illuminate\Support\Collection
     */
    private function fakeMatches() {

        return collect((new Game())->getAllGames());
    }

}