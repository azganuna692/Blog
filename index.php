<?php
session_start();
?>
ABOUT
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Մեր մասին</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <nav>
                <ul>
                    <li><a href="index.php">Գլխավոր</a></li>
                    <li><a href="about.php" class="active">Մեր մասին</a></li>
                    <li><a href="contact.php">Կապ</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <h1>Մեր մասին</h1>
            
            <div class="content-section">
                <p>Բարի գալուստ մեր կայք։ Մենք մասնագիտացած ենք վեբ ծրագրավորման և PHP-ի մեջ։</p>
                
                <h2>Նախագծի նկարագրություն</h2>
                <p>Այս PHP նախագիծը ներառում է ձևի վավերացման տարբեր մեթոդներ՝ ապահովելով տվյալների ճիշտ մուտքագրումը։</p>
            </div>
            
            <div class="features">
                <h2>Նախագծի հատկություններ</h2>
                <ul>
                    <li>✅ Պարտադիր դաշտերի ստուգում</li>
                    <li>✅ Անուն/ազգանուն վավերացում (միայն տառեր)</li>
                    <li>✅ Էլ. փոստի ֆորմատի ստուգում</li>
                    <li>✅ Տարիքի ստուգում (18+)</li>
                    <li>✅ Հեռախոսահամարի ֆորմատի ստուգում (+374 00 000 000)</li>
                    <li>✅ Գաղտնաբառի համընկնման ստուգում</li>
                    <li>✅ CSS սթայլինգ</li>
                    <li>✅ Բազմաէջ կառուցվածք</li>
                </ul>
            </div>

            <div class="technology">
                <h2>Օգտագործված տեխնոլոգիաներ</h2>
                <div class="tech-list">
                    <span class="tech-item">PHP</span>
                    <span class="tech-item">HTML5</span>
                    <span class="tech-item">CSS3</span>
                    <span class="tech-item">JavaScript</span>
                    <span class="tech-item">MySQL (հնարավոր է)</span>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Իմ Կայքը։ Բոլոր իրավունքները պաշտպանված են։</p>
        </footer>
    </div>
</body>
</html><?php
session_start();

$contact_errors = [];
$contact_success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contact_submit'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Ստուգումներ
    if (empty($name) || empty($email) || empty($message)) {
        $contact_errors[] = "Բոլոր դաշտերը պարտադիր են";
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contact_errors[] = "Էլ. փոստի ֆորմատը սխալ է";
    }
    
    if (empty($contact_errors)) {
        $contact_success = "Շնորհակալություն։ Ձեր հաղորդագրությունը ստացվել է։";
        // Այստեղ կարող եք ավելացնել կոդ՝ հաղորդագրությունը պահելու համար
    }
}
KAP
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Կապ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <nav>
                <ul>
                    <li><a href="index.php">Գլխավոր</a></li>
                    <li><a href="about.php">Մեր մասին</a></li>
                    <li><a href="contact.php" class="active">Կապ</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <h1>Կապ մեզ հետ</h1>
            
            <?php if (!empty($contact_errors)): ?>
                <div class="errors">
                    <?php foreach ($contact_errors as $error): ?>
                        <div class="error"><?= $error ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($contact_success)): ?>
                <div class="success"><?= $contact_success ?></div>
            <?php endif; ?>

            <div class="contact-container">
                <div class="contact-info">
                    <h2>Կոնտակտային տվյալներ</h2>
                    <div class="contact-item">
                        <strong>📞 Հեռախոս:</strong> +374 10 123456
                    </div>
                    <div class="contact-item">
                        <strong>📧 Էլ. փոստ:</strong> info@example.com
                    </div>
                    <div class="contact-item">
                        <strong>🏢 Հասցե:</strong> Երևան, Հայաստան
                    </div>
                    <div class="contact-item">
                        <strong>🕒 Աշխատանքային ժամեր:</strong> Երկ-Ուրբ 09:00-18:00
                    </div>
                </div>

                <div class="contact-form">
                    <h2>Կապնվել մեզ հետ</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Անուն *</label>
                            <input type="text" id="name" name="name" 
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                                   placeholder="Ձեր անունը" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Էլ. փոստ *</label>
                            <input type="email" id="email" name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                                   placeholder="example@mail.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Հաղորդագրություն *</label>
                            <textarea id="message" name="message" rows="5" 
                                      placeholder="Ձեր հաղորդագրությունը..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" name="contact_submit">Ուղարկել հաղորդագրություն</button>
                    </form>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Իմ Կայքը։ Բոլոր իրավունքները պաշտպանված են։</p>
        </footer>
    </div>
