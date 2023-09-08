import React from 'react';
import Square from "./Square.jsx";
import styles from './styles/styles.module.scss';

const Board = ({ boardSize, game, makeMove }) => {
    return (
        <div className={styles.board}>

            {Array.from({ length: boardSize }).map((_, index) => (
                <Square key={index} index={index} value={game.moves[index]} game={game} makeMove={makeMove} />
            ))}

        </div>
    );
}

export default Board;
