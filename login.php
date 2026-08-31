<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role, customer_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['customer_id'] = $user['customer_id'];

            header("Location: modules/dashboard/index.php");
            exit();
        } else {
            $error = "Invalid credentials. Please try again.";
        }
    } else {
        $error = "User account not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | A'One Intelligence</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4362CE;
            --primary-glow: #3B82F6;
            --cyan-glow: #00F0FF;
            --purple-glow: #8B5CF6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #060913;
            color: #F8FAFC;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
        }

        /* Interactive Particle Constellation Canvas */
        #bgCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1;
            pointer-events: auto;
        }

        /* Aurora Animated Background Mesh */
        .aurora-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            background: #050811;
            overflow: hidden;
        }

        .aurora-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.55;
            mix-blend-mode: screen;
            animation: auroraMovement 18s infinite alternate ease-in-out;
        }

        .blob-1 {
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(67, 98, 206, 0.6) 0%, rgba(59, 130, 246, 0.2) 60%, transparent 80%);
            top: -20%;
            left: -10%;
            animation-duration: 20s;
        }

        .blob-2 {
            width: 650px;
            height: 650px;
            background: radial-gradient(circle, rgba(0, 240, 255, 0.45) 0%, rgba(67, 98, 206, 0.2) 50%, transparent 75%);
            bottom: -20%;
            right: -10%;
            animation-duration: 24s;
            animation-delay: -6s;
        }

        .blob-3 {
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.4) 0%, rgba(99, 102, 241, 0.15) 60%, transparent 80%);
            top: 30%;
            right: 20%;
            animation-duration: 16s;
            animation-delay: -11s;
        }

        @keyframes auroraMovement {
            0% {
                transform: translate(0, 0) scale(1) rotate(0deg);
            }

            33% {
                transform: translate(80px, -50px) scale(1.15) rotate(120deg);
            }

            66% {
                transform: translate(-60px, 90px) scale(0.9) rotate(240deg);
            }

            100% {
                transform: translate(40px, -30px) scale(1.05) rotate(360deg);
            }
        }

        /* Ambient Cyber Grid Floor */
        .cyber-grid {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 45vh;
            background:
                linear-gradient(to top, rgba(6, 9, 19, 0.95), transparent),
                linear-gradient(to right, rgba(67, 98, 206, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(67, 98, 206, 0.1) 1px, transparent 1px);
            background-size: 100% 100%, 40px 40px, 40px 40px;
            transform: perspective(600px) rotateX(65deg);
            transform-origin: bottom center;
            z-index: 1;
            pointer-events: none;
            opacity: 0.6;
            animation: gridScan 20s linear infinite;
        }

        @keyframes gridScan {
            0% {
                background-position: 0 0, 0 0, 0 0;
            }

            100% {
                background-position: 0 0, 0 800px, 0 800px;
            }
        }

        /* Container & 3D Tilt Wrapper */
        .login-wrapper {
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 10;
            perspective: 1200px;
        }

        /* Holographic Glowing Glass Card */
        .login-card {
            background: rgba(13, 19, 36, 0.75);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border-radius: 30px;
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.1),
                0 25px 60px -15px rgba(0, 0, 0, 0.9),
                0 0 50px -10px rgba(67, 98, 206, 0.4);
            transform-style: preserve-3d;
            transition: transform 0.15s ease-out, box-shadow 0.3s ease;
        }

        /* Animated Rotating RGB Border Beam */
        .login-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(transparent,
                    rgba(67, 98, 206, 0.9),
                    rgba(0, 240, 255, 0.9),
                    rgba(139, 92, 246, 0.9),
                    transparent 65%);
            animation: rotateBorder 6s linear infinite;
            z-index: -2;
        }

        .login-card::after {
            content: '';
            position: absolute;
            inset: 2px;
            background: rgba(11, 17, 33, 0.92);
            backdrop-filter: blur(24px);
            border-radius: 28px;
            z-index: -1;
        }

        @keyframes rotateBorder {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Ambient Glass Reflection Glare */
        .card-glare {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle 350px at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(255, 255, 255, 0.08), transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* Logo Badge with 3D Hologram Ring */
        .brand-header {
            text-align: center;
            margin-bottom: 2.25rem;
            position: relative;
            z-index: 2;
        }

        .brand-logo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 1.25rem;
        }

        .logo-ring-pulse {
            position: absolute;
            inset: -10px;
            border-radius: 28px;
            border: 2px solid rgba(0, 240, 255, 0.5);
            animation: ringRipple 3s infinite cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }

        @keyframes ringRipple {
            0% {
                transform: scale(0.85);
                opacity: 0.9;
            }

            50% {
                transform: scale(1.18);
                opacity: 0.3;
                border-color: rgba(67, 98, 206, 0.6);
            }

            100% {
                transform: scale(1.35);
                opacity: 0;
            }
        }

        .brand-logo-badge {
            width: 76px;
            height: 76px;
            background: linear-gradient(135deg, rgba(67, 98, 206, 0.4) 0%, rgba(17, 24, 39, 0.9) 100%);
            border: 1.5px solid rgba(0, 240, 255, 0.4);
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                0 0 25px rgba(67, 98, 206, 0.5),
                0 0 45px rgba(0, 240, 255, 0.3),
                inset 0 1px 2px rgba(255, 255, 255, 0.4);
            animation: floatLogo 4s infinite ease-in-out;
        }

        @keyframes floatLogo {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-7px) rotate(2deg);
            }
        }

        .brand-logo-badge img {
            width: 46px;
            height: 46px;
            object-fit: contain;
            filter: drop-shadow(0 0 12px rgba(0, 240, 255, 0.6));
        }

        .brand-title {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, #FFFFFF 20%, #A5F3FC 55%, #60A5FA 90%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 6px;
            text-shadow: 0 0 30px rgba(0, 240, 255, 0.25);
        }

        .brand-subtitle {
            font-size: 0.84rem;
            color: #94A3B8;
            font-weight: 500;
            letter-spacing: 0.02em;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 1.35rem;
            position: relative;
            z-index: 2;
        }

        .form-label {
            display: block;
            font-size: 0.76rem;
            font-weight: 700;
            color: #94A3B8;
            margin-bottom: 8px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box i.input-icon {
            position: absolute;
            left: 16px;
            color: #64748B;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            pointer-events: none;
        }

        .input-control {
            width: 100%;
            height: 48px;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            padding: 0 46px 0 46px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #FFFFFF;
            outline: none;
            font-family: inherit;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .input-control::placeholder {
            color: #475569;
            font-weight: 400;
        }

        .input-control:focus {
            background: rgba(15, 23, 42, 0.95);
            border-color: #00F0FF;
            box-shadow:
                0 0 0 3px rgba(0, 240, 255, 0.2),
                0 0 20px rgba(67, 98, 206, 0.35);
            transform: translateY(-2px);
        }

        .input-control:focus~i.input-icon {
            color: #00F0FF;
            transform: scale(1.15);
            filter: drop-shadow(0 0 6px rgba(0, 240, 255, 0.8));
        }

        .password-toggle-btn {
            position: absolute;
            right: 15px;
            color: #64748B;
            cursor: pointer;
            font-size: 0.95rem;
            background: none;
            border: none;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .password-toggle-btn:hover {
            color: #00F0FF;
            transform: scale(1.15);
        }

        /* Animated Glowing Futuristic Button */
        .btn-glow-wrap {
            position: relative;
            margin-top: 1.75rem;
            z-index: 2;
        }

        .btn-signin {
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, #4362CE 0%, #3B82F6 50%, #00F0FF 100%);
            background-size: 200% auto;
            color: #FFFFFF;
            border: none;
            border-radius: 14px;
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow:
                0 8px 25px -4px rgba(67, 98, 206, 0.6),
                0 0 20px rgba(0, 240, 255, 0.35),
                inset 0 1px 1px rgba(255, 255, 255, 0.5);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            animation: gradientShift 4s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Button Light Glint Sweep */
        .btn-signin::before {
            content: '';
            position: absolute;
            top: 0;
            left: -120%;
            width: 80%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transform: skewX(-25deg);
            animation: btnGlint 3.5s infinite ease-in-out;
        }

        @keyframes btnGlint {
            0% {
                left: -120%;
            }

            35%,
            100% {
                left: 160%;
            }
        }

        .btn-signin:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow:
                0 14px 35px -4px rgba(67, 98, 206, 0.8),
                0 0 30px rgba(0, 240, 255, 0.6),
                inset 0 1px 2px rgba(255, 255, 255, 0.7);
        }

        .btn-signin:active {
            transform: translateY(-1px) scale(0.99);
        }

        .btn-signin i {
            transition: transform 0.25s ease;
        }

        .btn-signin:hover i {
            transform: translateX(6px) scale(1.15);
        }

        /* Error Banner */
        .error-banner {
            background: rgba(239, 68, 68, 0.18);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #FCA5A5;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            font-size: 0.84rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shakeCard 0.4s ease-in-out;
            position: relative;
            z-index: 2;
        }

        @keyframes shakeCard {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-6px);
            }

            40%,
            80% {
                transform: translateX(6px);
            }
        }

        .footer-caption {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.78rem;
            color: #64748B;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }

            .login-card {
                padding: 2.25rem 1.5rem;
                border-radius: 24px;
            }

            .brand-title {
                font-size: 1.55rem;
            }

            .brand-logo-badge {
                width: 66px;
                height: 66px;
            }

            .brand-logo-badge img {
                width: 38px;
                height: 38px;
            }
        }
    </style>
</head>

<body>

    <!-- Aurora Glowing Background Meshes -->
    <div class="aurora-bg">
        <div class="aurora-blob blob-1"></div>
        <div class="aurora-blob blob-2"></div>
        <div class="aurora-blob blob-3"></div>
    </div>

    <!-- Cyber Space Grid -->
    <div class="cyber-grid"></div>

    <!-- Interactive Interactive Particle Canvas -->
    <canvas id="bgCanvas"></canvas>

    <!-- Login Container -->
    <div class="login-wrapper">
        <div class="login-card" id="loginCard">
            <div class="card-glare"></div>

            <div class="brand-header">
                <div class="brand-logo-container">
                    <div class="logo-ring-pulse"></div>
                    <div class="brand-logo-badge">
                        <img src="<?php echo BASE_URL; ?>assets/images/Frame.png" alt="A'One Logo" />
                    </div>
                </div>
                <h1 class="brand-title">A'One Intelligence</h1>
                <p class="brand-subtitle">Amazon CRM &amp; Analytics Portal</p>
            </div>

            <?php if ($error): ?>
                <div class="error-banner">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-box">
                        <input type="text" id="username" name="username" class="input-control" required
                            placeholder="Enter your username" autocomplete="username">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-box">
                        <input type="password" id="password" name="password" class="input-control" required
                            placeholder="••••••••" autocomplete="current-password">
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle-btn" onclick="togglePassword()"
                            aria-label="Toggle Password Visibility">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="btn-glow-wrap">
                    <button type="submit" class="btn-signin">
                        <span>SIGN IN TO PORTAL</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>

        <p class="footer-caption">
            &copy; <?php echo date('Y'); ?> A'One Intelligence • Secure Analytics Engine v2.0
        </p>
    </div>

    <script>
        // Password Visibility Toggle
        function togglePassword() {
            const pwd = document.getElementById('password');
            const eye = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        // 3D Card Hover Perspective Effect
        const card = document.getElementById('loginCard');
        document.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -5;
            const rotateY = ((x - centerX) / centerX) * 5;

            if (e.clientX >= rect.left - 50 && e.clientX <= rect.right + 50 &&
                e.clientY >= rect.top - 50 && e.clientY <= rect.bottom + 50) {
                card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            } else {
                card.style.transform = 'rotateX(0deg) rotateY(0deg)';
            }
        });

        // Interactive Constellation Node Particle Network
        const canvas = document.getElementById('bgCanvas');
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        class Node {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.vx = (Math.random() - 0.5) * 0.7;
                this.vy = (Math.random() - 0.5) * 0.7;
                this.radius = Math.random() * 2 + 1.2;
                this.baseAlpha = Math.random() * 0.5 + 0.3;
            }

            update() {
                this.x += this.vx;
                this.y += this.vy;

                if (this.x < 0 || this.x > width) this.vx *= -1;
                if (this.y < 0 || this.y > height) this.vy *= -1;
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(0, 240, 255, ${this.baseAlpha})`;
                ctx.shadowBlur = 8;
                ctx.shadowColor = '#00F0FF';
                ctx.fill();
            }
        }

        const count = Math.min(width > 768 ? 65 : 30, 80);
        for (let i = 0; i < count; i++) {
            particles.push(new Node());
        }

        let mouse = { x: null, y: null };
        window.addEventListener('mousemove', (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });

        function animate() {
            ctx.clearRect(0, 0, width, height);

            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();

                // Connect nodes with glowing lines
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < 130) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = `rgba(67, 98, 206, ${0.35 * (1 - dist / 130)})`;
                        ctx.lineWidth = 0.8;
                        ctx.stroke();
                    }
                }

                // Interactive connection to mouse cursor
                if (mouse.x !== null) {
                    const dx = particles[i].x - mouse.x;
                    const dy = particles[i].y - mouse.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 160) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(mouse.x, mouse.y);
                        ctx.strokeStyle = `rgba(0, 240, 255, ${0.45 * (1 - dist / 160)})`;
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }
            }

            requestAnimationFrame(animate);
        }
        animate();
    </script>
</body>

</html>