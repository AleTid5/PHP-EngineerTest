<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Game
{
    private $id = null;

    public function __construct($id)
    {
        $this->id = $id;
        $this->initializeBoard();
    }

    public function initializeBoard()
    {
        $board = $this->getBoard();

        if ($board === null) {
            $this->setBoard([
                'id' => $this->id,
                'name' => 'Match' . $this->id,
                'next' => '1',
                'winner' => 0,
                'board' => [
                    0, 0, 0,
                    0, 0, 0,
                    0, 0, 0,
                ]
            ]);
        }
    }

    /**
     * @return array|mixed
     */
    public function getBoard()
    {
        return Session::get('board_' . $this->id);
    }

    /**
     * @param array|mixed $board
     */
    public function setBoard($board): void
    {
        Session::put('board_' . $this->id, $board);
    }

    public function changeBoard($player, $position)
    {
        $board = $this->getBoard();
        $board['board'][$position] = $player;
        $board['winner'] = $this->checkWinner($player, $position);
        $this->setBoard($board);
    }

    private function checkWinner($player, $position)
    {
        $board = $this->getBoard();
        $board['board'][$position] = $player;

        // Movimientos totales
        $moveLeft = 9;
        foreach (range(0, 8) as $iterator) {
            if ((int) $board['board'][$iterator] !== 0) {
                $moveLeft--;
            }
        }

        // Por cada posibilidad de partida ganada
        foreach ($this->getWinnerLines() as $winnerLine) {
            if ($board['board'][$winnerLine[0] - 1] === $board['board'][$winnerLine[1] - 1] &&
                $board['board'][$winnerLine[1] - 1] === $board['board'][$winnerLine[2] - 1]) {
                return $board['board'][$winnerLine[0] - 1];
            }
        }

        // Si no hay mas movimientos es porque no hay ganador, por lo tanto empate
        if ($moveLeft === 0) {
            return -1;
        }

        return 0;
    }

    private function getWinnerLines()
    {
        return [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
            [1, 4, 7],
            [2, 5, 8],
            [3, 6, 9],
            [1, 5, 9],
            [3, 5, 7]
        ];
    }
}