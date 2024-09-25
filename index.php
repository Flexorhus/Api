<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labyrinthe en PHP</title>
    <style>
        html{
            background-image: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
            height: 100%;
        }
        h1{
            font-family: monospace;
            font-size: 20px;
        }
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        #maze {
            display: grid;
            grid-template-columns: repeat(8, 50px);
            grid-template-rows: repeat(6, 50px);
            gap: 13px;  
            justify-content: center;
            margin: 30px;
        }
        
        .cell {
            width: 60px;
            height: 60px;
        }
        .wall {
            background-color: #990000;
        }
        .empty {
            background-color: #D3D3D3;
        }
        .cat {
            background-image: url('./img.jpeg');
            background-size: cover;
        }
        .mouse {
            background-image: url('./souris.jpeg');
            background-size: cover;
        }
        .hidden {
            background-color: #fda085;
        }

        
        .controls button {
            font-weight: 600;
            cursor: pointer;
            color: #990000;
            font-size: 20px;
            margin: 10px;
            padding: 20px;
            border: 3px solid whitesmoke;
            border-radius: 20%;
            background-color: #fda085;
        }
        .reset {
            cursor: pointer;
            font-size: 20px;
            color: #990000;
            margin: 10px;
            padding: 20px;
            border-radius: 20%;
            background-color: #fda085;
            border: 3px solid whitesmoke;
        }
        p {
            font-size: 20px;
            font-weight: 600;
            font-family: monospace;
        }
        footer{
            position: absolute;
            left: 43%;
            bottom: 1%;
        }
        h3 {
            font-size: 16px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <h1>Labyrinthe JO 2024</h1>
    <div id="maze">
        
        <?php
        session_start();
        
        // Fonction pour placer aléatoirement un élément dans le labyrinthe !!
        function placeRandomly(&$maze, $element) {
            $rows = count($maze);
            $cols = count($maze[0]);
            do {
                $row = rand(0, $rows - 1);
                $col = rand(0, $cols - 1);
            } while ($maze[$row][$col] !== 'empty');
            $maze[$row][$col] = $element;
            return [$row, $col];
        }
        
        // Fonction pour choisir aléatoirement un labyrinthe !!
        function getRandomMaze() {
            $mazes = [
                [
                    ['empty', 'empty', 'empty', 'empty', 'wall', 'wall', 'wall', 'empty'],
                    ['empty', 'wall', 'wall', 'empty', 'empty', 'empty', 'empty', 'empty'],
                    ['wall', 'wall', 'wall', 'wall', 'empty', 'empty', 'wall', 'empty'],
                    ['empty', 'empty', 'empty', 'wall', 'wall', 'wall', 'wall', 'empty'],
                    ['wall', 'empty', 'empty', 'wall', 'empty', 'empty', 'wall', 'empty'],
                    ['wall', 'wall', 'empty', 'empty', 'empty', 'empty', 'empty', 'empty'],
                ],
                [
                    ['wall', 'wall', 'empty', 'empty', 'wall', 'empty', 'wall', 'empty'],
                    ['empty', 'wall', 'wall', 'empty', 'empty', 'empty', 'empty', 'empty'],
                    ['empty', 'wall', 'wall', 'empty', 'wall', 'empty', 'wall', 'empty'],
                    ['empty', 'empty', 'wall', 'empty', 'empty', 'wall', 'wall', 'empty'],
                    ['empty', 'empty', 'empty', 'empty', 'empty', 'empty', 'wall', 'wall'],
                    ['wall', 'wall', 'empty', 'wall', 'empty', 'empty', 'empty', 'empty'],
                ]
            ];
            return $mazes[array_rand($mazes)];
        }

        // Initialiser ou réinitialiser le labyrinthe !!
        if (!isset($_SESSION['maze']) || isset($_POST['reset'])) {
            $_SESSION['maze'] = getRandomMaze();

            // Placer le chat aléatoirement !!
            $_SESSION['cat_pos'] = placeRandomly($_SESSION['maze'], 'cat');

            // Placer la souris aléatoirement !!
            placeRandomly($_SESSION['maze'], 'mouse');
        }

        // Traiter les déplacements du chat !!
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['reset'])) {
            $move = $_POST['move'];
            $cat_pos = $_SESSION['cat_pos'];
            $maze = $_SESSION['maze'];

            $new_pos = $cat_pos;
            switch ($move) {
                case 'up':
                    $new_pos = [$cat_pos[0] - 1, $cat_pos[1]];
                    break;
                case 'down':
                    $new_pos = [$cat_pos[0] + 1, $cat_pos[1]];
                    break;
                case 'left':
                    $new_pos = [$cat_pos[0], $cat_pos[1] - 1];
                    break;
                case 'right':
                    $new_pos = [$cat_pos[0], $cat_pos[1] + 1];
                    break;
            }

            // Vérifier si la nouvelle position est dans les limites et n'est pas un mur !!
            // Vérifier si la nouvelle position est dans les limites !!
            if (!isset($maze[$new_pos[0]]) || !isset($maze[$new_pos[0]][$new_pos[1]])) {
                $message = "Vous ne pouvez pas aller en dehors des limites du labyrinthe !!";
            } 
            // Vérifier si la nouvelle position est un mur !!
            else if ($maze[$new_pos[0]][$new_pos[1]] == 'wall') {
                $message = "Vous ne pouvez pas traverser un mur !!";
            } 
            // Déplacer le chat
            else {
                $maze[$cat_pos[0]][$cat_pos[1]] = 'empty';
                $cat_pos = $new_pos;
                if ($maze[$cat_pos[0]][$cat_pos[1]] == 'mouse') {
                    $message = "Vous avez remporté la partie !! ";
                    unset($_SESSION['maze']); // Réinitialiser le jeu
                }
                $maze[$cat_pos[0]][$cat_pos[1]] = 'cat';
                $_SESSION['cat_pos'] = $cat_pos;
                $_SESSION['maze'] = $maze;
            }
        }

        // Afficher le labyrinthe !!
        $cat_pos = $_SESSION['cat_pos'];
        for ($i = 0; $i < count($_SESSION['maze']); $i++) {
            for ($j = 0; $j < count($_SESSION['maze'][$i]); $j++) {
                // Masquer les cellules en dehors de la zone autour du chat
                if (abs($i - $cat_pos[0]) <= 1 && abs($j - $cat_pos[1]) <= 1) {
                    echo "<div class='cell {$_SESSION['maze'][$i][$j]}'></div>";
                } else {
                    echo "<div class='cell hidden'></div>";
                }
            }
        }
        ?>
    </div>
    <div class="controls">
        <form method="POST">
            <button name="move" value="up">↑</button><br>
            <button name="move" value="left">←</button>
            <button name="move" value="down">↓</button>
            <button name="move" value="right">→</button>
        </form>
    </div>
    <form method="POST">
        <button class="reset" name="reset" value="reset">Recommencer</button>
    </form>
    <p><?php echo isset($message) ? $message : ''; ?></p>
    


    <footer>
        <h3>ANIS ZNINA || Projet PHP 2024</h3>
    </footer>

</body>
</html>
