<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Game
{
    private $id = null;

    /**
     * Game constructor.
     * @param int|null $id
     */
    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }

    /**
     * Inicializa el tablero y el jugador que juega primero.
     *
     * @param int|null $player
     */
    public function initializeBoard(?int $player = null)
    {
        $board = $this->getBoard();

        if ($board === null) {
            $this->setBoard([
                'id' => (int) $this->id,
                'name' => 'Match' . $this->id,
                'next' => $player,
                'winner' => 0,
                'board' => [
                    0, 0, 0,
                    0, 0, 0,
                    0, 0, 0,
                ]
            ]);
        }

        $board = $this->getBoard();

        if ($player !== null && (int) $board['next'] === 0) {
            $board['next'] = $player;
            $this->setBoard($board);
        }
    }

    /**
     * Devuelve el tablero seleccionado.
     *
     * @return array|mixed
     */
    public function getBoard()
    {
        return Session::get('board_' . $this->id);
    }

    /**
     * Establece el tablero en sesión.
     *
     * @param array|mixed $board
     */
    public function setBoard($board): void
    {
        Session::put('board_' . $this->id, $board);
    }

    /**
     * Crea un juego y establece un ID basado en el último creado.
     */
    public function createMatch()
    {
        $games = $this->getAllGames();
        $this->id = isset($games[count($games) - 1]) ? $games[count($games) - 1]['id'] + 1 : 1;
        $this->initializeBoard();
    }

    /**
     * Elimina un juego seleccionado.
     */
    public function removeMatch()
    {
        Session::remove('board_' . $this->id);
    }

    /**
     * Devuelve todos los juegos que hay en sesión.
     *
     * @return array
     */
    public function getAllGames()
    {
        $mixedGames = Session::all();
        $games = [];

        foreach ($mixedGames as $sessionName => $game) {
            if (stripos($sessionName, 'board_') !== false) {
              $games[] = $game;
            }
        }

        return $games;
    }

    /**
     * Realiza una jugada en el tablero.
     *
     * @param $player
     * @param $position
     */
    public function changeBoard($player, $position)
    {
        $board = $this->getBoard();
        $board['board'][$position] = $player;
        $board['next'] = $player === 1 ? 2 : 1;
        $board['winner'] = $this->checkWinner($player, $position);
        $this->setBoard($board);
    }

    /**
     * Valida si el jugador que realizó el movimiento es ganador.
     *
     * @param $player
     * @param $position
     * @return int
     */
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

    /**
     * Devuelve las lineas ganadoras.
     *
     * @return array
     */
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