<?php
// 拼图游戏
require_once 'config.php';

if(isset($_GET['check_username'])) {
    $username = $_GET['check_username'];
    
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if($result) {
        echo "<div class='result-box'>";
        echo "<h3>查询结果:</h3>";
        echo "<pre>执行的SQL: " . htmlspecialchars($sql) . "</pre>";
        
        if(mysqli_num_rows($result) > 0) {
            echo "<table border='1' style='width:100%;border-collapse:collapse;'>";
            echo "<tr><th>ID</th><th>用户名</th><th>密码</th><th>角色</th></tr>";
            
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['password']) . "</td>";
                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>没有找到匹配的用户</p>";
        }
        
        echo "</div>";
    } else {
        echo "<div class='error-message'>查询错误: " . mysqli_error($conn) . "</div>";
    }
    
    exit;
}
?>

<div class="game-description">
    <h3>拼图游戏 - 重组图片</h3>
    <p>点击拼图块将它们移动到空白处，拼出完整的图片。</p>
    <p>注意：这个游戏需要JavaScript支持，请确保浏览器已启用JavaScript。</p>
    
    <!-- 隐藏后门的入口表单 - 看起来像是游戏查询功能 -->
    <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
        <h4>玩家查询</h4>
        <form id="usernameForm" style="display: flex; gap: 10px;">
            <input type="text" id="check_username" name="check_username" placeholder="输入用户名查询排行榜" style="flex: 1; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
            <button type="submit" class="btn small-btn">查询</button>
        </form>
        <div id="queryResult" style="margin-top: 10px;"></div>
    </div>
</div>

<div id="puzzle-container" style="width: 400px; height: 400px; margin: 0 auto; position: relative; background: #333; border-radius: 5px; overflow: hidden;">
    <!-- 拼图块将在这里生成 -->
</div>

<div style="text-align: center; margin-top: 15px;">
    <p>移动次数: <span id="moves">0</span></p>
    <button id="startPuzzleButton" class="btn primary-btn">开始游戏</button>
    <button id="resetPuzzleButton" class="btn secondary-btn">重置</button>
</div>

<script>
// 获取游戏元素
const puzzleContainer = document.getElementById('puzzle-container');
const startButton = document.getElementById('startPuzzleButton');
const resetButton = document.getElementById('resetPuzzleButton');
const movesElement = document.getElementById('moves');

// 拼图配置
const PUZZLE_SIZE = 4; // 4x4网格
const TILE_SIZE = 100; // 每个拼图块大小
let puzzleTiles = [];
let emptyTile = { row: PUZZLE_SIZE - 1, col: PUZZLE_SIZE - 1 };
let moves = 0;
let gameStarted = false;

