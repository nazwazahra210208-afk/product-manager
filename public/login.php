<?php
require_once '../config/db.php';

session_start();

// Jika sudah login, langsung tendang ke dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Cari user di database
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Verifikasi password hashing
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true); // Keamanan session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Luxury Inventory</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;600&display=swap');

        body { 
            font-family: 'Montserrat', sans-serif; 
            background: #ffffff; /* Latar putih bersih */
            color: #333; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0;
            background-image: radial-gradient(#fcfcfc 20%, transparent 20%), radial-gradient(#fcfcfc 20%, transparent 20%);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }
        
        .login-box { 
            background: #ffffff; 
            padding: 50px 40px; 
            width: 100%; 
            max-width: 380px; 
            border: 1px solid #eaeaea; 
            border-top: 4px solid #111111; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.05); 
            text-align: center;
        }

        .login-box h2 { 
            font-family: 'Playfair Display', serif; 
            font-size: 24px; 
            text-transform: uppercase; 
            letter-spacing: 3px; 
            color: #111; 
            margin-top: 0; 
            margin-bottom: 10px; 
        }

        .login-box p {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #888;
            margin-bottom: 35px;
        }

        .form-group { margin-bottom: 25px; text-align: left; }
        label { display: block; margin-bottom: 10px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: #111; }
        input[type="text"], input[type="password"] { 
            width: 100%; 
            padding: 14px 15px; 
            border: 1px solid #ddd; 
            background: #fcfcfc; 
            font-family: 'Montserrat', sans-serif; 
            font-size: 13px; 
            outline: none; 
            transition: all 0.3s; 
            box-sizing: border-box; 
        }
        input[type="text"]:focus, input[type="password"]:focus { border-color: #111; background: #fff; box-shadow: 0 0 0 1px #111; }
        
        .btn-login { 
            background: #111; 
            color: #d4af37; 
            padding: 16px; 
            border: none; 
            text-transform: uppercase; 
            font-size: 12px; 
            font-weight: 600; 
            letter-spacing: 2px; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            width: 100%; 
            margin-top: 10px; 
        }
        .btn-login:hover { background: #d4af37; color: #111; }
        
        .alert { padding: 12px; margin-bottom: 20px; font-size: 12px; border-left: 3px solid #8b0000; background: #fdf5f5; color: #8b0000; text-align: left; }
    </style>
</head>
<body>

    <div class="login-box">
        <h2>Inventory</h2>
        <p>Exclusive Management System</p>

        <?php if (isset($error)): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Masuk (Sign In)</button>
        </form>
    </div>

</body>
</html>