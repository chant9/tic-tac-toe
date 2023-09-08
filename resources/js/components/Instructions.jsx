import React, { useCallback, useEffect, useState } from 'react';
import { Row, Col } from "react-bootstrap";
import styles from './styles/styles.module.scss';
import classNames from 'classnames';

const Instructions = ({ game }) => {
    const [instruction, setInstruction] = useState('');
    const [symbol, setSymbol] = useState(false);
    const [result, setResult] = useState(false);

    useEffect(() => {
        setResult(false);

        if (!!game.winner) {
            setSymbol(false);

            if (game.winner === 3) {
                setResult('drew');
                setInstruction(`Draw, try again!`);
            } else if (game.gameStyle === 'multi') {
                setResult('win');
                setInstruction(`Congratulation ${game.player1Name}, you win!`);
            } else if (game.winner === 1) {
                setResult('win');
                setInstruction(`Congratulation ${game.player1Name}, you win!`);
            } else if (game.gameStyle === 'multi' && game.winner === 2) {
                setResult('win');
                setInstruction(`Congratulation ${game.player2Name}, you win!`);
            } else {
                setResult('lost');
                setInstruction(`Sorry ${game.player1Name}, you lost!`);
            }

            return;
        }

        if (game.gameStyle === 'single') {
            setInstruction(`It's your turn ${game.player1Name}, `);
            setSymbol(game.player1Symbol);
        }
        else {
            if (game.playerMove === 1) {
                setInstruction(`It's your turn ${game.player1Name}, `);
                setSymbol(game.player1Symbol);
            }
            else {
                setInstruction(`It's your turn ${game.player2Name}, `);
                setSymbol(game.player2Symbol);
            }
        }
    }, [game, setInstruction, setResult, setSymbol]);

    return (
        <>

            <h1 className='text-center mb-4'>
                Tic-Tac-Toe {game?.gameStyle === 'multi' ? 'Multiplayer' : 'Single-player'}
            </h1>

            <Row>
                <Col>
                    <h4 className={classNames(`text-center ${styles.instructionHeading}`, {
                        [styles.win]: result === 'win',
                        [styles.lost]: result === 'lost',
                        [styles.drew]: result === 'drew',
                    })}>
                        {instruction}
                        {symbol === 'x' && (<i className="fa fa-close ms-2"></i>)}
                        {symbol === 'o' && (<i className="fa-classic fa-circle ms-2"></i>)}
                    </h4>
                </Col>
            </Row>

        </>
    );
}

export default Instructions;
