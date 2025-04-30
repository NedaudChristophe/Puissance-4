<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    private array $board = [];
    private const ROWS = 6;
    private const COLUMNS = 7;
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->initializeBoard();
    }

    private function initializeBoard(): void
    {
        if (!$this->session->has('board')) {
            $this->board = array_fill(0, self::ROWS, array_fill(0, self::COLUMNS, null));
            $this->session->set('board', $this->board);
            $this->session->set('currentPlayer', 'red');
        } else {
            $this->board = $this->session->get('board');
        }
    }

    #[Route('/', name: 'game_index')]
    public function index(): Response
    {
        $winner = $this->checkWinner();
        return $this->render('game/index.html.twig', [
            'board' => $this->board,
            'currentPlayer' => $this->session->get('currentPlayer'),
            'winner' => $winner,
        ]);
    }

    #[Route('/play/{column}', name: 'game_play', methods: ['POST'])]
    public function play(int $column): JsonResponse
    {
        if ($this->checkWinner()) {
            return $this->json([
                'board' => $this->board,
                'currentPlayer' => $this->session->get('currentPlayer'),
                'winner' => $this->checkWinner(),
            ]);
        }

        $currentPlayer = $this->session->get('currentPlayer');
        
        for ($row = self::ROWS - 1; $row >= 0; $row--) {
            if ($this->board[$row][$column] === null) {
                $this->board[$row][$column] = $currentPlayer;
                $this->session->set('board', $this->board);
                $this->session->set('currentPlayer', $currentPlayer === 'red' ? 'yellow' : 'red');
                break;
            }
        }

        return $this->json([
            'board' => $this->board,
            'currentPlayer' => $this->session->get('currentPlayer'),
            'winner' => $this->checkWinner(),
        ]);
    }

    #[Route('/reset', name: 'game_reset')]
    public function reset(): JsonResponse
    {
        $this->session->remove('board');
        $this->session->remove('currentPlayer');
        $this->initializeBoard();
        
        return $this->json([
            'board' => $this->board,
            'currentPlayer' => $this->session->get('currentPlayer'),
            'winner' => null,
        ]);
    }

    private function checkWinner(): ?string
    {
        // Vérification horizontale
        for ($row = 0; $row < self::ROWS; $row++) {
            for ($col = 0; $col < self::COLUMNS - 3; $col++) {
                if ($this->board[$row][$col] !== null &&
                    $this->board[$row][$col] === $this->board[$row][$col + 1] &&
                    $this->board[$row][$col] === $this->board[$row][$col + 2] &&
                    $this->board[$row][$col] === $this->board[$row][$col + 3]) {
                    return $this->board[$row][$col];
                }
            }
        }

        // Vérification verticale
        for ($col = 0; $col < self::COLUMNS; $col++) {
            for ($row = 0; $row < self::ROWS - 3; $row++) {
                if ($this->board[$row][$col] !== null &&
                    $this->board[$row][$col] === $this->board[$row + 1][$col] &&
                    $this->board[$row][$col] === $this->board[$row + 2][$col] &&
                    $this->board[$row][$col] === $this->board[$row + 3][$col]) {
                    return $this->board[$row][$col];
                }
            }
        }

        // Vérification diagonale (descendante)
        for ($row = 0; $row < self::ROWS - 3; $row++) {
            for ($col = 0; $col < self::COLUMNS - 3; $col++) {
                if ($this->board[$row][$col] !== null &&
                    $this->board[$row][$col] === $this->board[$row + 1][$col + 1] &&
                    $this->board[$row][$col] === $this->board[$row + 2][$col + 2] &&
                    $this->board[$row][$col] === $this->board[$row + 3][$col + 3]) {
                    return $this->board[$row][$col];
                }
            }
        }

        // Vérification diagonale (montante)
        for ($row = 3; $row < self::ROWS; $row++) {
            for ($col = 0; $col < self::COLUMNS - 3; $col++) {
                if ($this->board[$row][$col] !== null &&
                    $this->board[$row][$col] === $this->board[$row - 1][$col + 1] &&
                    $this->board[$row][$col] === $this->board[$row - 2][$col + 2] &&
                    $this->board[$row][$col] === $this->board[$row - 3][$col + 3]) {
                    return $this->board[$row][$col];
                }
            }
        }

        return null;
    }
} 