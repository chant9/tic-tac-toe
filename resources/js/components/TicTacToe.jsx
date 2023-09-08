import React, { useCallback, useState } from 'react';
import { Container } from "react-bootstrap";
import Setup from "./Setup.jsx";
import Instructions from "./Instructions.jsx";
import Board from "./Board.jsx";
import Result from "./Result.jsx";
import PreviousResults from "./PreviousResults.jsx";

const TicTacToe = () => {
    const [game, setGame] = useState({});
    const [boardSize, setBoardSize] = useState(0);

    const startGame = useCallback((gameStyle, player1Symbol, player1Name, player2Name) => {
        axios.post('/start', { gameStyle, player1Symbol, player1Name, player2Name })
            .then(response => {
                setGame(response.data?.game);
                setBoardSize(response.data?.boardSize);
            })
            .catch(error => {
                console.log("Error: ", error.response.data);
            });
    }, [setGame]);

    const newGame = useCallback(() => {
        setGame({});
    }, [setGame]);

    const makeMove = useCallback((move) => {
        axios.post('/move', { id: game.id, move })
            .then(response => {
                setGame(response.data?.game);
            })
            .catch(error => {
                console.log("Error: ", error.response.data);
            });
    }, [game, setGame]);

    return (
        <Container>

            {game?.id ? (
                <>
                    <Instructions game={game} makeMove={makeMove} />
                    <Board boardSize={boardSize} game={game} makeMove={makeMove} />
                </>
            ) : (
                <Setup game={game} startGame={startGame} />
            )}

            <Result game={game} startGame={startGame} newGame={newGame} />

            <PreviousResults game={game} />

        </Container>
    );
}

export default TicTacToe;