</body>
</html><?php
// Սկիզբում ավելացնել այս տողը
echo "PHP Server Started Successfully!\n\n";

session_start();
?>
INDEX.PHP
<!DOCTYPE html>
<html>
<head>
    <title>Գլխավոր</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f0f0f0; padding: 20px; }
        nav { background: #2c3e50; padding: 1rem; border-radius: 10px; margin-bottom: 20px; }
        nav ul { list-style: none; display: flex; justify-content: center; flex-wrap: wrap; }
        nav li { margin: 5px 15px; }
        nav a { color: white; text-decoration: none; font-weight: bold; padding: 10px; display: block; }
        nav a:hover { background: rgba(255,255,255,0.1); border-radius: 5px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #2c3e50; }
        input, textarea, select { width: 100%; padding: 12px; border: 2px solid #e1e8ed; border-radius: 8px; font-size: 16px; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #3498db; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); }
        button { background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 15px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px; }
        button:hover { background: linear-gradient(135deg, #2980b9, #3498db); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3); }
        .error { background: #e74c3c; color: white; padding: 12px; border-radius: 8px; margin: 10px 0; border-left: 5px solid #c0392b; }
        .success { background: #27ae60; color: white; padding: 12px; border-radius: 8px; margin: 10px 0; border-left: 5px solid #219652; text-align: center; }
        small { display: block; margin-top: 5px; color: #7f8c8d; font-size: 12px; }
        .features { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .features li { margin: 10px 0; padding: 10px; background: white; border-radius: 5px; border-left: 4px solid #3498db; }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="?page=home">🏠 Գլխավոր</a></li>
            <li><a href="?page=about">ℹ️ Մեր մասին</a></li>
            <li><a href="?page=contact">📞 Կապ</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php
        $page = $_GET['page'] ?? 'home';
        
        if ($page === 'about') {
            echo '<h1>Մեր մասին</h1>';
            echo '<div class="features">';
            echo '<p><strong>Նախագծի նկարագրություն</strong></p>';
            echo '<ul>';
            echo '<li>✅ Պարտադիր դաշտերի ստուգում</li>';
            echo '<li>✅ Անուն/ազգանուն վավերացում (միայն տառեր)</li>';
            echo '<li>✅ Էլ. փոստի ֆորմատի ստուգում</li>';
            echo '<li>✅ Հեռախոսահամարի ֆորմատի ստուգում (+374)</li>';
            echo '<li>✅ Գաղտնաբառի համընկնման ստուգում</li>';
            echo '</ul>';
            echo '</div>';
            
        } elseif ($page === 'contact') {
            echo '<h1>Կապ մեզ հետ</h1>';
            echo '<div style="background:#f8f9fa; padding:20px; border-radius:10px; margin:20px 0;">';
            echo '<h3>📞 Կոնտակտային տվյալներ</h3>';
            echo '<p><strong>Հեռախոս:</strong> +374 10 123456</p>';
            echo '<p><strong>Էլ. փոստ:</strong> info@example.com</p>';
            echo '<p><strong>Հասցե:</strong> Երևան, Հայաստան</p>';
            echo '</div>';
            
        } else {
            // Գլխավոր էջ - Ձևաթուղթ
            $errors = [];
            $success = '';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $firstName = $_POST['firstName'] ?? '';
                $lastName = $_POST['lastName'] ?? '';
                $email = $_POST['email'] ?? '';
                $birthDate = $_POST['birthDate'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirmPassword'] ?? '';
                
                // Պարտադիր դաշտեր
                if (empty($firstName) || empty($lastName) || empty($email) || empty($birthDate) || empty($phone) || empty($password) || empty($confirmPassword)) {
                    $errors[] = "Բոլոր դաշտերը պարտադիր են լրացման համար";
                }
                
                // Անուն և ազգանուն ստուգում
                if (!empty($firstName) && !preg_match("/^[a-zA-ZԱ-Ֆա-ֆ]+$/u", $firstName)) {
                    $errors[] = "Անունը պետք է պարունակի միայն տառեր";
                }
                
                if (!empty($lastName) && !preg_match("/^[a-zA-ZԱ-Ֆա-ֆ]+$/u", $lastName)) {
                    $errors[] = "Ազգանունը պետք է պարունակի միայն տառեր";
                }
                
                // Էլ. փոստի ստուգում
                if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Էլ. փոստի ֆորմատը սխալ է";
                }
                
                // Հեռախոսահամարի ստուգում
                if (!empty($phone) && !preg_match("/^\+374\s\d{2}\s\d{3}\s\d{3}$/", $phone)) {
                    $errors[] = "Հեռախոսահամարը պետք է լինի +374 00 000 000 ֆորմատով";
                }
                
                // Գաղտնաբառի ստուգում
                if (!empty($password) && !empty($confirmPassword) && $password !== $confirmPassword) {
                    $errors[] = "Գաղտնաբառերը չեն համընկնում";
                }
                
                if (empty($errors)) {
                    $success = "✅ Ձեր տվյալները հաջողությամբ ուղարկվել են!";
                }
            }
            ?>
            
            <h1>📝 Գրանցման Ձևաթուղթ</h1>
            
            <?php if ($success): ?>
                <div class="success"><?= $success ?></div>
            <?php endif; ?>

            <?php foreach ($errors as $error): ?>
                <div class="error"><?= $error ?></div>
            <?php endforeach; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="firstName">Անուն *</label>
                    <input type="text" id="firstName" name="firstName" 
                           value="<?= htmlspecialchars($_POST['firstName'] ?? '') ?>" 
                           placeholder="Մուտքագրեք ձեր անունը" required>
                    <small>Միայն տառեր, առանց թվեր կամ սիմվոլներ</small>
                </div>

                <div class="form-group">
                    <label for="lastName">Ազգանուն *</label>
                    <input type="text" id="lastName" name="lastName" 
                           value="<?= htmlspecialchars($_POST['lastName'] ?? '') ?>" 
                           placeholder="Մուտքագրեք ձեր ազգանունը" required>
                    <small>Միայն տառեր, առանց թվեր կամ սիմվոլներ</small>
                </div>

                <div class="form-group">
                    <label for="email">Էլ. փոստ *</label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                           placeholder="example@mail.com" required>
                </div>

                <div class="form-group">
                    <label for="birthDate">Ծննդյան ամսաթիվ *</label>
                    <input type="date" id="birthDate" name="birthDate" 
                           value="<?= htmlspecialchars($_POST['birthDate'] ?? '') ?>" required>
                    <small>Դուք պետք է լինեք 18 տարեկան կամ ավելի</small>
                </div>

                <div class="form-group">
                    <label for="phone">Հեռախոսահամար *</label>
                    <input type="tel" id="phone" name="phone" 
                           placeholder="+374 00 000 000"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                    <small>Ֆորմատ: +374 00 000 000</small>
                </div>

                <div class="form-group">
                    <label for="password">Գաղտնաբառ *</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Մուտքագրեք գաղտնաբառը" required>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Գաղտնաբառի հաստատում *</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" 
                           placeholder="Կրկնեք գաղտնաբառը" required>
                </div>

                <button type="submit">📤 Գրանցվել</button>
            </form>
            <?php
        }
        ?>
    </div>
</body>

</html>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', 'Segoe UI', sans-serif;
    line-height: 1.6;
    color: #333;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

header {
    background-color: rgba(44, 62, 80, 0.95);
    color: white;
    padding: 1rem 0;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
}

nav ul {
    list-style: none;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

nav ul li {
    margin: 0 25px;
}

nav ul li a {
    color: white;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    padding: 10px 15px;
    border-radius: 5px;
}

nav ul li a:hover {
    color: #3498db;
    background-color: rgba(255,255,255,0.1);
    transform: translateY(-2px);
}

nav ul li a.active {
    color: #3498db;
    background-color: rgba(255,255,255,0.15);
}

main {
    background: rgba(255, 255, 255, 0.95);
    padding: 2.5rem;
    margin: 2rem 0;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    flex: 1;
    backdrop-filter: blur(10px);
}

h1 {
    color: #2c3e50;
    margin-bottom: 2rem;
    text-align: center;
    border-bottom: 3px solid #3498db;
    padding-bottom: 1rem;
    font-size: 2.2rem;
}

h2 {
    color: #34495e;
    margin: 2rem 0 1rem 0;
    font-size: 1.5rem;
}

.form-group {
    margin-bottom: 1.8rem;
    position: relative;
}

label {
    display: block;
    margin-bottom: 0.7rem;
    font-weight: 600;
    color: #2c3e50;
    font-size: 1rem;
}

input[type="text"],
input[type="email"],
input[type="date"],
input[type="tel"],
input[type="password"],
textarea {
    width: 100%;
    padding: 1rem;
    border: 2px solid #e1e8ed;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

input:focus,
textarea:focus {
    outline: none;
    border-color: #3498db;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    transform: translateY(-2px);
}

small {
    display: block;
    margin-top: 0.4rem;
    color: #7f8c8d;
    font-size: 0.85rem;
}

button {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    padding: 1rem 2.5rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1.1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    display: block;
    margin: 2rem auto 0;
    min-width: 200px;
}

button:hover {
    transform: translateY(-3px);
    box-shadow: 0 7px 20px rgba(52, 152, 219, 0.4);
    background: linear-gradient(135deg, #2980b9, #3498db);
}

.errors {
    background-color: #e74c3c;
    color: white;
    padding: 1.2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    border-left: 5px solid #c0392b;
}

.error {
    margin-bottom: 0.5rem;
    padding-left: 1rem;
}

.success {
    background-color: #27ae60;
    color: white;
    padding: 1.2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    text-align: center;
    border-left: 5px solid #219652;
}

/* About Page Styles */
.content-section {
    margin-bottom: 2rem;
    line-height: 1.8;
}

.features ul {
    list-style: none;
    margin-left: 0;
}

.features li {
    margin-bottom: 0.8rem;
    padding: 1rem;
    background: linear-gradient(135deg, #ecf0f1, #dfe6e9);
    border-radius: 8px;
    border-left: 4px solid #3498db;
    transition: transform 0.3s ease;
}

.features li:hover {
    transform: translateX(10px);
}

.tech-list {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1rem;
}

.tech-item {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    padding: 0.7rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
}

/* Contact Page Styles */
.contact-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-top: 2rem;
}

.contact-info {
    background: linear-gradient(135deg, #ecf0f1, #dfe6e9);
    padding: 2rem;
    border-radius: 12px;
    height: fit-content;
}

.contact-item {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    border-left: 4px solid #3498db;
}

.contact-form {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

footer {
    background-color: rgba(44, 62, 80, 0.95);
    color: white;
    text-align: center;
    padding: 1.5rem;
    margin-top: auto;
    border-radius: 10px 10px 0 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    nav ul {
        flex-direction: column;
        align-items: center;
    }
    
    nav ul li {
        margin: 8px 0;
    }
    
    .container {
        padding: 0 15px;
    }
    
    main {
        padding: 1.5rem;
        margin: 1rem 0;
    }
    
    .contact-container {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    h1 {
        font-size: 1.8rem;
    }
    
    button {
        width: 100%;
        margin: 1.5rem auto 0;
    }
}

@media (max-width: 480px) {
    main {
        padding: 1rem;
    }
    
    .contact-info,
    .contact-form {
        padding: 1.5rem;
    }
    
    .tech-list {
        justify-content: center;
    }
    
    .tech-item {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
}