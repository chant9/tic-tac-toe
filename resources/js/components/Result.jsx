import React, { useCallback } from 'react';
import { Button, Col, Row } from "react-bootstrap";

const Result = ({ game, newGame, startGame }) => {

    // Call the start game function to interact with the backend.
    const handleStartGame = useCallback(() => {
        startGame(game.gameStyle, game.player1Symbol, game.player1Name, game.player2Name);
    }, [game, startGame]);

    const handleNewGame = useCallback(() => {
        newGame();
    }, [newGame]);

    return (
        <>

            {!!game.winner && (
                <Row>
                    <Col className='text-center'>
                        <Button variant="success" onClick={handleStartGame} className='ms-2 me-2 mb-2'>
                            Restart game
                        </Button>
                        <Button variant="success" onClick={handleNewGame} className='ms-2 me-2 mb-2'>
                            Start a new game
                        </Button>
                    </Col>
                </Row>
            )}

        </>
    );
}

export default Result;
