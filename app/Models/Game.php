<?php

namespace App\Models;

use App\Models\Traits\CamelCaseSerializable;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use CamelCaseSerializable;

    /**  @var array */
    protected $fillable = [
        'player1_symbol',
        'player1_name',
        'player2_name',
        'player_move',
        'moves',
        'winner',
        'winning_squares',
    ];

    /** @var array */
    protected $casts = [
        'moves' => 'array',
        'winning_squares' => 'array',
        'created_at'  => 'date:Y-m-d H:i:s',
    ];

    /** @var array */
    protected $attributes = [
        'moves' => '[]',
        'winning_squares' => '[]',
    ];

    /** @var string */
    protected $appends = [
        'player2_symbol',
        'game_style',
    ];

    /**
     * Return the symbol as the opposite of player1.
     */
    function getPlayer2SymbolAttribute(): string
    {
        return $this->player1_symbol === 'x' ? 'o' : 'x';
    }

    /**
     * Return if the game is single or multiplayer.
     */
    function getGameStyleAttribute(): string
    {
        return $this->player2_name ? 'multi' : 'single';
    }
}
