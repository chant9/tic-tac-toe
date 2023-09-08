<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GameController extends Controller
{
    /**
     * Save new game to the database and return details to frontend.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function startGame(Request $request): JsonResponse
    {
        try {
            // Validate request.
            $validator = Validator::make($request->all(), [
                'player1_name' => 'string|required',
                'player_symbol' => [
                    'string|required',
                    Rule::in(['x', 'o']),
                ],
                'moves' => 'nullable',
                'winner' => 'nullable',
            ]);
            if ($validator->fails()) {
                return response()
                    ->json([
                        'status' => 'error',
                        'error' => $validator->errors(),
                    ], 422);
            }

            // Create the game.
            $game = GameService::createGame($request);

            // Create the response for the frontend.
            $response = [
                'game' => GameService::getGameResponse($game),
                'boardSize' => GameService::BOARD_SIZE,
            ];
        }
        catch (Exception $e) {
            Log::error('Start error (' . $e->getMessage() .')');
            throw new Exception('There was an error', 500);
        }

        return response()->json($response);
    }

    /**
     * Receive the move from the user, and
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function move(Request $request): JsonResponse
    {
        try {
            $game = Game::findOrFail($request->id);

            // Validate request.
            $validator = Validator::make($request->all(), [
                'move' => 'int|required|min:0|max:' . GameService::BOARD_SIZE - 1,
            ]);
            if ($validator->fails()) {
                return response()
                    ->json([
                        'status' => 'error',
                        'error' => $validator->errors(),
                    ], 422);
            }

            // Make the move.
            GameService::playerMakeMove($game, $request);

            // Create the response for the frontend.
            $response = [
                'game' => GameService::getGameResponse($game),
                'boardSize' => GameService::BOARD_SIZE,
            ];
        }
        catch (Exception $e) {
            Log::error('Start error (' . $e->getMessage() .')');
            throw new Exception('There was an error', 500);
        }

        return response()->json($response);
    }

    /**
     * Return the 5 most recently completed games.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function gameResults(Request $request): JsonResponse
    {
        $games = Game::whereNotNull('winner')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Create the response for the frontend.
        $response = [
            'games' => $games,
        ];

        return response()->json($response);
    }
}
