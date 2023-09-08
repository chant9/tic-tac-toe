import React from 'react';
import ReactDOM from 'react-dom/client';
import TicTacToe from "./TicTacToe.jsx";

if (document.getElementById('game')) {
    const Index = ReactDOM.createRoot(document.getElementById("game"));

    Index.render(
        <React.StrictMode>
            <TicTacToe/>
        </React.StrictMode>
    )
}
