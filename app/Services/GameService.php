<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GameService
{
    /**
     * Combinations that win the game.
     * (Would be set in constructor for dynamic size board)
     *
     * @var int
     */
    const BOARD_SIZE = 9;

    /**
     * Calculate the size of a board line.
     * (Horizontal / Vertical / Diagonal)
     */
    const BOARD_LINE = 3;

    /**
     * Combinations that win the game.
     * (Would be generated for dynamic size board)
     *
     * @var array
     */
    const WINNING_LINES = [
        // Horizontal.
        [0, 1, 2],
        [3, 4, 5],
        [6, 7 ,8],
        // Vertical.
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        // Diagonal.
        [0, 4, 8],
        [2, 4, 6],
     ];

    /**
     * Create the game.
     *
     * @param Request $request
     * @return Game
     */
    public static function createGame(Request $request): Game
    {
        $game = Game::create([
            'player1_symbol' => $request->player1_symbol,
            'player_move' => rand(1, 2),
            'player1_name' => $request->player1_name,
            'player2_name' => $request->player2_name ? $request->player2_name : null,
            'moves' => array_fill(0, self::BOARD_SIZE, ''),
            'winner' => null,
            'winning_squares' => [],
        ]);

        // If single player and computer starting, make computer move.
        if (!$game->player2_name && $game->player_move === 2) {
            self::computerMakeMove($game);
        }

        return $game;
    }

    /**
     * Return a formatted array for the current game model.
     *
     * @param Game $game
     * @return array
     */
    public static function getGameResponse(Game $game): array
    {
        return $game->toArray();
    }

    /**
     * Check if the current board has a winner.
     *
     * @param Game $game
     * @return void
     */
    public static function checkForWinner(Game $game): void
    {
        // Loop the winning lines to is if either player has won.
        foreach (self::WINNING_LINES as $winningLine) {
            $line = array_count_values(array_intersect_key($game->moves, array_flip($winningLine)));

            if (($line['x'] ?? 0) === self::BOARD_LINE || ($line['o'] ?? 0) === self::BOARD_LINE) {
                // We have a winner, set the winner and save to the database.
                $game->winner = (($line[$game->player1_symbol] ?? 0) === self::BOARD_LINE) ? 1 : 2;
                $game->winning_squares = $winningLine;
                $game->save();
                return;
            }
        }
    }

    /**
     * Check if the game is finished without a winner.
     *
     * @param Game $game
     * @return void
     */
    public static function checkForDraw(Game $game): void
    {
        if (!in_array('', $game->moves) && !$game->winner) {
            // We have a draw, set the winner and save to the database.
            $game->winner = 3;
            $game->save();
        }
    }

    /**
     * Save the players move.
     *
     * @param Game $game
     * @param Request $request
     * @return void
     */
    public static function playerMakeMove(Game $game, Request $request): void
    {
        $moves = $game->moves;
        $moves[$request->move] = $game->{strtr('player{playerMove}_symbol', ['{playerMove}' => $game->player_move])};

        // Update the moves.
        $game->moves = $moves;

        // Update the next player move.
        $game->player_move = $game->player_move === 1 ? 2 : 1;

        // Save game.
        $game->save();

        // Check for winner.
        self::checkForWinner($game);

        // Check for draw.
        self::checkForDraw($game);

        // If no winner, and single player, make computer move.
        if (!$game->player2_name && !$game->winner) {
            self::computerMakeMove($game);

            // Check for winner again.
            self::checkForWinner($game);

            // Check for draw again.
            self::checkForDraw($game);
        }
    }

    /**
     * Make a move based on the current board.
     *
     * @param Game $game
     * @return void
     */
    public static function computerMakeMove(Game $game): void
    {
        $gameMoves = $game->moves;

        // Update the next player move.
        $game->player_move = 1;

        // If computer is making the first move, start in the centre, or occasionally a corner.
        if ((array_count_values($game->moves)[''] ?? 0) === self::BOARD_SIZE) {
            if (self::percentageChance(70)) {
                // Start in center.
                $square = floor(self::BOARD_SIZE / 2);
            } else {
                // Start in a corner.
                $corners = [
                    0,
                    self::BOARD_LINE - 1,
                    self::BOARD_SIZE - self::BOARD_LINE,
                    self::BOARD_SIZE - 1,
                ];
                $square = $corners[array_rand($corners)];
            }

            // Put the computer symbol in the relevant moves square.
            $gameMoves[$square] = $game->player2_symbol;
            $game->moves = $gameMoves;

            // Save game and return.
            $game->save();
            return;
        }

        // If the computer is responding to the first move, go in the centre, or a corner.
        if ((array_count_values($game->moves)[''] ?? 0) === self::BOARD_SIZE - 1) {
            $centre = floor(self::BOARD_SIZE / 2);

            if ($game->moves[$centre] === '') {
                // Go in center.
                $square = $centre;
            }
            else {
                // Go in a corner.
                $corners = [
                    0,
                    self::BOARD_LINE - 1,
                    self::BOARD_SIZE - self::BOARD_LINE,
                    self::BOARD_SIZE - 1,
                ];
                $square = $corners[array_rand($corners)];
            }

            // Put the computer symbol in the relevant moves square.
            $gameMoves[$square] = $game->player2_symbol;
            $game->moves = $gameMoves;

            // Save game and return.
            $game->save();
            return;
        }

        // Loop winning lines to complete a win for the computer.
        foreach (self::winningLines() as $winningLine) {
            $line = array_count_values(array_intersect_key($game->moves, array_flip($winningLine)));

            if (($line[$game->player2_symbol] ?? 0) === self::BOARD_LINE - 1 &&
                ($line[$game->player1_symbol] ?? 0) === 0) {
                // Fill the empty square to win the game.
                $square = array_search('', array_intersect_key($game->moves, array_flip($winningLine)));
            }

            if (isset($square)) {
                // Put the computer symbol in the relevant moves square.
                $gameMoves[$square] = $game->player2_symbol;
                $game->moves = $gameMoves;

                // Save game and return.
                $game->save();
                return;
            }
        }

        // Loop winning lines to prevent a win for the player.
        foreach (self::winningLines() as $winningLine) {
            $line = array_count_values(array_intersect_key($game->moves, array_flip($winningLine)));

            if (($line[$game->player1_symbol] ?? 0) === self::BOARD_LINE - 1 &&
                ($line[$game->player2_symbol] ?? 0) === 0) {
                // Fill the empty square to prevent player from winning (potentially).
                $square = array_search('', array_intersect_key($game->moves, array_flip($winningLine)));
            }

            if (isset($square)) {
                // Put the computer symbol in the relevant moves square.
                $gameMoves[$square] = $game->player2_symbol;
                $game->moves = $gameMoves;

                // Save game and return.
                $game->save();
                return;
            }
        }

        // No win happening just yet, pick a square based on the existing moves.
        foreach (self::winningLines() as $winningLine) {
            $line = array_count_values(array_intersect_key($game->moves, array_flip($winningLine)));

            // Find a line that the computer has a symbol in already, and the player doesn't.
            if (($line[$game->player2_symbol] ?? 0) > 0 &&
                ($line[$game->player1_symbol] ?? 0) === 0) {
                // Continue the line.
                $square = array_search('', array_intersect_key($game->moves, array_flip($winningLine)));
            }

            if (isset($square)) {
                // Put the computer symbol in the relevant moves square.
                $gameMoves[$square] = $game->player2_symbol;
                $game->moves = $gameMoves;

                // Save game and return.
                $game->save();
                return;
            }
        }
        foreach (self::winningLines() as $winningLine) {
            $line = array_count_values(array_intersect_key($game->moves, array_flip($winningLine)));

            // Find a line that the player doesn't have a symbol in already.
            if (($line[$game->player1_symbol] ?? 0) === 0) {
                // Start or continue the line.
                $square = array_search('', array_intersect_key($game->moves, array_flip($winningLine)));
            }

            if (isset($square)) {
                // Put the computer symbol in the relevant moves square.
                $gameMoves[$square] = $game->player2_symbol;
                $game->moves = $gameMoves;

                // Save game and return.
                $game->save();
                return;
            }
        }

        // At this point, any random square is fine.
        $square = array_search('', $game->moves);

        // Put the computer symbol in the relevant moves square.
        $gameMoves[$square] = $game->player2_symbol;
        $game->moves = $gameMoves;

        // Save game.
        $game->save();
    }

    /**
     * Return all the winning lines, but shuffled to add more randomness tot he computer play.
     *
     * @return array
     */
    private static function winningLines(): array
    {
        $winningLines = self::WINNING_LINES;
        shuffle($winningLines);
        return $winningLines;
    }

    /**
     * Add some randomness to the computer play.
     *
     * @param int $chance
     * @return bool
     */
    private static function percentageChance(int $chance): bool
    {
        $randPercent = rand(0,99);
        return $chance > $randPercent;
    }
}
