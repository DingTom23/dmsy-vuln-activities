document.addEventListener('DOMContentLoaded', function() {
    // 侧边栏切换
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if(sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            
            if(sidebar.classList.contains('active')) {
                mainContent.style.marginLeft = '280px';
            } else {
                mainContent.style.marginLeft = '0';
            }
        });
    }
    
    // 响应式调整
    function checkWidth() {
        if(window.innerWidth <= 992) {
            sidebar.classList.remove('active');
            mainContent.style.marginLeft = '0';
        } else {
            sidebar.classList.add('active');
            mainContent.style.marginLeft = '280px';
        }
    }
    
    window.addEventListener('resize', checkWidth);
    checkWidth(); // 初始检查
    
    // 通知和消息下拉
    const notificationEl = document.querySelector('.notification');
    const messagesEl = document.querySelector('.messages');
    
    if(notificationEl) {
        notificationEl.addEventListener('click', function(e) {
            e.stopPropagation();
            // 此处可添加通知下拉菜单的逻辑
            console.log('通知被点击');
        });
    }
    
    if(messagesEl) {
        messagesEl.addEventListener('click', function(e) {
            e.stopPropagation();
            // 此处可添加消息下拉菜单的逻辑
            console.log('消息被点击');
        });
    }
    
    // 点击文档其他地方关闭下拉菜单
    document.addEventListener('click', function() {
        // 关闭所有下拉菜单的逻辑
    });
    
    // 快捷操作按钮动画
    const quickActionBtns = document.querySelectorAll('.quick-action-btn');
    
    quickActionBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}); 