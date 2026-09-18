<?php
session_start();

// 定义棋子类型
$redPieces = ['帥', '仕', '相', '傌', '俥', '炮', '兵'];
$blackPieces = ['將', '士', '象', '馬', '車', '砲', '卒'];

// 初始化棋盘
if (!isset($_SESSION['board']) || isset($_GET['reset'])) {
    $_SESSION['board'] = [
        ['車', '馬', '象', '士', '將', '士', '象', '馬', '車'],
        ['', '', '', '', '', '', '', '', ''],
        ['', '砲', '', '', '', '', '', '砲', ''],
        ['卒', '', '卒', '', '卒', '', '卒', '', '卒'],
        ['', '', '', '', '', '', '', '', ''],
        ['', '', '', '', '', '', '', '', ''],
        ['兵', '', '兵', '', '兵', '', '兵', '', '兵'],
        ['', '炮', '', '', '', '', '', '炮', ''],
        ['', '', '', '', '', '', '', '', ''],
        ['俥', '傌', '相', '仕', '帥', '仕', '相', '傌', '俥'],
    ];
    $_SESSION['current_player'] = 'red';
    $_SESSION['message'] = '红方先行';
    $_SESSION['game_over'] = false;
    $_SESSION['winner'] = '';
}

// 检查棋子是否属于指定方
function isPlayerPiece($piece, $player) {
    global $redPieces, $blackPieces;
    if ($player === 'red') {
        return in_array($piece, $redPieces);
    } else {
        return in_array($piece, $blackPieces);
    }
}

// 找到将/帅的位置
function findKing($board, $player) {
    $king = ($player === 'red') ? '帥' : '將';
    for ($r = 0; $r < 10; $r++) {
        for ($c = 0; $c < 9; $c++) {
            if ($board[$r][$c] === $king) {
                return ['row' => $r, 'col' => $c];
            }
        }
    }
    return null;
}

// 检查将帅是否对面（白脸将）
function areKingsFacing($board) {
    $redKing = findKing($board, 'red');
    $blackKing = findKing($board, 'black');
    
    if (!$redKing || !$blackKing) {
        return false;
    }
    
    // 如果不在同一列，则不对面
    if ($redKing['col'] !== $blackKing['col']) {
        return false;
    }
    
    // 检查两个将/帅之间是否有其他棋子
    $col = $redKing['col'];
    $startRow = min($redKing['row'], $blackKing['row']) + 1;
    $endRow = max($redKing['row'], $blackKing['row']);
    
    for ($r = $startRow; $r < $endRow; $r++) {
        if ($board[$r][$col] !== '') {
            return false; // 中间有棋子，不算对面
        }
    }
    
    return true; // 同一列且中间没有棋子，对面了
}

