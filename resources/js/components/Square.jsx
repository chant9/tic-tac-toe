import React, { useCallback } from 'react';
import styles from './styles/styles.module.scss';
import classNames from 'classnames';

const Square = ({ index, game, makeMove, value }) => {
    // Call the make move function to interact with the backend.
    const handleMakeMove = useCallback((index) => {
        if (game?.winner !== null || value !== '') {
            return;
        }

        makeMove(index);
    }, [game, makeMove, value]);

    return (
        <div className={classNames(styles.square, {
            [styles.available]: value === '' && game.winner === null,
            [styles.finished]: game.winner !== null,
            [styles.winner]: game.winner !== null && game.winningSquares.includes(index) && (game.gameStyle === 'multi' || game.winner === 1),
            [styles.loser]: game.winner !== null && game.winningSquares.includes(index) && (game.gameStyle === 'single' && game.winner === 2),
            [styles.draw]: game.winner !== null && game.winner === 3,
        })} onClick={() => handleMakeMove(index)}>

            {value === 'x' && (
                <i className="fa fa-close"></i>
            )}
            {value === 'o' && (
                <i className="fa-classic fa-circle"></i>
            )}

        </div>
    );
}

export default Square;
