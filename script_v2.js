// ========== 深色模式切换 ==========
const themeToggle = document.getElementById('theme-toggle');
const html = document.documentElement;

// 检测是否为移动设备
const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

// 检查本地存储的主题设置
const currentTheme = localStorage.getItem('theme') || 'light';
if (currentTheme === 'dark') {
	html.setAttribute('data-theme', 'dark');
	themeToggle.checked = true;
}

// 主题切换事件
themeToggle.addEventListener('change', function() {
	if (this.checked) {
		html.setAttribute('data-theme', 'dark');
		localStorage.setItem('theme', 'dark');
	} else {
		html.removeAttribute('data-theme');
		localStorage.setItem('theme', 'light');
	}
});

// ========== 微信二维码弹窗 ==========
function showWechatQR() {
	const modal = document.getElementById('wechatModal');
	modal.style.display = 'block';
	
	// 添加触觉反馈
	if ('vibrate' in navigator) {
		navigator.vibrate(10);
	}
}

function closeWechatQR() {
	const modal = document.getElementById('wechatModal');
	modal.style.display = 'none';
}

// 点击微信模态框外部关闭
window.addEventListener('click', function(event) {
	const wechatModal = document.getElementById('wechatModal');
	if (event.target === wechatModal) {
		closeWechatQR();
	}
});

// ========== 二维码弹窗 - 保留但更新微信二维码路径 ==========
function showQRCode(type) {
	const modal = document.getElementById('qrModal');
	const img = document.getElementById('qrImage');
	
	// 根据类型设置不同的二维码图片
	const qrImages = {
		'weixin': './img/WEIXIN.png', // 微信二维码路径
		'gongzhonghao': './img/gongzhonghao.png' // 公众号二维码路径
	};
	
	img.src = qrImages[type] || '';
	modal.style.display = 'block';
	
	// 添加触觉反馈
	if ('vibrate' in navigator) {
		navigator.vibrate(10);
	}
}

function closeQRCode() {
	const modal = document.getElementById('qrModal');
	modal.style.display = 'none';
}

// 点击模态框外部关闭
window.addEventListener('click', function(event) {
	const qrModal = document.getElementById('qrModal');
	if (event.target === qrModal) {
		closeQRCode();
	}
});

// ESC 键关闭弹窗
document.addEventListener('keydown', (e) => {
	if (e.key === 'Escape') {
		closeQRCode();
		closeWechatQR();
	}
});

// ========== 时钟和问候语 ==========
function updateClock() {
	const now = new Date();
	const year = now.getFullYear();
	const month = String(now.getMonth() + 1).padStart(2, '0');
	const day = String(now.getDate()).padStart(2, '0');
	const hours = String(now.getHours()).padStart(2, '0');
	const minutes = String(now.getMinutes()).padStart(2, '0');
	const weekdays = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
	const weekday = weekdays[now.getDay()];
	
	document.getElementById('clock').textContent = `${year}年${month}月${day}日 ${hours}:${minutes} ${weekday}`;
	
	// 问候语
	const hour = now.getHours();
	let greeting = '';
	if (hour >= 0 && hour < 5) greeting = '🌙 凌晨好';
	else if (hour >= 5 && hour < 9) greeting = '🌅 早上好';
	else if (hour >= 9 && hour < 12) greeting = '☀️ 上午好';
	else if (hour >= 12 && hour < 14) greeting = '🌞 中午好';
	else if (hour >= 14 && hour < 18) greeting = '🌤️ 下午好';
	else if (hour >= 18 && hour < 19) greeting = '🌆 傍晚好';
	else if (hour >= 19 && hour < 22) greeting = '🌃 晚上好';
	else greeting = '🌙 夜深了';
	
	document.getElementById('greeting').textContent = greeting;
}

// 初始化时钟
updateClock();
// 每分钟更新一次
setInterval(updateClock, 60000);

// ========== 运行时间计算 ==========
function updateRunTime() {
	const startDate = new Date('2024-01-01 00:00:00'); // 修改为你的网站开始日期
	const now = new Date();
	const diff = now - startDate;
	
	const days = Math.floor(diff / (1000 * 60 * 60 * 24));
	const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
	
	const runTimeElement = document.getElementById('runTime');
	if (runTimeElement) {
		runTimeElement.textContent = `${days} 天 ${hours} 小时 ${minutes} 分钟`;
	}
}

// 初始化运行时间
updateRunTime();
// 每分钟更新一次
setInterval(updateRunTime, 60000);

// ========== 一言 API ==========
fetch('https://v1.hitokoto.cn/?encode=json')
	.then(response => response.json())
	.then(data => {
		const hitokotoElement = document.getElementById('hitokoto');
		if (hitokotoElement) {
			hitokotoElement.textContent = data.hitokoto;
		}
	})
	.catch(error => {
		console.error('获取一言失败:', error);
		const hitokotoElement = document.getElementById('hitokoto');
		if (hitokotoElement) {
			hitokotoElement.textContent = '热爱简洁设计与高质量代码';
		}
	});

// ========== 平滑滚动 ==========
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
	anchor.addEventListener('click', function (e) {
		e.preventDefault();
		const target = document.querySelector(this.getAttribute('href'));
		if (target) {
			target.scrollIntoView({
				behavior: 'smooth',
				block: 'start'
			});
		}
	});
});

// ========== 项目卡片交互 ==========
document.querySelectorAll('.project-item').forEach(item => {
	item.addEventListener('mouseenter', function() {
		this.style.transform = 'translateY(-3px)';
	});
	
	item.addEventListener('mouseleave', function() {
		this.style.transform = 'translateY(0)';
	});
});

