logout. php 
<?php
session_start();
session_destroy();
header('Location: index.php');
exit;
 
profile
<?php
require 'config.php';
require 'classes/User.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userObj = new User($pdo);
$user = $userObj->getById($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $avatar = $user['avatar'];
    
    if (!empty($_FILES['avatar']['name'])) {
        $avatar = time() . '_' . $_FILES['avatar']['name'];
        move_uploaded_file($_FILES['avatar']['tmp_name'], 'uploads/' . $avatar);
    }
    
    if (!empty($_POST['password'])) {
        if ($_POST['password'] !== $_POST['password_confirm']) {
            $error = "Գաղտնաբառերը չեն համընկնում։";
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, avatar = ?, password = ? WHERE id = ?");
            $success = $stmt->execute([$name, $email, $avatar, $password, $_SESSION['user_id']]);
        }
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, avatar = ? WHERE id = ?");
        $success = $stmt->execute([$name, $email, $avatar, $_SESSION['user_id']]);
    }
    
    if (isset($success) && $success) {
        $user = $userObj->getById($_SESSION['user_id']); 
        $message = "Տվյալները հաջողությամբ թարմացվեցին։";
    } elseif (!isset($error)) {
        $error = "Email-ը հնարավոր է արդեն օգտագործվում է։";
    }
}
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Իմ Պրոֆիլը</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Armenian:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --danger-color: #e63946;
            --success-color: #2a9d8f;
            --warning-color: #f4a261;
            --gray-color: #adb5bd;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Noto Sans Armenian', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-color);
            padding: 20px;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 550px;
            animation: fadeIn 0.8s ease-in-out;
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }
        
        h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 28px;
            position: relative;
            display: inline-block;
            text-align: center;
            width: 100%;
        }
        
        h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 2px;
        }
        
        .message {
            padding: 12px 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 500;
            text-align: center;
            animation: slideDown 0.5s ease-out;
        }
        
        .message-success {
            background-color: rgba(42, 157, 143, 0.15);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        
        .message-error {
            background-color: rgba(230, 57, 70, 0.15);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        
        .avatar-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .avatar:hover {
            transform: scale(1.05);
        }
        
        .avatar-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: var(--gray-color);
            margin: 0 auto;
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .form-group {
            position: relative;
        }
        
        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-color);
        }
        
        .form-group small {
            display: block;
            margin-top: 5px;
            color: var(--gray-color);
            font-size: 12px;
            padding-left: 15px;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Noto Sans Armenian', sans-serif;
            color: var(--dark-color);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
            height: 50px;
            border-radius: 10px;
            background-color: #f8f9fa;
            border: 1px dashed #dee2e6;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            margin: 0 0 20px;
        }
        
        .file-upload:hover {
            background-color: #e9ecef;
            border-color: var(--primary-color);
        }
        
        .file-upload input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-upload-label {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            display: inline-flex;
            align-items: center;
            color: var(--gray-color);
            font-size: 14px;
            text-align: center; 
        }
        
        .file-upload-label i {
            margin-right: 8px;
            font-size: 18px;
        }
        
        .file-name {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-left: 5px;
        }
        
        button {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
            transition: all 0.3s ease;
            font-family: 'Noto Sans Armenian', sans-serif;
        }
        
        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }
        
        button:active {
            transform: translateY(-1px);
        }
        
        .links-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }
        
        .link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .link:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }
        
        .link i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .link-logout {
            color: var(--danger-color);
        }
        
        .link-logout i {
            color: var(--danger-color);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            transform: translateX(-5px);
        }
        
        .back-link i {
            margin-right: 8px;
        }
        
        .section-divider {
            display: flex;
            align-items: center;
            margin: 15px 0;
            color: var(--gray-color);
        }
        
        .section-divider:before,
        .section-divider:after {
            content: "";
            flex: 1;
            height: 1px;
            background-color: #dee2e6;
        }
        
        .section-divider span {
            padding: 0 10px;
            font-size: 14px;
            font-weight: 500;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .avatar, .avatar-placeholder {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Վերադառնալ վահանակ
        </a>
        
        <h2>Իմ Պրոֆիլը</h2>
        
        <?php if (isset($message)): ?>
            <div class="message message-success">
                <i class="fas fa-check-circle"></i> <?= $message ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message message-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="avatar-container">
            <?php if ($user['avatar']): ?>
                <img src="uploads/<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="avatar">
            <?php else: ?>
                <div class="avatar-placeholder">
                    <i class="fas fa-user"></i>
                </div>
            <?php endif; ?>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Անուն" required>
            </div>
            
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Էլ. հասցե" required>
            </div>
            
            <div class="form-group">
                <label for="avatar-upload" class="file-upload">
                    <input type="file" name="avatar" id="avatar-upload" accept="image/*">
                    <span class="file-upload-label">
                        <div class="fas fa-cloud-upload-alt"></div> 
                        <p style="margin: 3px;">Ընտրել նկար</p>
                        <span class="file-name"></span>
                    </span>
                </label>
            </div>
            
            <div class="section-divider">
                <span>Գաղտնաբառի թարմացում</span>
            </div>
            
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Նոր գաղտնաբառ">
                <small>Թողեք դատարկ, եթե չեք ցանկանում փոխել գաղտնաբառը</small>
            </div>
            
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password_confirm" placeholder="Հաստատեք նոր գաղտնաբառը">
            </div>
            
            <button type="submit">
                <i class="fas fa-save"></i> Թարմացնել
            </button>
        </form>

        <div class="links-container">
            <a href="add_question.php" class="link">
                <i class="fas fa-plus-circle"></i> Ավելացնել հարց
            </a>
            <a href="questions.php" class="link">
                <i class="fas fa-book"></i> Բոլոր հարցերը
            </a>
            <a href="logout.php" class="link link-logout">
                <i class="fas fa-sign-out-alt"></i> Ելք
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('avatar-upload');
            const fileName = document.querySelector('.file-name');
            const form = document.querySelector('form');
            const passwordInput = document.querySelector('input[name="password"]');
            const confirmInput = document.querySelector('input[name="password_confirm"]');
            
            form.addEventListener('submit', function(e) {
                if (passwordInput.value && passwordInput.value !== confirmInput.value) {
                    e.preventDefault();
                    alert('Գաղտնաբառերը չեն համընկնում։');
                    confirmInput.focus();
                }
            });
            
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    fileName.textContent = this.files[0].name;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const avatarContainer = document.querySelector('.avatar-container');
                    
                        avatarContainer.innerHTML = '';
                    
                        const newAvatar = document.createElement('img');
                        newAvatar.src = e.target.result;
                        newAvatar.className = 'avatar';
                        newAvatar.style.opacity = '0';
                        avatarContainer.appendChild(newAvatar);
                        
                        setTimeout(() => {
                            newAvatar.style.opacity = '1';
                        }, 50);
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
            
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                group.style.opacity = '0';
                group.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    group.style.opacity = '1';
                    group.style.transform = 'translateY(0)';
                }, 200 + (index * 100));
            });
            
            const button = document.querySelector('button');
            button.style.opacity = '0';
            button.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                button.style.opacity = '1';
                button.style.transform = 'translateY(0)';
            }, 500);
            
            const links = document.querySelectorAll('.link');
            links.forEach((link, index) => {
                link.style.opacity = '0';
                link.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    link.style.opacity = '1';
                    link.style.transform = 'translateX(0)';
                }, 600 + (index * 100));
            });
        });
    </script>
