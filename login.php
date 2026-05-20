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
    <title>Login | AOne Amazon Intelligence</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #bef264;
            --primary-glow: rgba(190, 242, 100, 0.4);
            --bg: #020617;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text: #f8fafc;
            --accent: #38bdf8;
        }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Enhanced Animated Mesh Background */
        .bg-mesh {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            background: #020617;
            overflow: hidden;
        }

        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: float 25s infinite alternate ease-in-out;
        }

        .orb-1 { 
            width: 800px; height: 800px; 
            background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%);
            top: -20%; left: -10%; 
            animation-duration: 30s;
        }
        .orb-2 { 
            width: 600px; height: 600px; 
            background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, transparent 70%);
            bottom: -10%; right: -5%; 
            animation-duration: 20s;
            animation-delay: -7s;
        }
        .orb-3 { 
            width: 500px; height: 500px; 
            background: radial-gradient(circle, rgba(168, 85, 247, 0.15) 0%, transparent 70%);
            top: 40%; right: 20%; 
            animation-duration: 25s;
            animation-delay: -12s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1) rotate(0deg); }
            33% { transform: translate(100px, 50px) scale(1.1) rotate(10deg); }
            66% { transform: translate(-50px, 100px) scale(0.9) rotate(-10deg); }
            100% { transform: translate(0, 0) scale(1) rotate(0deg); }
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            z-index: 10;
        }

        /* Floating Particles */
        .particle {
            position: absolute;
            background: var(--primary);
            border-radius: 50%;
            pointer-events: none;
            opacity: 0.3;
            animation: move-particle 15s infinite linear;
        }

        @keyframes move-particle {
            0% { transform: translateY(0) translateX(0); opacity: 0; }
            50% { opacity: 0.5; }
            100% { transform: translateY(-100vh) translateX(50px); opacity: 0; }
        }

        .login-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            padding: 4rem 3.5rem;
            border-radius: 48px;
            box-shadow: 
                0 0 0 1px rgba(255,255,255,0.08),
                0 30px 60px -12px rgba(0,0,0,0.8);
            border: 1px solid rgba(255,255,255,0.12);
            position: relative;
            animation: card-entry 1.4s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        @keyframes card-entry {
            0% { opacity: 0; transform: translateY(50px) rotateX(10deg) scale(0.9); }
            100% { opacity: 1; transform: translateY(0) rotateX(0deg) scale(1); }
        }

        .logo {
            text-align: center;
            margin-bottom: 3rem;
        }

        .logo-icon {
            width: 72px;
            height: 72px;
            background: var(--primary);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            box-shadow: 
                0 0 20px var(--primary-glow),
                0 10px 30px rgba(190, 242, 100, 0.3);
            transform: rotate(-5deg);
            animation: logo-float 4s infinite ease-in-out;
            position: relative;
        }

        .logo-icon::after {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 24px;
            border: 2px solid var(--primary);
            opacity: 0.3;
            animation: pulse-glow 2s infinite ease-in-out;
        }

        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.1); opacity: 0; }
        }

        @keyframes logo-float {
            0%, 100% { transform: rotate(-5deg) translateY(0); }
            50% { transform: rotate(5deg) translateY(-12px); }
        }

        .logo-icon i {
            font-size: 2rem;
            color: #064e3b;
        }

        .logo h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.06em;
            background: linear-gradient(90deg, #fff, #bef264, #fff);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 4s linear infinite;
        }

        @keyframes shimmer {
            to { background-position: 200% center; }
        }

        .logo p {
            margin: 0.5rem 0 0;
            font-size: 0.875rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fade-in 0.8s forwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.3s; }
        .form-group:nth-child(2) { animation-delay: 0.4s; }

        @keyframes fade-in {
            to { opacity: 1; transform: translateY(0); }
            from { opacity: 0; transform: translateY(10px); }
        }

        label {
            display: block;
            margin-bottom: 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            transition: color 0.3s;
        }

        input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(51, 65, 85, 0.5);
            border-radius: 16px;
            color: white;
            font-family: inherit;
            box-sizing: border-box;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.95rem;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(15, 23, 42, 0.8);
            box-shadow: 0 0 0 4px rgba(190, 242, 100, 0.1);
            transform: translateY(-2px);
        }

        input:focus + i {
            color: var(--primary);
        }

        .btn-login {
            width: 100%;
            padding: 1.125rem;
            background: var(--primary);
            color: #064e3b;
            border: none;
            border-radius: 16px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 4px 15px rgba(190, 242, 100, 0.2);
            opacity: 0;
            animation: fade-in 0.8s forwards 0.5s;
        }

        .btn-login:hover {
            background: #d9f99d;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 25px rgba(190, 242, 100, 0.4);
        }

        .btn-login:active {
            transform: translateY(-1px) scale(0.98);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 1rem;
            border-radius: 14px;
            font-size: 0.875rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            text-align: center;
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        .footer-text {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.75rem;
            color: #64748b;
            opacity: 0;
            animation: fade-in 1s forwards 0.7s;
        }

        /* Glass Reflection Effect */
        .glass-reflect {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 0%,
                rgba(255,255,255,0.03) 45%,
                rgba(255,255,255,0.05) 50%,
                rgba(255,255,255,0.03) 55%,
                transparent 100%
            );
            transform: rotate(-45deg);
            pointer-events: none;
            animation: reflect 6s infinite linear;
        }

        @keyframes reflect {
            0% { transform: translateX(-100%) rotate(-45deg); }
            100% { transform: translateX(100%) rotate(-45deg); }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>
    <div class="bg-orb orb-3"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="glass-reflect"></div>
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h2>AOne Intelligence</h2>
                <p>Amazon CRM & Analytics Portal</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-msg">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" required placeholder="Enter your username" autocomplete="username">
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" required placeholder="••••••••" autocomplete="current-password">
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <span>SIGN IN TO PORTAL</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
        <p class="footer-text">© <?php echo date('Y'); ?> AOne Intelligence • Secure Analytics Engine v2.0</p>
    </div>

    <script>
        // Generate Floating Particles
        function createParticles() {
            const container = document.body;
            for (let i = 0; i < 20; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                const size = Math.random() * 4 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                particle.style.left = `${Math.random() * 100}vw`;
                particle.style.top = `${Math.random() * 100}vh`;
                
                particle.style.animationDelay = `${Math.random() * 10}s`;
                particle.style.animationDuration = `${Math.random() * 10 + 10}s`;
                
                container.appendChild(particle);
            }
        }
        createParticles();
    </script>
</body>
</html>
