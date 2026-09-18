<?php
session_start();

// 初始化棋盘（15x15）
if (!isset($_SESSION['board']) || isset($_GET['reset'])) {
    $_SESSION['board'] = array_fill(0, 15, array_fill(0, 15, ''));
    $_SESSION['current_player'] = 'black'; // 黑方先手
    $_SESSION['message'] = '黑方先行';
    $_SESSION['game_over'] = false;
    $_SESSION['winner'] = '';
}

$board = $_SESSION['board'];
$currentPlayer = $_SESSION['current_player'];
$message = $_SESSION['message'] ?? '';
$gameOver = $_SESSION['game_over'] ?? false;
$winner = $_SESSION['winner'] ?? '';

// 检查是否连成五子
function checkWin($board, $row, $col, $player) {
    // 四个方向：横、竖、左斜、右斜
    $directions = [
        [[0, 1], [0, -1]],   // 横向
        [[1, 0], [-1, 0]],   // 纵向
        [[1, 1], [-1, -1]],  // 左上到右下
        [[1, -1], [-1, 1]]   // 右上到左下
    ];
    
    foreach ($directions as $direction) {
        $count = 1; // 当前位置算一个
        
        // 检查两个方向
        foreach ($direction as $dir) {
            $r = $row + $dir[0];
            $c = $col + $dir[1];
            
            while ($r >= 0 && $r < 15 && $c >= 0 && $c < 15 && $board[$r][$c] === $player) {
                $count++;
                $r += $dir[0];
                $c += $dir[1];
            }
        }
        
        if ($count >= 5) {
            return true;
        }
    }
    
    return false;
}

// 处理下棋
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$gameOver) {
    $row = intval($_POST['row'] ?? -1);
    $col = intval($_POST['col'] ?? -1);
    
    if ($row >= 0 && $row < 15 && $col >= 0 && $col < 15) {
        // 检查位置是否为空
        if ($board[$row][$col] === '') {
            // 下棋
            $board[$row][$col] = $currentPlayer;
            $_SESSION['board'] = $board;
            
            // 检查是否获胜
            if (checkWin($board, $row, $col, $currentPlayer)) {
                $_SESSION['game_over'] = true;
                $_SESSION['winner'] = $currentPlayer;
                $_SESSION['message'] = ($currentPlayer === 'black' ? '黑方' : '白方') . '获胜！';
            } else {
                // 切换玩家
                $_SESSION['current_player'] = ($currentPlayer === 'black') ? 'white' : 'black';
                $_SESSION['message'] = ($_SESSION['current_player'] === 'black' ? '黑方' : '白方') . '下棋';
            }
            
            header('Location: game1.php');
            exit;
        } else {
            $_SESSION['message'] = '该位置已有棋子！';
        }
    }
}