</body>
</html>


dashboard


<?php
require 'config.php';
require 'classes/User.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userObj = new User($pdo);
$user = $userObj->getById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Իմ Վահանակը</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Armenian:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --danger-color: #e63946;
            --success-color: #2a9d8f;
            --warning-color: #f4a261;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Noto Sans Armenian', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-color);
            padding: 20px;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }
        
        h2 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 28px;
            position: relative;
            display: inline-block;
        }
        
        h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 2px;
        }
        
        .avatar-container {
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }
        
        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .avatar:hover {
            transform: scale(1.05);
        }
        
        .avatar-container::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 30px;
            height: 30px;
            background: var(--success-color);
            border-radius: 50%;
            border: 3px solid white;
        }
        
        .menu-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 350px;
            margin: auto;
        }
        
        .btn {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background: white;
            color: var(--dark-color);
            text-decoration: none;
            padding: 15px 20px;
            border-radius: 10px;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary-color);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            z-index: -1;
            transition: width 0.4s ease;
        }
        
        .btn:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn:hover::before {
            width: 100%;
        }
        
        .btn i {
            margin-right: 15px;
            font-size: 18px;
            transition: transform 0.3s ease;
        }
        
        .btn:hover i {
            transform: scale(1.2);
        }
        
        .btn-logout {
            border-left: 4px solid var(--danger-color);
        }
        
        .btn-logout::before {
            background: linear-gradient(90deg, var(--danger-color) 0%, #ff758f 100%);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .avatar {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Բարի գալուստ, <?= htmlspecialchars($user['name']) ?>!</h2>

        <?php if ($user['avatar']): ?>
            <div class="avatar-container">
                <img src="uploads/<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="avatar">
            </div>
        <?php endif; ?>

        <div class="menu-container">
            <a href="profile.php" class="btn">
                <i class="fas fa-user"></i> Իմ Պրոֆիլը
            </a>
            <a href="add_question.php" class="btn">
                <i class="fas fa-plus-circle"></i> Ավելացնել Հարց
            </a>
            <a href="questions.php" class="btn">
                <i class="fas fa-book"></i> Տեսնել Բոլոր Հարցերը
            </a>
            <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i> Ելք
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn');
            
            buttons.forEach((btn, index) => {
                btn.style.opacity = '0';
                btn.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    btn.style.opacity = '1';
                    btn.style.transform = 'translateY(0)';
                }, 300 + (index * 100));
            });
        });
    </script>
