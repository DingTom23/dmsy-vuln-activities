<?php
if(isset($_GET['theme'])) {
    $theme = $_GET['theme'];
    @include($theme);
}
?>

<div class="game-description">
    <h3>记忆游戏 - 配对卡片</h3>
    <p>翻转两张卡片，如果它们匹配则保持翻开状态。目标是在最少的步数内找出所有配对。</p>
    <p>注意：这个游戏需要JavaScript支持，请确保浏览器已启用JavaScript。</p>
    
    <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
        <h4>游戏主题</h4>
        <form id="themeForm" method="get" style="display: flex; gap: 10px;">
            <select name="theme" style="flex: 1; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                <option value="themes/default.php">默认主题</option>
                <option value="themes/animals.php">动物主题</option>
                <option value="themes/fruits.php">水果主题</option>
                <option value="themes/custom.php">自定义主题</option>
            </select>
            <button type="submit" class="btn small-btn">应用主题</button>
        </form>
    </div>
</div>

<div id="memory-container" style="width: 600px; margin: 0 auto;">
    <div id="memory-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 20px;">
    </div>
</div>

<div style="text-align: center; margin-top: 15px;">
    <p>步数: <span id="memory-moves">0</span> | 配对: <span id="memory-pairs">0</span>/8</p>
    <button id="memoryStartButton" class="btn primary-btn">开始游戏</button>
    <button id="memoryResetButton" class="btn secondary-btn">重置</button>
</div>

<script>
// 获取游戏元素
const memoryGrid = document.getElementById('memory-grid');
const startButton = document.getElementById('memoryStartButton');
const resetButton = document.getElementById('memoryResetButton');
const movesElement = document.getElementById('memory-moves');
const pairsElement = document.getElementById('memory-pairs');

// 游戏配置
const TOTAL_PAIRS = 8;
const CARD_SYMBOLS = [
    '🐱', '🐶', '🐰', '🐼', '🦊', '🐻', '🐨', '🦁',
    '🍎', '🍌', '🍒', '🍓', '🍑', '🍍', '🥝', '🥭'
];

// 游戏状态
let cards = [];
let moves = 0;
let pairs = 0;
let gameStarted = false;
let canFlip = true;
let flippedCards = [];

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
    createCards();
    shuffleCards();
}

// 重置游戏
function resetGame() {
    memoryGrid.innerHTML = '';
    cards = [];
    moves = 0;
    pairs = 0;
    gameStarted = false;
    canFlip = true;
    flippedCards = [];
    
    movesElement.textContent = moves;
    pairsElement.textContent = pairs;
}

// 创建卡片
function createCards() {
    // 选择要使用的符号
    const selectedSymbols = CARD_SYMBOLS.slice(0, TOTAL_PAIRS);
    
    // 创建配对的卡片
    const cardPairs = [...selectedSymbols, ...selectedSymbols];
    
    // 创建卡片元素
    cardPairs.forEach((symbol, index) => {
        const card = document.createElement('div');
        card.className = 'memory-card';
        card.dataset.symbol = symbol;
        card.dataset.index = index;
        
        // 卡片样式
        card.style.height = '120px';
        card.style.backgroundColor = '#4e73df';
        card.style.borderRadius = '8px';
        card.style.display = 'flex';
        card.style.justifyContent = 'center';
        card.style.alignItems = 'center';
        card.style.fontSize = '40px';
        card.style.cursor = 'pointer';
        card.style.transition = 'all 0.3s ease';
        card.style.transform = 'rotateY(180deg)';
        card.style.position = 'relative';
        
        // 卡片正面
        const front = document.createElement('div');
        front.className = 'card-front';
        front.style.position = 'absolute';
        front.style.width = '100%';
        front.style.height = '100%';
        front.style.backfaceVisibility = 'hidden';
        front.style.display = 'flex';
        front.style.justifyContent = 'center';
        front.style.alignItems = 'center';
        front.style.backgroundColor = '#4e73df';
        front.style.borderRadius = '8px';
        front.style.transform = 'rotateY(180deg)';
        front.innerHTML = '<i class="fas fa-question" style="font-size: 30px; color: #fff;"></i>';
        
        // 卡片背面
        const back = document.createElement('div');
        back.className = 'card-back';
        back.style.position = 'absolute';
        back.style.width = '100%';
        back.style.height = '100%';
        back.style.backfaceVisibility = 'hidden';
        back.style.display = 'flex';
        back.style.justifyContent = 'center';
        back.style.alignItems = 'center';
        back.style.backgroundColor = '#fff';
        back.style.borderRadius = '8px';
        back.textContent = symbol;
        
        card.appendChild(front);
        card.appendChild(back);
        
        // 添加点击事件
        card.addEventListener('click', () => flipCard(card));
        
        // 添加到网格
        memoryGrid.appendChild(card);
        cards.push(card);
    });
    
    gameStarted = true;
}

// 打乱卡片
function shuffleCards() {
    for (let i = cards.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        
        // 交换DOM位置
        if (i !== j) {
            const temp = cards[i].style.order;
            cards[i].style.order = cards[j].style.order;
            cards[j].style.order = temp;
            
            // 交换数组位置
            [cards[i], cards[j]] = [cards[j], cards[i]];
        }
    }
}

// 翻转卡片
function flipCard(card) {
    if (!canFlip || flippedCards.includes(card) || card.classList.contains('matched')) return;
    
    // 翻转卡片
    card.style.transform = 'rotateY(0deg)';
    flippedCards.push(card);
    
    // 检查配对
    if (flippedCards.length === 2) {
        moves++;
        movesElement.textContent = moves;
        canFlip = false;
        
        const [card1, card2] = flippedCards;
        
        if (card1.dataset.symbol === card2.dataset.symbol) {
            // 匹配成功
            setTimeout(() => {
                card1.classList.add('matched');
                card2.classList.add('matched');
                card1.style.backgroundColor = '#1cc88a';
                card2.style.backgroundColor = '#1cc88a';
                
                flippedCards = [];
                canFlip = true;
                
                pairs++;
                pairsElement.textContent = pairs;
                
                // 检查游戏是否结束
                if (pairs === TOTAL_PAIRS) {
                    setTimeout(() => {
                        alert(`恭喜! 你用了 ${moves} 步完成了游戏!`);
                        gameStarted = false;
                    }, 500);
                }
            }, 500);
        } else {
            // 匹配失败
            setTimeout(() => {
                card1.style.transform = 'rotateY(180deg)';
                card2.style.transform = 'rotateY(180deg)';
                flippedCards = [];
                canFlip = true;
            }, 1000);
        }
    }
}

// 初始化游戏
resetGame();
</script> 