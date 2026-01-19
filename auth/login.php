<?php
/**
 * Login Page
 * 
 * Simple authentication system using PHP sessions.
 */

require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../data/seed.php';

// Handle logout
if (isset($_GET['logout'])) {
    logout();
    header('Location: /VulnHub/auth/login.php');
    exit;
}

// Redirect if already logged in
if (is_authenticated()) {
    header('Location: /VulnHub/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $user = get_user_by_username($username);
        
        if ($user && $user['password'] === $password) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            
            header('Location: /VulnHub/index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

require_once __DIR__ . '/../assets/warning_banner.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #5568d3;
        }
        .error {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #fcc;
        }
        .demo-accounts {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
        .demo-accounts h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .demo-accounts ul {
            list-style: none;
            padding-left: 0;
        }
        .demo-accounts li {
            margin: 5px 0;
            padding: 5px;
            background: #f5f5f5;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <?php display_warning_banner(); ?>
    <div class="login-container">
        <h1>Healthcare Management System</h1>
        <p class="subtitle">Educational Lab - Login</p>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <div class="demo-accounts">
            <h3>Demo Accounts:</h3>
            <ul>
                <li><strong>Doctor:</strong> dr_smith / password123</li>
                <li><strong>Patient:</strong> patient_alice / password123</li>
                <li><strong>Lab Tech:</strong> lab_tech / password123</li>
                <li><strong>Billing:</strong> billing_officer / password123</li>
                <li><strong>Admin:</strong> admin / password123</li>
            </ul>
        </div>
    </div>
</body>
</html>