</body>
</html>


config php 
<?php
$host = "localhost";
$dbname = "forum_db";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

add comment php
<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $question_id = $_POST['question_id'];
    $comment = htmlspecialchars(trim($_POST['comment']));

    $stmt = $conn->prepare("INSERT INTO comments (question_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $question_id, $user_id, $comment);
    $stmt->execute();
}

header("Location: question.php?id=" . $_POST['question_id']);
exit;



db. php
<?php
$host = 'localhost';
$db   = 'forum_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
?>



login.php
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

coment .php
    <?php
class Comment {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getByQuestionId($questionId) {
        $stmt = $this->pdo->prepare("SELECT c.*, u.name, u.avatar 
                                     FROM comments c 
                                     JOIN users u ON c.user_id = u.id 
                                     WHERE c.question_id = ? 
                                     ORDER BY c.created_at ASC");
        $stmt->execute([$questionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function add($questionId, $userId, $content, $parentId = null) {
        $sql = "INSERT INTO comments (content, user_id, question_id, parent_id, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$content, $userId, $questionId, $parentId]);
    }
    
    public function getAllComments() {
        $sql = "SELECT * FROM comments ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCommentsByUserId($userId) {
        $sql = "SELECT * FROM comments WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

question. php

<?php
class Question {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function add($user_id, $title, $body) {
        $stmt = $this->pdo->prepare("INSERT INTO questions (user_id, title, body) VALUES (?, ?, ?)");
        return $stmt->execute([$user_id, $title, $body]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT q.*, u.name, u.avatar
            FROM questions q
            JOIN users u ON q.user_id = u.id
            ORDER BY q.created_at DESC
        ");
        return $stmt->fetchAll();
    }
}
?>

user. php

<?php
class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getById($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function register($name, $email, $password, $avatar = '') {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            return false;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (name, email, password, avatar, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $email, $hashedPassword, $avatar]);
    }
    
    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

index php


<?php
require 'config.php';
require 'classes/User.php';
require 'classes/Question.php';
require 'classes/Comment.php';

session_start();
$user = new User($pdo);
$question = new Question($pdo);
$comment = new Comment($pdo);

$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = null;

if ($isLoggedIn) {
    $currentUser = $user->getUserById($_SESSION['user_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_question']) && $isLoggedIn) {
    $title = $_POST['title'];
    $body = $_POST['body'];
    
    if ($question->add($_SESSION['user_id'], $title, $body)) {
        $successMessage = "Հարցը հաջողությամբ հրապարակվեց։";
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=' . urlencode($successMessage));
        exit;
    } else {
        $error = "Հարցը չի հաջողվել հրապարակել։";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment']) && $isLoggedIn) {
    $content = $_POST['content'];
    $questionId = $_POST['question_id'];
    $parentId = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;
    
    if ($comment->add($questionId, $_SESSION['user_id'], $content, $parentId)) {
        $successMessage = "Մեկնաբանությունը հաջողությամբ հրապարակվեց։";
        header('Location: ' . $_SERVER['PHP_SELF'] . '?question=' . $questionId . '&success=' . urlencode($successMessage));
        exit;
    } else {
        $error = "Մեկնաբանությունը չի հաջողվել։";
    }
}

$questions = $question->getAll();

$currentQuestion = null;
$questionComments = [];
if (isset($_GET['question']) && is_numeric($_GET['question'])) {
    foreach ($questions as $q) {
        if ($q['id'] == $_GET['question']) {
            $currentQuestion = $q;
            break;
        }
    }
    
    if ($currentQuestion) {
        $questionComments = $comment->getByQuestionId($_GET['question']);
    }
}

$successMessage = isset($_GET['success']) ? $_GET['success'] : null;
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Հարցեր և մեկնաբանություններ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <style>
    :root {
        --primary-color: #4361ee;
        --primary-light: #4895ef;
        --secondary-color: #3a0ca3;
        --accent-color: #f72585;
        --error-color: #ef476f;
        --success-color: #06d6a0;
        --warning-color: #ffd166;
        --bg-color: #f9fafb;
        --bg-secondary: #ffffff;
        --text-color: #1f2937;
        --text-secondary: #6b7280;
        --border-color: #e5e7eb;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --border-radius-sm: 8px;
        --border-radius: 12px;
        --border-radius-lg: 16px;
        --gradient-primary: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        --gradient-accent: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    }
    
    body {
        background-color: var(--bg-color);
        color: var(--text-color);
        line-height: 1.6;
        padding-bottom: 3rem;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(67, 97, 238, 0.05) 0%, transparent 20%),
            radial-gradient(circle at 90% 80%, rgba(247, 37, 133, 0.05) 0%, transparent 20%);
        min-height: 100vh;
    }
    
    header {
        background: var(--gradient-primary);
        color: white;
        padding: 1rem 0;
        box-shadow: var(--shadow-lg);
        position: sticky;
        top: 0;
        z-index: 100;
        backdrop-filter: blur(10px);
        background-color: rgba(58, 12, 163, 0.95);
        margin-bottom: 2.5rem;
    }
    
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }
    
    .logo {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .logo i {
        font-size: 1.5rem;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    .nav-links {
        display: flex;
        gap: 2rem;
        align-items: center;
    }
    
    .nav-links a {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        font-weight: 500;
        padding: 0.5rem 0;
        position: relative;
        transition: var(--transition);
    }
    
    .nav-links a:hover {
        color: white;
        transform: translateY(-1px);
    }
    
    .nav-links a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: white;
        transition: width 0.3s ease;
        border-radius: 2px;
    }
    
    .nav-links a:hover::after {
        width: 100%;
    }
    
    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        border: 2px solid white;
    }
    
    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }
    
    .page-title {
        margin-bottom: 2rem;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.5px;
        position: relative;
        display: inline-block;
    }
    
    .page-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 60px;
        height: 4px;
        background: var(--accent-color);
        border-radius: 2px;
    }
    
    .error-message {
        color: var(--error-color);
        background: linear-gradient(135deg, rgba(239, 71, 111, 0.1) 0%, rgba(239, 71, 111, 0.05) 100%);
        padding: 1rem 1.25rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid rgba(239, 71, 111, 0.2);
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .success-message {
        color: var(--success-color);
        background: linear-gradient(135deg, rgba(6, 214, 160, 0.1) 0%, rgba(6, 214, 160, 0.05) 100%);
        padding: 1rem 1.25rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid rgba(6, 214, 160, 0.2);
        animation: slideIn 0.3s ease;
    }
    
    .form-container {
        background: var(--bg-secondary);
        padding: 2rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-lg);
        margin-bottom: 2.5rem;
        border: 1px solid var(--border-color);
        transition: var(--transition);
    }
    
    .form-container:hover {
        box-shadow: var(--shadow-lg), 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .form {
        display: flex;
        flex-direction: column;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.75rem;
        font-weight: 600;
        color: var(--text-color);
        font-size: 0.95rem;
    }
    
    .form-control {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid var(--border-color);
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: var(--transition);
        background: var(--bg-color);
        color: var(--text-color);
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
        outline: none;
        background: white;
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 120px;
        line-height: 1.5;
    }
    
    .btn {
        background: var(--gradient-primary);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: var(--border-radius);
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        align-self: flex-start;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
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
        transition: left 0.5s;
    }
    
    .btn:hover::before {
        left: 100%;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }
    
    .btn-secondary {
        background: var(--text-secondary);
    }
    
    .btn-accent {
        background: var(--gradient-accent);
    }
    
    .login-message {
        text-align: center;
        padding: 2.5rem;
        background: linear-gradient(135deg, rgba(67, 97, 238, 0.08) 0%, rgba(58, 12, 163, 0.04) 100%);
        border-radius: var(--border-radius-lg);
        margin-bottom: 2.5rem;
        border: 2px dashed var(--primary-light);
    }
    
    .login-message a {
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        position: relative;
        transition: var(--transition);
    }
    
    .login-message a:hover {
        color: var(--secondary-color);
    }
    
    .login-message a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--primary-color);
        transition: width 0.3s ease;
    }
    
    .login-message a:hover::after {
        width: 100%;
    }
    
    .tab-container {
        margin-bottom: 3rem;
    }
    
    .tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        background: var(--bg-secondary);
        padding: 0.5rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }
    