// ========== 技能标签点击复制 ==========
document.querySelectorAll('.left-tag-item').forEach(tag => {
	tag.style.cursor = 'pointer';
	tag.addEventListener('click', function() {
		const skill = this.querySelector('span').textContent;
		
		if (navigator.clipboard) {
			navigator.clipboard.writeText(skill).then(() => {
				const originalText = this.querySelector('span').textContent;
				this.querySelector('span').textContent = '✓ 已复制';
				setTimeout(() => {
					this.querySelector('span').textContent = originalText;
				}, 1000);
			});
		}
	});
});

// 微信二维码功能已移至 index_v2.php 的内联脚本中

// ========== 移动端优化 ==========
if (isMobile) {
	// 触觉反馈
	document.querySelectorAll('.social-btn, .project-item, .header-icon, .left-tag-item').forEach(el => {
		el.addEventListener('click', () => {
			if ('vibrate' in navigator) {
				navigator.vibrate(10);
			}
		});
	});
	
	// 移动端优化：防止双击缩放
	let lastTouchEnd = 0;
	document.addEventListener('touchend', (e) => {
		const now = Date.now();
		if (now - lastTouchEnd <= 300) {
			e.preventDefault();
		}
		lastTouchEnd = now;
	}, false);
	
	// 移动端：优化滚动性能
	document.addEventListener('touchmove', () => {
		// 使用被动监听器提升滚动性能
	}, { passive: true });
}

// ========== 页面加载动画 ==========
window.addEventListener('load', () => {
	document.body.style.opacity = '0';
	setTimeout(() => {
		document.body.style.transition = 'opacity 0.5s ease';
		document.body.style.opacity = '1';
	}, 100);
});

// ========== 页面可见性变化 ==========
let originalTitle = document.title;
document.addEventListener('visibilitychange', () => {
	if (document.hidden) {
		document.title = '别走呀~ | ' + originalTitle;
	} else {
		document.title = originalTitle;
	}
});

// ========== 控制台彩蛋 ==========
console.log('%c👋 你好呀！', 'font-size: 20px; color: #2563eb; font-weight: bold;');
console.log('%c欢迎来到我的个人主页！', 'font-size: 14px; color: #666;');
console.log('%c如果你对代码感兴趣，欢迎联系我 😊', 'font-size: 12px; color: #999;');
console.log('%c💡 小提示：点击技能标签可以复制！', 'font-size: 12px; color: #f59e0b; font-style: italic;');

// ========== 滚动性能优化 ==========
let ticking = false;
let lastScrollY = window.scrollY;

window.addEventListener('scroll', () => {
	lastScrollY = window.scrollY;
	
	if (!ticking) {
		window.requestAnimationFrame(() => {
			// 滚动时的优化处理
			ticking = false;
		});
		
		ticking = true;
	}
}, { passive: true });

// ========== 响应式菜单（移动端） ==========
if (window.innerWidth <= 968) {
	console.log('📱 移动端模式');
}

// ========== 图片懒加载 ==========
if ('IntersectionObserver' in window) {
	const imageObserver = new IntersectionObserver((entries, observer) => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				const img = entry.target;
				if (img.dataset.src) {
					img.src = img.dataset.src;
					img.removeAttribute('data-src');
					observer.unobserve(img);
				}
			}
		});
	});
	
	document.querySelectorAll('img[data-src]').forEach(img => {
		imageObserver.observe(img);
	});
}

// ========== 性能监控 ==========
if ('performance' in window) {
	window.addEventListener('load', () => {
		const perfData = performance.getEntriesByType('navigation')[0];
		if (perfData) {
			console.log('⚡ 页面加载时间:', Math.round(perfData.loadEventEnd - perfData.fetchStart), 'ms');
		}
	});
}

// ========== 键盘快捷键 ==========
document.addEventListener('keydown', (e) => {
	// Esc 键优先关闭弹窗，否则返回顶部
	if (e.key === 'Escape') {
		const modal = document.getElementById('qrModal');
		if (modal && modal.style.display === 'block') {
			closeQRCode();
		} else {
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		}
		return;
	}
	
	// D 键切换深色模式
	if (e.key === 'd' || e.key === 'D') {
		if (!e.target.matches('input, textarea')) {
			themeToggle.checked = !themeToggle.checked;
			themeToggle.dispatchEvent(new Event('change'));
		}
	}
});

// ========== 统计数字动画 ==========
function animateValue(element, start, end, duration) {
	let startTimestamp = null;
	const step = (timestamp) => {
		if (!startTimestamp) startTimestamp = timestamp;
		const progress = Math.min((timestamp - startTimestamp) / duration, 1);
		const value = Math.floor(progress * (end - start) + start);
		element.textContent = value + (element.dataset.suffix || '');
		if (progress < 1) {
			window.requestAnimationFrame(step);
		}
	};
	window.requestAnimationFrame(step);
}

// 当统计数字进入视口时触发动画
const statsObserver = new IntersectionObserver((entries) => {
	entries.forEach(entry => {
		if (entry.isIntersecting) {
			const statValue = entry.target;
			const text = statValue.textContent;
			const match = text.match(/(\d+)/);
			if (match) {
				const endValue = parseInt(match[1]);
				statValue.dataset.suffix = text.replace(match[1], '');
				animateValue(statValue, 0, endValue, 1000);
				statsObserver.unobserve(statValue);
			}
		}
	});
});

document.querySelectorAll('.stat-value').forEach(stat => {
	statsObserver.observe(stat);
});
