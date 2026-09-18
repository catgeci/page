<?php
// 开启错误报告，帮助排查问题
error_reporting(E_ALL);
ini_set('display_errors', 1);

	$name = '歌辞';
	$title = '全栈开发工程师';
	$bio = '热爱简洁设计与高质量代码，专注于 Web/移动端产品与工具开发。';
	$location = 'NC, China';
	$email = '3208757167@qq.com';
	$avatar = 'http://catgeci.cn/xc/picture/20251219_125401_6944da694f443.jpg';
	
	$links = [
		// 个人作品
		['label' => '个人站', 'url' => 'http://catgeci.cn/index.php', 'icon' => '🏠', 'category' => 'work', 'desc' => '我的个人主页', 'img' => 'http://catgeci.cn/xc/picture/20251219_125401_6944da694f443.jpg'],
		['label' => '博客', 'url' => 'http://catgeci.cn/blog/', 'icon' => '📝', 'category' => 'work', 'desc' => '技术文章与思考', 'img' => ''],
		['label' => '个人相册', 'url' => 'http://catgeci.cn/xc/index.php', 'icon' => '📷', 'category' => 'work', 'desc' => '生活记录与分享', 'img' => ''],
		['label' => '个人云盘', 'url' => 'http://catgeci.cn/yp/index.php', 'icon' => '☁️', 'category' => 'work', 'desc' => '文件存储与管理', 'img' => ''],
		
		// 游戏项目
		['label' => '象棋游戏', 'url' => 'http://catgeci.cn/game.php', 'icon' => '♟️', 'category' => 'game', 'desc' => '在线中国象棋对战', 'img' => ''],
		['label' => '五子棋游戏', 'url' => 'http://catgeci.cn/wzq/index.html', 'icon' => '⚫', 'category' => 'game', 'desc' => '经典五子棋对弈', 'img' => ''],
		['label' => '三国杀游戏', 'url' => 'http://catgeci.cn/sgs/index.html', 'icon' => '🎴', 'category' => 'game', 'desc' => '策略卡牌游戏', 'img' => ''],
		['label' => '50款h5小游戏', 'url' => 'http://catgeci.cn/game/index.html', 'icon' => '🎮', 'category' => 'game', 'desc' => '精选小游戏合集', 'img' => ''],
		['label' => '召唤神龙', 'url' => 'http://catgeci.cn/sl/index.html', 'icon' => '🐉', 'category' => 'game', 'desc' => '趣味互动小游戏', 'img' => ''],
		['label' => '更多游戏', 'url' => 'http://catgeci.cn/gm/index.html', 'icon' => '🕹️', 'category' => 'game', 'desc' => '其他游戏作品', 'img' => ''],
		
		// 互动区域
		['label' => '留言板', 'url' => 'http://catgeci.cn/lyb/index.php', 'icon' => '💬', 'category' => 'social', 'desc' => '留下你的足迹', 'img' => ''],
	];
	
	$skills = [
		['name' => 'PHP', 'icon' => './img/php.png'],
		['name' => 'JavaScript', 'icon' => './img/javascript.png'],
		['name' => 'HTML/CSS', 'icon' => './img/HTML.png'],
		['name' => 'JavaWeb', 'icon' => './img/javaweb.png'],
		['name' => 'Node.js', 'icon' => './img/Nodejs.png'],
		['name' => 'MySQL', 'icon' => './img/mysql.png'],
		['name' => 'Python', 'icon' => './img/python.png'],
		['name' => 'JAVA', 'icon' => './img/java.png']
	];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
	<meta name="color-scheme" content="light dark">
	<meta name="theme-color" content="#2563eb">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<title><?php echo htmlspecialchars($name); ?> · <?php echo htmlspecialchars($title); ?></title>
	<link rel="icon" href="<?php echo htmlspecialchars($avatar); ?>" sizes="32x32">
	<link rel="apple-touch-icon" href="<?php echo htmlspecialchars($avatar); ?>">
	<link rel="stylesheet" href="style_v2.css?v=<?php echo time(); ?>">
	<link rel="stylesheet" href="APlayer.min.css">