// 检查移动是否合法（基础规则）
function isValidMove($board, $fromRow, $fromCol, $toRow, $toCol, $player) {
    global $redPieces, $blackPieces;
    
    $piece = $board[$fromRow][$fromCol];
    $target = $board[$toRow][$toCol];
    
    // 不能吃自己的棋子
    if (isPlayerPiece($target, $player)) {
        return false;
    }
    
    // 简化的移动规则验证
    $rowDiff = abs($toRow - $fromRow);
    $colDiff = abs($toCol - $fromCol);
    
    // 将/帅
    if ($piece === '帥' || $piece === '將') {
        // 只能在九宫内移动
        if ($player === 'red') {
            if ($toRow < 7 || $toRow > 9 || $toCol < 3 || $toCol > 5) return false;
        } else {
            if ($toRow < 0 || $toRow > 2 || $toCol < 3 || $toCol > 5) return false;
        }
        // 只能走一格（横或竖）
        return ($rowDiff === 1 && $colDiff === 0) || ($rowDiff === 0 && $colDiff === 1);
    }
    
    // 士/仕
    if ($piece === '仕' || $piece === '士') {
        if ($player === 'red') {
            if ($toRow < 7 || $toRow > 9 || $toCol < 3 || $toCol > 5) return false;
        } else {
            if ($toRow < 0 || $toRow > 2 || $toCol < 3 || $toCol > 5) return false;
        }
        return $rowDiff === 1 && $colDiff === 1;
    }
    
    // 相/象
    if ($piece === '相' || $piece === '象') {
        if ($player === 'red') {
            if ($toRow < 5) return false; // 不能过河
        } else {
            if ($toRow > 4) return false;
        }
        if ($rowDiff === 2 && $colDiff === 2) {
            // 检查象眼
            $eyeRow = ($fromRow + $toRow) / 2;
            $eyeCol = ($fromCol + $toCol) / 2;
            return $board[$eyeRow][$eyeCol] === '';
        }
        return false;
    }
    
    // 马/傌
    if ($piece === '傌' || $piece === '馬') {
        if (($rowDiff === 2 && $colDiff === 1) || ($rowDiff === 1 && $colDiff === 2)) {
            // 检查马腿
            if ($rowDiff === 2) {
                $legRow = $fromRow + ($toRow - $fromRow) / 2;
                return $board[$legRow][$fromCol] === '';
            } else {
                $legCol = $fromCol + ($toCol - $fromCol) / 2;
                return $board[$fromRow][$legCol] === '';
            }
        }
        return false;
    }
    
    // 车/俥
    if ($piece === '俥' || $piece === '車') {
        if ($rowDiff > 0 && $colDiff > 0) return false;
        // 检查路径是否有阻挡
        if ($rowDiff > 0) {
            $step = ($toRow > $fromRow) ? 1 : -1;
            for ($r = $fromRow + $step; $r != $toRow; $r += $step) {
                if ($board[$r][$fromCol] !== '') return false;
            }
        } else {
            $step = ($toCol > $fromCol) ? 1 : -1;
            for ($c = $fromCol + $step; $c != $toCol; $c += $step) {
                if ($board[$fromRow][$c] !== '') return false;
            }
        }
        return true;
    }
    
    // 炮/砲
    if ($piece === '炮' || $piece === '砲') {
        if ($rowDiff > 0 && $colDiff > 0) return false;
        $jumpCount = 0;
        if ($rowDiff > 0) {
            $step = ($toRow > $fromRow) ? 1 : -1;
            for ($r = $fromRow + $step; $r != $toRow; $r += $step) {
                if ($board[$r][$fromCol] !== '') $jumpCount++;
            }
        } else {
            $step = ($toCol > $fromCol) ? 1 : -1;
            for ($c = $fromCol + $step; $c != $toCol; $c += $step) {
                if ($board[$fromRow][$c] !== '') $jumpCount++;
            }
        }
        // 吃子需要跳一个，移动不能跳
        if ($target !== '') {
            return $jumpCount === 1;
        } else {
            return $jumpCount === 0;
        }
    }
    
    // 兵/卒
    if ($piece === '兵' || $piece === '卒') {
        if ($player === 'red') {
            // 未过河只能向前
            if ($fromRow > 4) {
                return $toRow === $fromRow - 1 && $toCol === $fromCol;
            } else {
                // 过河可以左右
                return ($toRow === $fromRow - 1 && $toCol === $fromCol) ||
                       ($toRow === $fromRow && $colDiff === 1);
            }
        } else {
            if ($fromRow < 5) {
                return $toRow === $fromRow + 1 && $toCol === $fromCol;
            } else {
                return ($toRow === $fromRow + 1 && $toCol === $fromCol) ||
                       ($toRow === $fromRow && $colDiff === 1);
            }
        }
    }
    
    return false;
}