    .tab {
        padding: 1rem 2rem;
        cursor: pointer;
        font-weight: 600;
        color: var(--text-secondary);
        border-radius: var(--border-radius);
        transition: var(--transition);
        flex: 1;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .tab:hover {
        color: var(--primary-color);
        background: var(--bg-color);
    }
    
    .tab.active {
        background: var(--gradient-primary);
        color: white;
        box-shadow: var(--shadow-md);
    }
    
    .tab-content {
        display: none;
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .tab-content.active {
        display: block;
    }
    
    .questions-list {
        list-style: none;
        display: grid;
        gap: 1.5rem;
    }
    
    .question-item {
        background: var(--bg-secondary);
        padding: 1.75rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-md);
        cursor: pointer;
        transition: var(--transition);
        border: 1px solid transparent;
        position: relative;
        overflow: hidden;
    }
    
    .question-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--primary-color);
        transform: scaleY(0);
        transition: transform 0.3s ease;
        border-radius: 2px;
    }
    
    .question-item:hover::before {
        transform: scaleY(1);
    }
    
    .question-item:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-light);
    }
    
    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }
    
    .question-author {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .author-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        background: var(--gradient-primary);
        border: 2px solid white;
        box-shadow: var(--shadow-sm);
    }
    
    .author-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .author-name {
        font-weight: 600;
        color: var(--text-color);
    }
    
    .question-date {
        font-size: 0.85rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .question-title {
        font-size: 1.375rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text-color);
        line-height: 1.4;
    }
    
    .question-body {
        margin-bottom: 1.25rem;
        overflow-wrap: break-word;
        word-wrap: break-word;
        hyphens: auto;
        color: var(--text-secondary);
        line-height: 1.6;
    }
    
    .question-actions {
        display: flex;
        gap: 1.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
    }
    
    .question-action {
        font-size: 0.9rem;
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
        text-decoration: none;
        font-weight: 500;
    }
    
    .question-action:hover {
        color: var(--primary-color);
        transform: translateX(2px);
    }
    
    .question-detail {
        background: var(--bg-secondary);
        padding: 2.5rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-lg);
        margin-bottom: 2.5rem;
        border: 1px solid var(--border-color);
        position: relative;
    }
    
    .question-detail::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
        border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
    }
    
    .comments-container {
        margin-top: 3rem;
    }
    
    .comments-list {
        list-style: none;
        display: grid;
        gap: 1.5rem;
    }
    
    .comment {
        background: var(--bg-color);
        padding: 1.75rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        transition: var(--transition);
    }
    
    .comment:hover {
        box-shadow: var(--shadow-md);
    }
    
    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .comment-author {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .comment-date {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }
    
    .comment-content {
        margin-bottom: 1.25rem;
        overflow-wrap: break-word;
        word-wrap: break-word;
        hyphens: auto;
        line-height: 1.7;
    }
    
    .comment-actions {
        display: flex;
        gap: 1.5rem;
        margin-top: 1rem;
    }
    
    .comment-action {
        font-size: 0.9rem;
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
        font-weight: 500;
    }
    
    .comment-action:hover {
        color: var(--primary-color);
        transform: translateY(-1px);
    }
    
    .reply-form {
        margin-top: 1.5rem;
        margin-left: 3rem;
        display: none;
        animation: slideIn 0.3s ease;
    }
    
    .reply-textarea {
        min-height: 100px;
    }
    
    .replies {
        margin-top: 1.5rem;
        margin-left: 3rem;
        display: grid;
        gap: 1rem;
    }
    
    .reply {
        background: white;
        padding: 1.5rem;
        border-radius: var(--border-radius);
        border-left: 3px solid var(--primary-light);
        box-shadow: var(--shadow-sm);
    }
    
    .no-content {
        text-align: center;
        padding: 3rem 2rem;
        background: linear-gradient(135deg, var(--bg-color) 0%, var(--bg-secondary) 100%);
        border-radius: var(--border-radius-lg);
        color: var(--text-secondary);
        font-style: italic;
        border: 2px dashed var(--border-color);
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 2rem;
        padding: 0.75rem 1.5rem;
        background: var(--bg-color);
        border-radius: var(--border-radius);
        transition: var(--transition);
        border: 1px solid var(--border-color);
    }
    
    .back-link:hover {
        background: white;
        transform: translateX(-4px);
        box-shadow: var(--shadow-md);
        color: var(--secondary-color);
    }
    
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        background: var(--gradient-accent);
        color: white;
        margin-left: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .header-container {
            flex-direction: column;
            gap: 1rem;
            padding: 1rem;
        }
        
        .nav-links {
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }
        
        .container {
            padding: 0 1rem;
        }
        
        .page-title {
            font-size: 2rem;
        }
        
        .tabs {
            flex-direction: column;
        }
        
        .replies {
            margin-left: 1rem;
        }
        
        .reply-form {
            margin-left: 1rem;
        }
        
        .question-detail,
        .form-container {
            padding: 1.5rem;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">Հարցեր և մեկնաբանություններ</div>
            <nav class="nav-links">
                <a href="index.php">Գլխավոր</a>
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php">Իմ էջը</a>
                    <a href="logout.php">Դուրս գալ</a>
                <?php else: ?>
                    <a href="login.php">Մուտք</a>
                    <a href="register.php">Գրանցվել</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if (isset($successMessage)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($currentQuestion): ?>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> Բոլոր հարցերը
            </a>
            
            <div class="question-detail">
                <div class="question-header">
                    <div class="question-author">
                        <?php if (!empty($currentQuestion['avatar'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($currentQuestion['avatar']); ?>" alt="Avatar" class="author-avatar">
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-2x" style="color: #6c757d;"></i>
                        <?php endif; ?>
                        
                        <div class="author-info">
                            <span class="author-name"><?php echo htmlspecialchars($currentQuestion['name']); ?></span>
                            <span class="question-date"><?php echo date('d.m.Y H:i', strtotime($currentQuestion['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
                
                <h2 class="question-title"><?php echo htmlspecialchars($currentQuestion['title']); ?></h2>
                
                <div class="question-body">
                    <?php echo nl2br(htmlspecialchars($currentQuestion['body'])); ?>
                </div>
            </div>
            
            <?php if ($isLoggedIn): ?>
                <div class="form-container">
                    <h3>Ձեր պատասխանը</h3>
                    <form method="POST" class="form">
                        <input type="hidden" name="question_id" value="<?php echo $currentQuestion['id']; ?>">
                        <div class="form-group">
                            <textarea name="content" class="form-control" placeholder="Գրեք ձեր պատասխանը այստեղ..." required></textarea>
                        </div>
                        <button type="submit" name="submit_comment" class="btn">Հրապարակել պատասխանը</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="login-message">
                    <p>Պատասխանելու համար անհրաժեշտ է <a href="login.php">մուտք գործել</a> կամ <a href="register.php">գրանցվել</a>։</p>
                </div>
            <?php endif; ?>
            
            <div class="comments-container">
                <h3>Պատասխաններ (<?php echo count($questionComments); ?>)</h3>
                
                <?php if (empty($questionComments)): ?>
                    <div class="no-content">
                        <p>Այս հարցին դեռևս պատասխաններ չկան։ Եղեք առաջինը, ով կպատասխանի։</p>
                    </div>
                <?php else: ?>
                    <ul class="comments-list">
                        <?php foreach ($questionComments as $commentItem): ?>
                            <?php if ($commentItem['parent_id'] === null):  ?>
                                <li class="comment">
                                    <div class="comment-header">
                                        <div class="comment-author">
                                            <?php if (!empty($commentItem['avatar'])): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($commentItem['avatar']); ?>" alt="Avatar" class="author-avatar">
                                            <?php else: ?>
                                                <i class="fas fa-user-circle fa-2x" style="color: #6c757d;"></i>
                                            <?php endif; ?>
                                            
                                            <div class="author-info">
                                                <span class="author-name"><?php echo htmlspecialchars($commentItem['name']); ?></span>
                                                <span class="comment-date"><?php echo date('d.m.Y H:i', strtotime($commentItem['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="comment-content">
                                        <?php echo nl2br(htmlspecialchars($commentItem['content'])); ?>
                                    </div>
                                    
                                    <div class="comment-actions">
                                        <?php if ($isLoggedIn): ?>
                                            <a href="#" class="comment-action reply-toggle" data-comment-id="<?php echo $commentItem['id']; ?>">
                                                <i class="fas fa-reply"></i> Պատասխանել
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($isLoggedIn): ?>
                                        <div id="reply-form-<?php echo $commentItem['id']; ?>" class="reply-form">
                                            <form method="POST">
                                                <input type="hidden" name="question_id" value="<?php echo $currentQuestion['id']; ?>">
                                                <input type="hidden" name="parent_id" value="<?php echo $commentItem['id']; ?>">
                                                <div class="form-group">
                                                    <textarea name="content" class="form-control reply-textarea" placeholder="Ձեր պատասխանը..." required></textarea>
                                                </div>
                                                <div style="display: flex; gap: 10px;">
                                                    <button type="submit" name="submit_comment" class="btn">Ուղարկել</button>
                                                    <button type="button" class="btn btn-secondary cancel-reply" data-comment-id="<?php echo $commentItem['id']; ?>">Չեղարկել</button>
                                                </div>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $replies = array_filter($questionComments, function($reply) use ($commentItem) {
                                        return $reply['parent_id'] === $commentItem['id'];
                                    });
                                    
                                    if (!empty($replies)):
                                    ?>
                                    <div class="replies">
                                        <?php foreach ($replies as $reply): ?>
                                            <div class="reply">
                                                <div class="comment-header">
                                                    <div class="comment-author">
                                                        <?php if (!empty($reply['avatar'])): ?>
                                                            <img src="uploads/<?php echo htmlspecialchars($reply['avatar']); ?>" alt="Avatar" class="author-avatar">
                                                        <?php else: ?>
                                                            <i class="fas fa-user-circle fa-2x" style="color: #6c757d;"></i>
                                                        <?php endif; ?>
                                                        
                                                        <div class="author-info">
                                                            <span class="author-name"><?php echo htmlspecialchars($reply['name']); ?></span>
                                                            <span class="comment-date"><?php echo date('d.m.Y H:i', strtotime($reply['created_at'])); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="comment-content">
                                                    <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
        <?php else: ?>
            <h1 class="page-title">Հարցեր և մեկնաբանություններ</h1>
            
            <div class="tab-container">
                <div class="tabs">
                    <div class="tab active" data-tab="questions">Հարցեր</div>
                    <div class="tab" data-tab="ask-question">Հարց տալ</div>
                </div>
                
                <div id="questions" class="tab-content active">
                    <?php if (empty($questions)): ?>
                        <div class="no-content">
                            <p>Դեռևս հարցեր չկան։ Եղեք առաջինը, ով կտա հարց։</p>
                        </div>
                    <?php else: ?>
                        <ul class="questions-list">
                            <?php foreach ($questions as $questionItem): ?>
                                <li class="question-item" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF'] . '?question=' . $questionItem['id']; ?>'">
                                    <div class="question-header">
                                        <div class="question-author">
                                            <?php if (!empty($questionItem['avatar'])): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($questionItem['avatar']); ?>" alt="Avatar" class="author-avatar">
                                            <?php else: ?>
                                                <i class="fas fa-user-circle fa-2x" style="color: #6c757d;"></i>
                                            <?php endif; ?>
                                            
                                            <div class="author-info">
                                                <span class="author-name"><?php echo htmlspecialchars($questionItem['name']); ?></span>
                                                <span class="question-date"><?php echo date('d.m.Y H:i', strtotime($questionItem['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h3 class="question-title"><?php echo htmlspecialchars($questionItem['title']); ?></h3>
                                    
                                    <div class="question-body">
                                        <?php 
                                        $bodyPreview = strlen($questionItem['body']) > 200 ? 
                                            substr($questionItem['body'], 0, 200) . '...' : 
                                            $questionItem['body'];
                                        echo nl2br(htmlspecialchars($bodyPreview)); 
                                        ?>
                                    </div>
                                    
                                    <div class="question-actions">
                                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?question=' . $questionItem['id']; ?>" class="question-action">
                                            <i class="fas fa-comments"></i> Պատասխանել
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                
                <div id="ask-question" class="tab-content">
                    <?php if ($isLoggedIn): ?>
                        <div class="form-container">
                            <form method="POST" class="form">
                                <div class="form-group">
                                    <label for="title">Հարցի վերնագիրը</label>
                                    <input type="text" id="title" name="title" class="form-control" placeholder="Հարցի վերնագիրը..." required>
                                </div>
                                <div class="form-group">
                                    <label for="body">Հարցի մանրամասները</label>
                                    <textarea id="body" name="body" class="form-control" placeholder="Նկարագրեք ձեր հարցը մանրամասն..." required></textarea>
                                </div>
                                <button type="submit" name="submit_question" class="btn">Հրապարակել հարցը</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="login-message">
                            <p>Հարց տալու համար անհրաժեշտ է <a href="login.php">մուտք գործել</a> կամ <a href="register.php">գրանցվել</a>։</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    
                    this.classList.add('active');
                    const tabContent = document.getElementById(this.dataset.tab);
                    if (tabContent) {
                        tabContent.classList.add('active');
                    }
                });
            });
            
            const replyToggles = document.querySelectorAll('.reply-toggle');
            const cancelButtons = document.querySelectorAll('.cancel-reply');
            
            replyToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const commentId = this.dataset.commentId;
                    const replyForm = document.getElementById(`reply-form-${commentId}`);
                    
            
                    document.querySelectorAll('.reply-form').forEach(form => {
                        if (form !== replyForm) {
                            form.style.display = 'none';
                        }
                    });
                    
                    replyForm.style.display = replyForm.style.display === 'block' ? 'none' : 'block';
                    
                    if (replyForm.style.display === 'block') {
                        replyForm.querySelector('textarea').focus();
                    }
                });
            });
            
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const commentId = this.dataset.commentId;
                    const replyForm = document.getElementById(`reply-form-${commentId}`);
                    replyForm.style.display = 'none';
                });
            });
        });
    </script>
</body>
</html>