$board = $_SESSION['board'];
$currentPlayer = $_SESSION['current_player'];
$message = $_SESSION['message'] ?? '';
$gameOver = $_SESSION['game_over'] ?? false;
$winner = $_SESSION['winner'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>五子棋对弈</title>
    <style>
        :root {
            --bg: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --board-bg: #dcb35c;
            --line: #000000;
            --black: #000000;
            --white: #ffffff;
            --selected: #fbbf24;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0b1220;
                --text: #e5e7eb;
                --muted: #94a3b8;
            }
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            width: 100%;
        }
        h1 { margin: 0 0 8px; font-size: 20px; }
        @media (min-width: 640px) {
            body { padding: 20px; }
            .header { margin-bottom: 20px; }
            h1 { font-size: 28px; }
        }
        .status {
            padding: 10px 16px;
            background: var(--board-bg);
            border-radius: 8px;
            margin: 10px 0;
            font-weight: 600;
        }
        .board-container {
            background: var(--board-bg);
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative;
            max-width: 100%;
            width: 100%;
        }
        @media (min-width: 640px) {
            .board-container { 
                padding: 30px;
                max-width: 560px;
            }
        }
        .board {
            position: relative;
            width: 100%;
            max-width: 500px;
            padding-bottom: 100%; /* 1:1 比例 */
            background: var(--board-bg);
            margin: 0 auto;
        }
        .board-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        .board-lines {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .h-line {
            position: absolute;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--line);
        }
        .v-line {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 1px;
            background: var(--line);
        }
        .star-point {
            position: absolute;
            width: 3%;
            height: 3%;
            background: var(--line);
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }
        .cell {
            position: absolute;
            width: 8%;
            padding-bottom: 8%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }
        .cell:hover:not(.has-piece) {
            background: rgba(251, 191, 36, 0.2);
        }
        .piece {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            height: 90%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            user-select: none;
            box-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .piece.black {
            background: radial-gradient(circle at 30% 30%, #4a4a4a, #000000);
            border: 1px solid #000;
        }
        .piece.white {
            background: radial-gradient(circle at 30% 30%, #ffffff, #e0e0e0);
            border: 1px solid #999;
        }
        .controls {
            margin-top: 15px;
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
            width: 100%;
        }
        @media (min-width: 640px) {
            .controls { margin-top: 20px; gap: 10px; }
        }
        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            touch-action: manipulation;
        }
        @media (min-width: 640px) {
            .btn { padding: 10px 20px; font-size: 16px; }
        }
        .btn:hover {
            background: #1d4ed8;
        }
        .btn-secondary {
            background: #64748b;
        }
        .btn-secondary:hover {
            background: #475569;
        }
        .message {
            margin-top: 10px;
            padding: 8px 12px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 6px;
            font-size: 13px;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
        }
        @media (min-width: 640px) {
            .message { font-size: 14px; }
        }
        .game-over-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }
        .game-over-box {
            background: var(--bg);
            padding: 30px 20px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            max-width: 90%;
            width: 100%;
        }
        @media (min-width: 640px) {
            .game-over-box {
                padding: 40px;
                max-width: 400px;
            }
        }
        .game-over-box h2 {
            margin: 0 0 15px;
            font-size: 24px;
            color: #2563eb;
        }
        @media (min-width: 640px) {
            .game-over-box h2 {
                margin: 0 0 20px;
                font-size: 32px;
            }
        }
        .game-over-box p {
            margin: 0 0 20px;
            font-size: 16px;
            color: var(--muted);
        }
        @media (min-width: 640px) {
            .game-over-box p {
                margin: 0 0 30px;
                font-size: 18px;
            }
        }
        .winner-piece {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            vertical-align: middle;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <?php if ($gameOver): ?>
    <div class="game-over-overlay">
        <div class="game-over-box">
            <h2>🎉 游戏结束</h2>
            <p>
                <?php if ($winner === 'black'): ?>
                    <span style="display: inline-flex; align-items: center;">
                        <span class="winner-piece" style="background: radial-gradient(circle at 30% 30%, #4a4a4a, #000000); border: 1px solid #000;"></span>
                        黑方获胜！
                    </span>
                <?php else: ?>
                    <span style="display: inline-flex; align-items: center;">
                        <span class="winner-piece" style="background: radial-gradient(circle at 30% 30%, #ffffff, #e0e0e0); border: 1px solid #999;"></span>
                        白方获胜！
                    </span>
                <?php endif; ?>
            </p>
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                <a href="game1.php?reset=1" class="btn">重新开始</a>
                <a href="index.php" class="btn btn-secondary">返回主页</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="header">
        <h1>⚫⚪ 五子棋对弈</h1>
        <div class="status">
            当前：<?php if ($currentPlayer === 'black'): ?>
                <span style="display: inline-flex; align-items: center; gap: 5px;">
                    <span style="display: inline-block; width: 16px; height: 16px; background: radial-gradient(circle at 30% 30%, #4a4a4a, #000000); border-radius: 50%; border: 1px solid #000;"></span>
                    黑方
                </span>
            <?php else: ?>
                <span style="display: inline-flex; align-items: center; gap: 5px;">
                    <span style="display: inline-block; width: 16px; height: 16px; background: radial-gradient(circle at 30% 30%, #ffffff, #e0e0e0); border-radius: 50%; border: 1px solid #999;"></span>
                    白方
                </span>
            <?php endif; ?>
        </div>
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    </div>

    <div class="board-container">
        <div class="board" id="board">
            <div class="board-wrapper">
                <!-- 绘制棋盘线 -->
                <div class="board-lines">
                    <?php for ($i = 0; $i < 15; $i++): ?>
                        <div class="h-line" style="top: <?= $i * 100 / 14 ?>%;"></div>
                        <div class="v-line" style="left: <?= $i * 100 / 14 ?>%;"></div>
                    <?php endfor; ?>
                    
                    <!-- 星位（天元和四个角星） -->
                    <div class="star-point" style="left: 50%; top: 50%;"></div>
                    <div class="star-point" style="left: <?= 3 * 100 / 14 ?>%; top: <?= 3 * 100 / 14 ?>%;"></div>
                    <div class="star-point" style="left: <?= 11 * 100 / 14 ?>%; top: <?= 3 * 100 / 14 ?>%;"></div>
                    <div class="star-point" style="left: <?= 3 * 100 / 14 ?>%; top: <?= 11 * 100 / 14 ?>%;"></div>
                    <div class="star-point" style="left: <?= 11 * 100 / 14 ?>%; top: <?= 11 * 100 / 14 ?>%;"></div>
                </div>
                
                <!-- 棋子放在交叉点上 -->
                <?php for ($row = 0; $row < 15; $row++): ?>
                    <?php for ($col = 0; $col < 15; $col++): ?>
                        <div class="cell <?= $board[$row][$col] !== '' ? 'has-piece' : '' ?>" 
                             data-row="<?= $row ?>" 
                             data-col="<?= $col ?>" 
                             style="left: <?= $col * 100 / 14 ?>%; top: <?= $row * 100 / 14 ?>%;">
                            <?php if ($board[$row][$col] !== ''): ?>
                                <div class="piece <?= $board[$row][$col] ?>"></div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="controls">
        <a href="index.php" class="btn btn-secondary">返回主页</a>
        <a href="game1.php?reset=1" class="btn">重新开始</a>
    </div>

    <form id="moveForm" method="post" style="display: none;">
        <input type="hidden" name="row" id="row">
        <input type="hidden" name="col" id="col">
    </form>

    <script>
        const cells = document.querySelectorAll('.cell');
        const gameOver = <?= $gameOver ? 'true' : 'false' ?>;
        
        cells.forEach(cell => {
            cell.addEventListener('click', function() {
                if (gameOver) return;
                if (this.classList.contains('has-piece')) return;
                
                const row = parseInt(this.dataset.row);
                const col = parseInt(this.dataset.col);
                
                document.getElementById('row').value = row;
                document.getElementById('col').value = col;
                document.getElementById('moveForm').submit();
            });
        });
    </script>
</body>
</html>