</head>
<body>
	<!-- 背景滤镜 -->
	<div class="noise-filter"></div>
	
	<!-- 微信二维码弹窗 -->
	<div id="wechatModal" class="wechat-modal">
		<div class="wechat-modal-content">
			<span class="wechat-close" onclick="closeWechatQR()">&times;</span>
			<img src="./img/WEIXIN.png" alt="微信二维码">
		</div>
	</div>
	
	<!-- 二维码弹窗 -->
	<div id="qrModal" class="qr-modal">
		<div class="qr-modal-content">
			<span class="qr-close" onclick="closeQRCode()">&times;</span>
			<img id="qrImage" src="" alt="二维码">
		</div>
	</div>
	
	<!-- 主容器 -->
	<div class="noise-main">
		<!-- 左侧栏 -->
		<aside class="noise-left">
			<!-- 头像 -->
			<div class="logo" style="background-image: url('<?php echo htmlspecialchars($avatar); ?>');"></div>
			
			<!-- 左侧卡片网格 -->
			<div class="left-grid">
				<!-- 个人信息 -->
				<div class="left-div left-des">
					<div class="left-des-item">
					<span class="icon-emoji">📍</span>
					<span><?php echo htmlspecialchars($location); ?></span>
				</div>
				<div class="left-des-item">
					<span class="icon-emoji">💻</span>
					<span><?php echo htmlspecialchars($title); ?></span>
				</div>
				<div class="left-des-item">
					<span class="icon-emoji">💭</span>
					<span><?php echo htmlspecialchars($bio); ?></span>
				</div>
				</div>
				
				<!-- 技能标签 -->
				<div class="left-div left-tag">
					<?php foreach ($skills as $skill): ?>
					<div class="left-tag-item">
						<img src="<?php echo htmlspecialchars($skill['icon']); ?>" alt="<?php echo htmlspecialchars($skill['name']); ?>" style="width: 20px !important; height: 20px !important; max-width: 20px !important; max-height: 20px !important;">
						<span><?php echo htmlspecialchars($skill['name']); ?></span>
					</div>
				<?php endforeach; ?>
				</div>
				
				<!-- 时间显示 -->
				<div class="left-div left-time">
					<div id="clock"></div>
					<div id="greeting"></div>
				</div>
				
				<!-- 统计数据 -->
				<div class="left-div left-stats">
					<div class="stat-row">
						<span class="stat-label">开发经验</span>
						<span class="stat-value">2+ 年</span>
					</div>
					<div class="stat-row">
						<span class="stat-label">完成项目</span>
						<span class="stat-value">50+</span>
					</div>
					<div class="stat-row">
						<span class="stat-label">学习热情</span>
						<span class="stat-value">∞</span>
					</div>
				</div>
				
				<!-- 一言 -->
				<div class="left-div left-hitokoto">
					<div id="hitokoto">正在加载...</div>
				</div>
				
				<!-- 音乐播放器 -->
				<div class="left-div left-music">
					<meting-js
						preload="none"
						server="netease"
						type="playlist"
						id="2926227857">
					</meting-js>
				</div>
			</div>
		</aside>
		
		<!-- 右侧内容区 -->
		<main class="noise-right">
			<!-- 头部 -->
			<header class="page-header">
				<div class="header-top">
					<div class="header-left">
						<div class="welcome">
							<img src="./img/welcome.png" alt="welcome" class="welcome-icon">
							<span class="gradient-text">⭐️ <?php echo htmlspecialchars($name); ?> 的个人主页</span>
						</div>
						<div class="description">
							<img src="./img/welcome.png" alt="欢迎" class="welcome-icon-small">
							<span class="purple-text">欢迎访问！</span> 这里是我的个人作品展示空间
						</div>
					</div>
					
					<!-- 社交图标容器 -->
					<div class="header-social">
						<a href="https://github.com/catgeci" class="header-icon" title="GitHub" target="_blank">
							<img src="./img/github.png" alt="GitHub" class="social-icon-img">
						</a>
						<a href="https://space.bilibili.com/400717186" class="header-icon" title="Bilibili" target="_blank">
							<img src="./img/bilibili.png" alt="Bilibili" class="social-icon-img">
						</a>
						<a href="mailto:<?= htmlspecialchars($email) ?>" class="header-icon" title="邮箱">
							<img src="./img/邮箱.png" alt="邮箱" class="social-icon-img">
						</a>
						<a href="mqqwpa://im/chat?chat_type=wpa&uin=3208757167&version=1&src_type=web" class="header-icon" title="QQ">
							<img src="./img/qq.png" alt="QQ" class="social-icon-img">
						</a>
						<a href="https://twitter.com/catgeci" class="header-icon" title="Twitter" target="_blank">
							<img src="./img/推特.png" alt="Twitter" class="social-icon-img">
						</a>
						<a href="http://catgeci.cn/blog/" class="header-icon" title="博客" target="_blank">
							<img src="./img/blog.png" alt="博客" class="social-icon-img">
						</a>
						<a href="javascript:void(0)" class="header-icon wechat-icon" title="微信" onclick="showWechatQR()">
				<img src="./img/wx.png" alt="微信" class="social-icon-img">
			</a>
						<a href="https://t.me/catgeci" class="header-icon" title="Telegram" target="_blank">
							<img src="./img/纸飞机.png" alt="Telegram" class="social-icon-img">
						</a>
						
						<!-- 深色模式切换 -->
						<label class="theme-switch-btn" title="切换主题">
							<input type="checkbox" id="theme-toggle">
							<span class="theme-slider">
								<img src="./img/白天模式.png" alt="白天" class="theme-icon">
								<img src="./img/夜间模式.png" alt="夜间" class="theme-icon">
							</span>
						</label>
					</div>
				</div>
				
				<!-- 滚动公告栏 -->
				<div class="announcement-bar">
					<div class="announcement-content">
						<marquee direction="left" scrollamount="5">
							<strong>
								<a href="http://catgeci.cn" target="_blank">
									<span style="color: #FF0000;"><img src="./img/welcome.png" class="inline-icon" alt="欢迎"> 欢迎访问本站，站点域名 catgeci.cn 👉</span>
								</a>
								<a href="http://catgeci.cn/blog/" target="_blank">
									<span style="color: #33a4e5;">📢 ①：博客已更新最新文章</span>
								</a>
								<a href="http://catgeci.cn/xc/index.php" target="_blank">
									<span style="color: #e56e33;">📢 ②：个人相册新增照片</span>
								</a>
								<a href="http://catgeci.cn/game.php" target="_blank">
									<span style="color: #b333e5;">📢 ③：新游戏上线啦</span>
								</a>
								<a href="http://catgeci.cn" target="_blank">
									<span style="color: #10b981;">公告已更新，欢迎随时回来 <img src="./img/welcome.png" class="inline-icon" alt="欢迎"></span>
								</a>
							</strong>
						</marquee>
					</div>
				</div>
			</header>
			
			<!-- 内容区 -->
			<div class="content-wrapper">
				<!-- 个人作品 -->
				<section class="project-section">
					<div class="section-title">
						⭐ 个人作品
					</div>
					<div class="project-list">
						<?php foreach ($links as $link): ?>
					<?php if ($link['category'] === 'work'): ?>
						<a class="project-item" href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank">
							<div class="project-left">
								<h3>
									<?php echo htmlspecialchars($link['label']); ?>
								</h3>
								<p><?php echo htmlspecialchars($link['desc']); ?></p>
							</div>
							<div class="project-right">
								<span class="project-icon"><?php echo $link['icon']; ?></span>
							</div>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>
					</div>
				</section>
				
				<!-- 游戏项目 -->
				<section class="project-section">
					<div class="section-title">
						🎮 游戏项目
					</div>
					<div class="project-list">
						<?php foreach ($links as $link): ?>
					<?php if ($link['category'] === 'game'): ?>
						<a class="project-item" href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank">
							<div class="project-left">
								<h3>
									<?php echo htmlspecialchars($link['label']); ?>
								</h3>
								<p><?php echo htmlspecialchars($link['desc']); ?></p>
							</div>
							<div class="project-right">
								<span class="project-icon"><?php echo $link['icon']; ?></span>
							</div>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>
					</div>
				</section>
				
				<!-- 互动区域 -->
				<section class="project-section">
					<div class="section-title">
						💬 互动区域
					</div>
					<div class="project-list">
						<?php foreach ($links as $link): ?>
					<?php if ($link['category'] === 'social'): ?>
						<a class="project-item" href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank">
							<div class="project-left">
								<h3>
									<?php echo htmlspecialchars($link['label']); ?>
								</h3>
								<p><?php echo htmlspecialchars($link['desc']); ?></p>
							</div>
							<div class="project-right">
								<span class="project-icon"><?php echo $link['icon']; ?></span>
							</div>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>
					</div>
				</section>
			</div>
			
			<!-- 页脚 -->
			<footer class="page-footer">
				<div class="footer-content">
					<p>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($name); ?> · 保持好奇与热爱 ❤️</p>
					<p class="footer-time">
						本站已运行 <span id="runTime"></span>
					</p>
				</div>
			</footer>
		</main>
	</div>
	
	<script src="APlayer.min.js"></script>
	<script src="Meting.min.js"></script>
	<script src="script_v2.js"></script>
</body>
</html>
