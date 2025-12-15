<?php
require 'config.php';
require 'classes/User.php';

session_start();
$user = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $avatarName = '';
    if ($_FILES['avatar']['name']) {
        $avatarName = time() . '_' . $_FILES['avatar']['name'];
        move_uploaded_file($_FILES['avatar']['tmp_name'], 'uploads/' . $avatarName);
    }

    if ($user->register($name, $email, $password, $avatarName)) {
        header('Location: login.php');
        exit;
    } else {
        $error = "Գրանցումը ձախողվեց։ Email-ը հնարավոր է արդեն օգտագործվում է։";
    }
}
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Գրանցում</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #7C3AED;
            --primary-gradient: linear-gradient(135deg, #7C3AED 0%, #5B21B6 100%);
            --primary-light: #8B5CF6;
            --secondary-color: #EC4899;
            --accent-color: #10B981;
            --error-color: #EF4444;
            --warning-color: #F59E0B;
            --bg-color: #F9FAFB;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --text-color: #1F2937;
            --text-light: #6B7280;
            --input-bg: #FFFFFF;
            --card-bg: rgba(255, 255, 255, 0.95);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 10px 20px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 25px 50px rgba(124, 58, 237, 0.15);
            --border-radius: 16px;
            --border-radius-lg: 24px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        body {
            background: var(--bg-gradient);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(124, 58, 237, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(236, 72, 153, 0.1) 0%, transparent 50%);
            z-index: -1;
        }
        
        .container {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 500px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition);
        }
        
        .container:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-5px);
        }
        
        .logo {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        
        .logo-icon {
            background: var(--primary-gradient);
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        h2 {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2rem;
            font-size: 2.25rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            z-index: 1;
            font-size: 1.1rem;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--input-bg);
            color: var(--text-color);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
            outline: none;
            transform: translateY(-2px);
        }
        
        .form-control:focus + i {
            color: var(--secondary-color);
            animation: iconPulse 0.5s ease;
        }
        
        @keyframes iconPulse {
            0%, 100% { transform: translateY(-50%) scale(1); }
            50% { transform: translateY(-50%) scale(1.2); }
        }
        
        .file-input-container {
            position: relative;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
            border: 2px dashed #D1D5DB;
            border-radius: 12px;
            cursor: pointer;
            color: var(--text-light);
            font-weight: 600;
            transition: var(--transition);
            min-height: 80px;
        }
        
        .file-input-label:hover {
            background: linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%);
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .file-input-label i {
            margin-right: 12px;
            font-size: 1.5rem;
            transition: var(--transition);
        }
        
        .file-input-label:hover i {
            transform: scale(1.1) rotate(5deg);
        }
        
        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-name {
            margin-top: 0.75rem;
            font-size: 0.875rem;
            color: var(--text-light);
            padding: 0.5rem;
            background: #F9FAFB;
            border-radius: 8px;
            transition: var(--transition);
        }
        
        .btn {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 1.25rem 2rem;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
            margin-top: 1.5rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(124, 58, 237, 0.3);
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .error-message {
            color: var(--error-color);
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%);
            padding: 1.25rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-left: 4px solid var(--error-color);
            animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .error-message i {
            margin-right: 12px;
            font-size: 1.2rem;
        }
        
        .login-link {
            display: block;
            margin-top: 2rem;
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            font-size: 1rem;
            transition: var(--transition);
            padding: 0.75rem;
            border-radius: 8px;
            position: relative;
        }
        
        .login-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary-gradient);
            transition: var(--transition);
            transform: translateX(-50%);
        }
        
        .login-link:hover {
            color: var(--primary-light);
            background: rgba(124, 58, 237, 0.05);
        }
        
        .login-link:hover::after {
            width: 80%;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            h2 {
                font-size: 1.75rem;
            }
            
            .logo {
                font-size: 2rem;
            }
            
            .btn {
                padding: 1rem 1.5rem;
            }
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            opacity: 0.1;
            animation: float 20s infinite linear;
        }
        
        .shape:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -100px;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -50px;
            left: -50px;
            animation-delay: -5s;
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        }
        
        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(30px, -50px) rotate(120deg);
            }
            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="container">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>
        
        <h2>Գրանցում</h2>
        
        <?php if (isset($error)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Անուն</label>
                <div class="input-group">
                    <i class="fas fa-user-circle"></i>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Ձեր անունը" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Էլ․ հասցե</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="example@mail.com" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Գաղտնաբառ</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Գաղտնաբառ" required>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="file-input-container">
                <label class="file-input-label" for="avatar">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Ընտրել նկար</span>
                </label>
                <input type="file" id="avatar" name="avatar" class="file-input" accept="image/*">
                <div class="file-name" id="file-name">Ոչ մի ֆայլ ընտրված չէ</div>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-user-plus" style="margin-right: 8px;"></i>
                Գրանցվել
            </button>
        </form>
        
        <a href="login.php" class="login-link">
            <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
             Մուտք գործել
        </a>
    </div>
    
    <script>
        // File name display
        document.getElementById('avatar').addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Ոչ մի ֆայլ ընտրված չէ';
            document.getElementById('file-name').textContent = fileName;
            
            if (this.files[0]) {
                document.getElementById('file-name').style.color = 'var(--primary-color)';
                document.getElementById('file-name').style.fontWeight = '600';
            }
        });
        
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Add floating animation to form inputs on focus
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
        
        // Add ripple effect to button
        document.querySelector('.btn').addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    </script>
</body>
</html>
