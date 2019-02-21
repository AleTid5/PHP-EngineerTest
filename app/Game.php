<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'games';
    protected $fillable = ['id', 'name', 'next', 'winner', 'board'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Inicializa el tablero y el jugador que juega primero.
     *
     * @param int|null $player
     */
    public function initializeBoard(?int $player = null) : void
    {
        $board = self::query()->where('id', $this->getAttribute('id'))->first();

        if ($board === null && (int) $player === 0) {
            $lastID = self::query()->insertGetId([
                'name' => 'Match',
                'next' => (int) $player,
                'winner' => 0,
                'board' => json_encode([
                    0, 0, 0,
                    0, 0, 0,
                    0, 0, 0,
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ]);

            self::query()->find($lastID)->update([
                'name' => 'Match' . $lastID
            ]);

            return;
        }

        $board = $board->toArray();

        if ($player !== null && (int) $board['next'] === 0) {
            $board['next'] = $player;
            self::query()->find(self::getAttribute('id'))->update($board);
        }
    }

    /**
     * Realiza una jugada en el tablero.
     *
     * @param $player
     * @param $position
     */
    public function changeBoard($player, $position)
    {
        $board = self::query()->find($this->getAttribute('id'))->toArray();
        $board['board'] = json_decode($board['board'], false);
        $board['board'][$position] = $player;
        $board['next'] = $player === 1 ? 2 : 1;
        $board['winner'] = $this->checkWinner($player, $position);
        $board['board'] = json_encode($board['board']);
        self::query()->find(self::getAttribute('id'))->update($board);
    }

    /**
     * Elimina un juego seleccionado.
     */
    public function removeMatch()
    {
        self::query()->find(1)->delete();
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
        $board = self::query()->find($this->getAttribute('id'))->toArray();
        $board['board'] = json_decode($board['board'], false);
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