// 检查是否被将军
function isInCheck($board, $player) {
    $kingPos = findKing($board, $player);
    if (!$kingPos) return false;
    
    $opponent = ($player === 'red') ? 'black' : 'red';
    
    // 检查对方所有棋子是否能攻击到己方将/帅
    for ($r = 0; $r < 10; $r++) {
        for ($c = 0; $c < 9; $c++) {
            $piece = $board[$r][$c];
            if ($piece !== '' && isPlayerPiece($piece, $opponent)) {
                if (isValidMove($board, $r, $c, $kingPos['row'], $kingPos['col'], $opponent)) {
                    return true;
                }
            }
        }
    }
    
    return false;
}

// 检查移动后是否仍被将军（用于验证应将是否有效）
function wouldBeInCheck($board, $fromRow, $fromCol, $toRow, $toCol, $player) {
    // 模拟移动
    $tempBoard = $board;
    $tempBoard[$toRow][$toCol] = $tempBoard[$fromRow][$fromCol];
    $tempBoard[$fromRow][$fromCol] = '';
    
    // 检查将帅是否对面
    if (areKingsFacing($tempBoard)) {
        return true; // 将帅对面视为被将军（违规）
    }
    
    return isInCheck($tempBoard, $player);
}

// 检查是否被绝杀
function isCheckmate($board, $player) {
    // 必须先被将军
    if (!isInCheck($board, $player)) {
        return false;
    }
    
    // 尝试所有可能的移动，看是否能解除将军
    for ($fromRow = 0; $fromRow < 10; $fromRow++) {
        for ($fromCol = 0; $fromCol < 9; $fromCol++) {
            $piece = $board[$fromRow][$fromCol];
            if ($piece !== '' && isPlayerPiece($piece, $player)) {
                // 尝试移动到所有位置
                for ($toRow = 0; $toRow < 10; $toRow++) {
                    for ($toCol = 0; $toCol < 9; $toCol++) {
                        if ($fromRow === $toRow && $fromCol === $toCol) continue;
                        
                        if (isValidMove($board, $fromRow, $fromCol, $toRow, $toCol, $player)) {
                            // 检查移动后是否还被将军
                            if (!wouldBeInCheck($board, $fromRow, $fromCol, $toRow, $toCol, $player)) {
                                return false; // 找到了解救方法
                            }
                        }
                    }
                }
            }
        }
    }
    
    return true; // 无法解救，被绝杀
}

$board = $_SESSION['board'];
$currentPlayer = $_SESSION['current_player'];
$message = $_SESSION['message'] ?? '';
$gameOver = $_SESSION['game_over'] ?? false;
$winner = $_SESSION['winner'] ?? '';

