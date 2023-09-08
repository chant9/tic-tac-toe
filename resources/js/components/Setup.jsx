import React, { useState, useCallback, useEffect } from 'react';
import { Button, Col, Form, Row, ToggleButton, ToggleButtonGroup } from "react-bootstrap";
import styles from './styles/styles.module.scss';

const Setup = ({ game, startGame }) => {
    const [player1Name, setPlayer1Name] = useState('');
    const [player1Symbol, setPlayer1Symbol] = useState('x');
    const [gameStyle, setGameStyle] = useState('single');
    const [player2Name, setPlayer2Name] = useState('');
    const [step, setStep] = useState(1);
    const [stepComplete, setStepComplete] = useState(true);

    // Basic validation of each step.
    useEffect(() => {
        // Check for invalid config.
        if (step === 1 && player1Name === '') {
            return setStepComplete(false);
        }
        else if (step === 2 && gameStyle === 'multi' && player2Name === '') {
            return setStepComplete(false);
        }
        else if (step === 2 && gameStyle === 'multi' && player1Name === player2Name) {
            return setStepComplete(false);
        }

        // Everything looks good.
        setStepComplete(true);
    }, [gameStyle, player1Name, player2Name, step]);

    // Call the start game function to interact with the backend.
    const handleStartGame = useCallback(() => {
        startGame(gameStyle, player1Symbol, player1Name, player2Name);
    }, [gameStyle, player1Symbol, player1Name, player2Name, startGame]);

    return (
        <>

            <h1 className="text-center mb-4">Welcome to my Tic-Tac-Toe game</h1>

            <Row>
                <Col>
                    <h5 className='text-center'>Please complete the options below, then click {step === 1 ? "'Continue'" : "'Start game'"}.</h5>
                </Col>
            </Row>

            <div className={`text-center ${styles.introPanel}`}>

                {step === 1 && (
                    <>

                        <Row>
                            <Col>
                                <Form.Group controlId="player1NameGroup">
                                    <Form.Label>Player 1 name:</Form.Label>
                                    <Form.Control type="text" value={player1Name} className='text-center' onChange={(event) => {
                                        setPlayer1Name(event.target.value.trim())
                                    }} />
                                </Form.Group>
                            </Col>
                        </Row>

                        <Row>
                            <Col>
                                <Form.Label>Player 1 symbol:</Form.Label>
                                <ToggleButtonGroup type="radio" name="player1Symbol" defaultValue={player1Symbol} onChange={(event) => {
                                    setPlayer1Symbol(event)
                                }}>
                                    <ToggleButton variant="outline-primary" id="player1Symbol-x" value={'x'}>
                                        <i className="fa fa-close"></i>
                                    </ToggleButton>
                                    <ToggleButton variant="outline-primary" id="player1Symbol-o" value={'o'}>
                                        <i className="fa-classic fa-circle"></i>
                                    </ToggleButton>
                                </ToggleButtonGroup>
                            </Col>
                        </Row>

                        <Row>
                            <Col>
                                <Button variant="success" onClick={() => setStep(step + 1)} disabled={!stepComplete} className='ms-2 me-2 mb-2'>
                                    Continue
                                </Button>
                            </Col>
                        </Row>
                    </>
                )}

                {step === 2 && (
                    <>
                        <Row>
                            <Col>
                                <Form.Label>Game style:</Form.Label>
                                <ToggleButtonGroup type="radio" name="gameStyle" defaultValue={gameStyle} onChange={(event) => {
                                    setGameStyle(event)
                                }}>
                                    <ToggleButton variant="outline-primary" id="player1Symbol-single" value={'single'}>
                                        Single-player
                                    </ToggleButton>
                                    <ToggleButton variant="outline-primary" id="player1Symbol-multi" value={'multi'}>
                                        Multiplayer
                                    </ToggleButton>
                                </ToggleButtonGroup>
                            </Col>
                        </Row>

                        {gameStyle === 'multi' && (
                            <Row>
                                <Col>
                                    <Form.Group controlId="player2NameGroup">
                                        <Form.Label>Player 2 name:</Form.Label>
                                        <Form.Control type="text" value={player2Name} className='text-center' onChange={(event) => {
                                            setPlayer2Name(event.target.value.trim())
                                        }}/>
                                    </Form.Group>
                                </Col>
                            </Row>
                        )}

                        <Row>
                            <Col>
                                <Button variant="outline-success" onClick={() => setStep(step - 1)} className='ms-2 me-2 mb-2'>
                                    Back
                                </Button>
                                <Button variant="success" onClick={handleStartGame} disabled={!stepComplete} className='ms-2 me-2 mb-2'>
                                    Start game
                                </Button>
                            </Col>
                        </Row>
                    </>
                )}

            </div>

        </>
    );
}

export default Setup;
