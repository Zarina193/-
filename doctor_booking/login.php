<?php
require 'includes/functions.php';
if(logged_in()) redirect('appointments.php');
$error=''; $email='';
if($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf(); $email=trim((string)($_POST['email']??'')); $password=(string)($_POST['password']??'');
 if(!filter_var($email,FILTER_VALIDATE_EMAIL) || $password==='') $error='Email мен құпиясөзді дұрыс енгізіңіз.';
 else {
  require 'includes/db.php'; $stmt=$pdo->prepare('SELECT id,name,password_hash FROM users WHERE email=?'); $stmt->execute([$email]); $user=$stmt->fetch();
  if(!$user || !password_verify($password,$user['password_hash'])) $error='Email немесе құпиясөз қате.';
  else { session_regenerate_id(true); $_SESSION['user_id']=(int)$user['id']; $_SESSION['user_name']=$user['name']; redirect('appointments.php'); }
 }
}
$page_title='Кіру'; require 'includes/header.php'; ?>
<section class="form-panel"><h1>Жүйеге кіру</h1><?php if($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Email<input type="email" name="email" required value="<?= e($email) ?>"></label><label>Құпиясөз<input type="password" name="password" required></label><button class="btn">Кіру</button></form><p>Аккаунтыңыз жоқ па? <a href="register.php">Тіркелу</a></p></section>
<?php require 'includes/footer.php'; ?>
