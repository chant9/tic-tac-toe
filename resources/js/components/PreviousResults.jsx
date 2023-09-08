import React, { useEffect, useCallback, useState } from 'react';
import Board from "./Board.jsx";
import { DateTime } from "luxon"
import { Col, Row } from "react-bootstrap";
import styles from './styles/styles.module.scss';
import classNames from 'classnames';

const PreviousResults = ({ game }) => {
    const [gameResults, setGameResults] = useState(false);

    const getGameResults = useCallback(async () => {
        await axios.get('/game-results')
            .then(response => {
                setGameResults(response.data?.games);
            })
            .catch(error => {
                console.log("Error: ", error.response.data);
            });
    }, [setGameResults]);

    const getGameResultText = useCallback((gameResult) => {
        if (gameResult.gameStyle === 'single') {
            if (gameResult.winner === 1) {
                return `${gameResult.player1Name} beat the computer`
            }
            else if (gameResult.winner === 2) {
                return `${gameResult.player1Name} lost to the computer`
            }
            else {
                return `${gameResult.player1Name} drew with the computer`
            }
        }
        else {
            if (gameResult.winner === 1) {
                return `${gameResult.player1Name} beat ${gameResult.player2Name}`
            }
            else if (gameResult.winner === 2) {
                return `${gameResult.player2Name} beat ${gameResult.player1Name}`
            }
            else {
                return `${gameResult.player2Name} drew with ${gameResult.player1Name}`
            }
        }
    }, []);

    // Call on initial load.
    useEffect (() => {
        if (gameResults === false) {
            getGameResults();
        }
    }, [getGameResults]);

    // Call each time a new game starts.
    useEffect(() => {
        if (gameResults) {
            getGameResults();
        }

    }, [game?.createdAt]);

    return (
        <>

            {!!gameResults?.length && (
                <Row>
                    <Col>
                        <hr className={`mt-4 ${styles.previousGamesHr}`} />

                        <div className={styles.previousGames}>

                            <h4 className='text-center mt-4'>Previous {gameResults.length === 1 ? 'game' : 'games'}</h4>

                            {gameResults.map((gameResult, index) => (
                                <Row key={index}>
                                    <Col className='align-self-center'>
                                        {DateTime.fromSQL(gameResult.createdAt).toLocaleString(DateTime.DATETIME_SHORT)}
                                    </Col>
                                    <Col className='align-self-center'>
                                        {getGameResultText(gameResult)}
                                    </Col>
                                    <Col className='align-self-center'>
                                        <Board game={gameResult} boardSize={9} />
                                    </Col>
                                </Row>
                            ))}
                        </div>

                    </Col>
                </Row>
            )}

        </>
    );
}

export default PreviousResults;
