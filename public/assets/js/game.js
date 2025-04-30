function play(column) {
    fetch('/play/' + column, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        updateBoard(data.board);
        updateCurrentPlayer(data.currentPlayer);
        if (data.winner) {
            showWinner(data.winner);
        }
    });
}

function reset() {
    fetch('/reset', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        updateBoard(data.board);
        updateCurrentPlayer(data.currentPlayer);
        hideWinner();
    });
}

function updateBoard(board) {
    const cells = document.querySelectorAll('.cell');
    cells.forEach(cell => {
        const row = parseInt(cell.dataset.row);
        const col = parseInt(cell.dataset.col);
        const value = board[row][col];
        
        cell.className = 'cell';
        if (value === 'red') {
            cell.classList.add('red');
            cell.innerHTML = '<div class="token"></div>';
        } else if (value === 'yellow') {
            cell.classList.add('yellow');
            cell.innerHTML = '<div class="token"></div>';
        } else {
            cell.innerHTML = '';
        }
    });
}

function updateCurrentPlayer(player) {
    const currentPlayerDiv = document.getElementById('current-player');
    currentPlayerDiv.className = `current-player ${player}`;
    currentPlayerDiv.textContent = `Tour du joueur : ${player === 'red' ? 'Rouge' : 'Jaune'}`;
}

function showWinner(winner) {
    const winnerDiv = document.getElementById('winner-message');
    if (!winnerDiv) {
        const container = document.querySelector('.container');
        const newWinnerDiv = document.createElement('div');
        newWinnerDiv.id = 'winner-message';
        newWinnerDiv.className = 'status winner';
        newWinnerDiv.textContent = `Le joueur ${winner === 'red' ? 'Rouge' : 'Jaune'} a gagné !`;
        container.insertBefore(newWinnerDiv, document.querySelector('.board'));
    }
}

function hideWinner() {
    const winnerDiv = document.getElementById('winner-message');
    if (winnerDiv) {
        winnerDiv.remove();
    }
}

// Animation au clic
document.addEventListener('DOMContentLoaded', function() {
    const cells = document.querySelectorAll('.cell');
    cells.forEach(cell => {
        cell.addEventListener('click', function() {
            this.style.transform = 'scale(1.1)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
        });
    });
}); 