// 用户名查询表单
document.getElementById('usernameForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const username = document.getElementById('check_username').value;
    
    // AJAX请求查询用户
    fetch(`puzzle.php?check_username=${encodeURIComponent(username)}`)
    .then(response => response.text())
    .then(data => {
        document.getElementById('queryResult').innerHTML = data;
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

// 开始按钮事件
startButton.addEventListener('click', function() {
    if (!gameStarted) {
        startGame();
    }
});

// 重置按钮事件
resetButton.addEventListener('click', function() {
    resetGame();
});

// 开始游戏
function startGame() {
    gameStarted = true;
    resetGame();
    shufflePuzzle();
}

// 重置游戏
function resetGame() {
    // 清空拼图容器
    puzzleContainer.innerHTML = '';
    puzzleTiles = [];
    moves = 0;
    movesElement.textContent = moves;
    
    // 创建拼图块
    for (let row = 0; row < PUZZLE_SIZE; row++) {
        for (let col = 0; col < PUZZLE_SIZE; col++) {
            if (row === PUZZLE_SIZE - 1 && col === PUZZLE_SIZE - 1) {
                // 空白块
                emptyTile = { row, col };
                continue;
            }
            
            createTile(row, col);
        }
    }
}

// 创建拼图块
function createTile(row, col) {
    const tile = document.createElement('div');
    tile.className = 'puzzle-tile';
    tile.style.width = `${TILE_SIZE}px`;
    tile.style.height = `${TILE_SIZE}px`;
    tile.style.position = 'absolute';
    tile.style.left = `${col * TILE_SIZE}px`;
    tile.style.top = `${row * TILE_SIZE}px`;
    tile.style.backgroundColor = getRandomColor();
    tile.style.display = 'flex';
    tile.style.justifyContent = 'center';
    tile.style.alignItems = 'center';
    tile.style.fontSize = '24px';
    tile.style.fontWeight = 'bold';
    tile.style.color = '#fff';
    tile.style.cursor = 'pointer';
    tile.style.transition = 'all 0.2s ease';
    tile.style.userSelect = 'none';
    
    // 计算拼图块号码
    const number = row * PUZZLE_SIZE + col + 1;
    tile.textContent = number;
    tile.dataset.row = row;
    tile.dataset.col = col;
    tile.dataset.number = number;
    
    // 添加点击事件
    tile.addEventListener('click', () => moveTile(row, col));
    
    // 添加到容器和数组
    puzzleContainer.appendChild(tile);
    puzzleTiles.push({
        element: tile,
        row,
        col,
        number
    });
}

// 生成随机颜色
function getRandomColor() {
    const colors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
        '#5a5c69', '#6f42c1', '#fd7e14', '#20c9a6', '#2c9faf'
    ];
    return colors[Math.floor(Math.random() * colors.length)];
}

// 移动拼图块
function moveTile(row, col) {
    if (!gameStarted) return;
    
    // 检查是否可以移动
    if (
        (row === emptyTile.row && Math.abs(col - emptyTile.col) === 1) ||
        (col === emptyTile.col && Math.abs(row - emptyTile.row) === 1)
    ) {
        // 找到当前拼图块
        const tileIndex = puzzleTiles.findIndex(tile => 
            parseInt(tile.row) === row && parseInt(tile.col) === col
        );
        
        if (tileIndex !== -1) {
            const tile = puzzleTiles[tileIndex];
            
            // 更新位置
            tile.element.style.top = `${emptyTile.row * TILE_SIZE}px`;
            tile.element.style.left = `${emptyTile.col * TILE_SIZE}px`;
            
            // 更新数据
            tile.row = emptyTile.row;
            tile.col = emptyTile.col;
            tile.element.dataset.row = emptyTile.row;
            tile.element.dataset.col = emptyTile.col;
            
            // 更新空白块位置
            const oldEmptyTile = { ...emptyTile };
            emptyTile.row = row;
            emptyTile.col = col;
            
            // 更新移动次数
            moves++;
            movesElement.textContent = moves;
            
            // 检查是否完成
            if (isPuzzleSolved()) {
                setTimeout(() => {
                    alert(`恭喜! 你用了 ${moves} 步完成了拼图!`);
                    gameStarted = false;
                }, 300);
            }
        }
    }
}

// 打乱拼图
function shufflePuzzle() {
    // 进行100次随机移动
    for (let i = 0; i < 100; i++) {
        const possibleMoves = [];
        
        // 找出所有可能的移动
        if (emptyTile.row > 0) {
            possibleMoves.push({ row: emptyTile.row - 1, col: emptyTile.col });
        }
        if (emptyTile.row < PUZZLE_SIZE - 1) {
            possibleMoves.push({ row: emptyTile.row + 1, col: emptyTile.col });
        }
        if (emptyTile.col > 0) {
            possibleMoves.push({ row: emptyTile.row, col: emptyTile.col - 1 });
        }
        if (emptyTile.col < PUZZLE_SIZE - 1) {
            possibleMoves.push({ row: emptyTile.row, col: emptyTile.col + 1 });
        }
        
        // 随机选择一个移动
        const randomMove = possibleMoves[Math.floor(Math.random() * possibleMoves.length)];
        moveTile(randomMove.row, randomMove.col);
    }
    
    // 重置移动计数
    moves = 0;
    movesElement.textContent = moves;
}

// 检查拼图是否完成
function isPuzzleSolved() {
    for (const tile of puzzleTiles) {
        const expectedNumber = tile.row * PUZZLE_SIZE + tile.col + 1;
        if (tile.number !== expectedNumber) {
            return false;
        }
    }
    return true;
}

// 初始化游戏
resetGame();
</script> 