// 处理走棋
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$gameOver) {
    $fromRow = intval($_POST['from_row'] ?? -1);
    $fromCol = intval($_POST['from_col'] ?? -1);
    $toRow = intval($_POST['to_row'] ?? -1);
    $toCol = intval($_POST['to_col'] ?? -1);

    if ($fromRow >= 0 && $fromRow < 10 && $fromCol >= 0 && $fromCol < 9 &&
        $toRow >= 0 && $toRow < 10 && $toCol >= 0 && $toCol < 9) {
        
        $piece = $board[$fromRow][$fromCol];
        
        // 检查是否是当前玩家的棋子
        if (isPlayerPiece($piece, $currentPlayer)) {
            // 检查移动是否合法
            if (isValidMove($board, $fromRow, $fromCol, $toRow, $toCol, $currentPlayer)) {
                // 模拟移动检查将帅对面
                $tempBoard = $board;
                $tempBoard[$toRow][$toCol] = $tempBoard[$fromRow][$fromCol];
                $tempBoard[$fromRow][$fromCol] = '';
                
                if (areKingsFacing($tempBoard)) {
                    $_SESSION['message'] = '将帅不能对面！';
                } elseif (wouldBeInCheck($board, $fromRow, $fromCol, $toRow, $toCol, $currentPlayer)) {
                    $_SESSION['message'] = '此移动会让自己被将军！必须应将！';
                } else {
                    // 执行移动
                    $board[$toRow][$toCol] = $piece;
                    $board[$fromRow][$fromCol] = '';
                    $_SESSION['board'] = $board;
                    
                    // 切换玩家
                    $nextPlayer = ($currentPlayer === 'red') ? 'black' : 'red';
                    $_SESSION['current_player'] = $nextPlayer;
                    
                    // 检查对方是否被将军
                    if (isCheckmate($board, $nextPlayer)) {
                        $_SESSION['game_over'] = true;
                        $_SESSION['winner'] = $currentPlayer;
                        $_SESSION['message'] = '绝杀！' . ($currentPlayer === 'red' ? '红方' : '黑方') . '获胜！';
                    } elseif (isInCheck($board, $nextPlayer)) {
                        $_SESSION['message'] = ($nextPlayer === 'red' ? '红方' : '黑方') . '被将军！必须应将！';
                    } else {
                        $_SESSION['message'] = ($nextPlayer === 'red' ? '红方' : '黑方') . '走棋';
                    }
                    
                    header('Location: game.php');
                    exit;
                }
            } else {
                $_SESSION['message'] = '移动不符合规则！';
            }
        } else {
            $_SESSION['message'] = '请选择自己的棋子！';
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
    <title>中国象棋对弈</title>
    <style>
        :root {
            --bg: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --board-bg: #f4e4c1;
            --line: #8b4513;
            --red: #dc2626;
            --black: #1e293b;
            --selected: #fbbf24;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0b1220;
                --text: #e5e7eb;
                --muted: #94a3b8;
                --board-bg: #3e2723;
                --line: #d4a574;
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
                padding: 40px;
                max-width: 560px;
            }
        }
        .board {
            position: relative;
            width: 100%;
            max-width: 480px;
            padding-bottom: 112.5%; /* 9/8 比例 (540/480) */
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
            height: 2px;
            background: var(--line);
        }
        .v-line {
            position: absolute;
            top: 0;
            width: 2px;
            background: var(--line);
        }
        .cell {
            position: absolute;
            width: 10%;
            padding-bottom: 10%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }
        .cell:hover {
            background: rgba(251, 191, 36, 0.15);
        }
        .cell.selected {
            background: rgba(251, 191, 36, 0.4);
        }
        .piece {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 85%;
            height: 85%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(14px, 4vw, 20px);
            font-weight: bold;
            border: 2px solid;
            cursor: pointer;
            user-select: none;
        }
        .piece.red {
            background: #fff;
            color: var(--red);
            border-color: var(--red);
        }
        .piece.black {
            background: #fff;
            color: var(--black);
            border-color: var(--black);
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
        .message.check {
            background: #fee2e2;
            color: #991b1b;
            font-weight: 600;
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
            color: var(--red);
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
    </style>
</head>
<body>
    <?php if ($gameOver): ?>
    <div class="game-over-overlay">
        <div class="game-over-box">
            <h2>🎉 游戏结束</h2>
            <p><?= $winner === 'red' ? '<span style="color: var(--red);">红方</span>' : '<span style="color: var(--black);">黑方</span>' ?> 获胜！</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="game.php?reset=1" class="btn">重新开始</a>
                <a href="index.php" class="btn btn-secondary">返回主页</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="header">
        <h1>🎮 中国象棋对弈</h1>
        <div class="status">
            当前：<?= $currentPlayer === 'red' ? '<span style="color: var(--red);">红方</span>' : '<span style="color: var(--black);">黑方</span>' ?>
        </div>
        <?php if ($message): ?>
            <div class="message <?= strpos($message, '将军') !== false ? 'check' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="board-container">
        <div class="board" id="board">
            <div class="board-wrapper">
                <!-- 绘制棋盘线 -->
                <div class="board-lines">
                    <?php for ($i = 0; $i < 10; $i++): ?>
                        <div class="h-line" style="top: <?= $i * 11.111 ?>%;"></div>
                    <?php endfor; ?>
                    <?php for ($i = 0; $i < 9; $i++): ?>
                        <?php if ($i == 0 || $i == 8): ?>
                            <!-- 边线全长 -->
                            <div class="v-line" style="left: <?= $i * 12.5 ?>%; height: 100%;"></div>
                        <?php else: ?>
                            <!-- 中间线分两段（楚河汉界） -->
                            <div class="v-line" style="left: <?= $i * 12.5 ?>%; height: 44.444%;"></div>
                            <div class="v-line" style="left: <?= $i * 12.5 ?>%; top: 55.556%; height: 44.444%;"></div>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <!-- 九宫斜线 - 上方（黑方） -->
                    <svg style="position: absolute; top: 0; left: 37.5%; width: 25%; height: 22.222%; pointer-events: none;">
                        <line x1="0" y1="0" x2="100%" y2="100%" stroke="var(--line)" stroke-width="2"/>
                        <line x1="100%" y1="0" x2="0" y2="100%" stroke="var(--line)" stroke-width="2"/>
                    </svg>
                    <!-- 九宫斜线 - 下方（红方） -->
                    <svg style="position: absolute; top: 77.778%; left: 37.5%; width: 25%; height: 22.222%; pointer-events: none;">
                        <line x1="0" y1="0" x2="100%" y2="100%" stroke="var(--line)" stroke-width="2"/>
                        <line x1="100%" y1="0" x2="0" y2="100%" stroke="var(--line)" stroke-width="2"/>
                    </svg>
                </div>
                
                <!-- 棋子放在交叉点上 -->
                <?php for ($row = 0; $row < 10; $row++): ?>
                    <?php for ($col = 0; $col < 9; $col++): ?>
                        <div class="cell" data-row="<?= $row ?>" data-col="<?= $col ?>" 
                             style="left: <?= $col * 12.5 ?>%; top: <?= $row * 11.111 ?>%;">
                            <?php
                            $piece = $board[$row][$col];
                            if ($piece !== '') {
                                $color = in_array($piece, $redPieces) ? 'red' : 'black';
                                echo '<div class="piece ' . $color . '">' . htmlspecialchars($piece) . '</div>';
                            }
                            ?>
                        </div>
                    <?php endfor; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="controls">
        <a href="index.php" class="btn btn-secondary">返回主页</a>
        <a href="game.php?reset=1" class="btn">重新开始</a>
    </div>

    <form id="moveForm" method="post" style="display: none;">
        <input type="hidden" name="from_row" id="from_row">
        <input type="hidden" name="from_col" id="from_col">
        <input type="hidden" name="to_row" id="to_row">
        <input type="hidden" name="to_col" id="to_col">
    </form>

    <script>
        let selectedCell = null;
        const cells = document.querySelectorAll('.cell');
        const gameOver = <?= $gameOver ? 'true' : 'false' ?>;
        
        cells.forEach(cell => {
            cell.addEventListener('click', function() {
                if (gameOver) return;
                
                const row = parseInt(this.dataset.row);
                const col = parseInt(this.dataset.col);
                const piece = this.querySelector('.piece');

                if (selectedCell === null) {
                    // 选择棋子
                    if (piece) {
                        selectedCell = { row, col, element: this };
                        this.classList.add('selected');
                    }
                } else {
                    // 移动棋子
                    if (selectedCell.row === row && selectedCell.col === col) {
                        // 取消选择
                        selectedCell.element.classList.remove('selected');
                        selectedCell = null;
                    } else {
                        // 提交移动
                        document.getElementById('from_row').value = selectedCell.row;
                        document.getElementById('from_col').value = selectedCell.col;
                        document.getElementById('to_row').value = row;
                        document.getElementById('to_col').value = col;
                        document.getElementById('moveForm').submit();
                    }
                }
            });
        });
    </script>
</body>
</html>
