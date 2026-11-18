<?php
require 'includes/functions.php';
if (logged_in()) redirect('appointments.php');
$error=''; $name=''; $email='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf(); $name=trim((string)($_POST['name']??'')); $email=trim((string)($_POST['email']??'')); $password=(string)($_POST['password']??'');
 if (mb_strlen($name)<2 || mb_strlen($name)>100) $error='Аты-жөні 2–100 таңба болуы тиіс.';
 elseif (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>190) $error='Email дұрыс емес.';
 elseif (strlen($password)<8) $error='Құпиясөз кемінде 8 таңба болуы тиіс.';
 else {
  require 'includes/db.php';
  try {
   $stmt=$pdo->prepare('INSERT INTO users (name,email,password_hash) VALUES (?,?,?)');
   $stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
   session_regenerate_id(true); $_SESSION['user_id']=(int)$pdo->lastInsertId(); $_SESSION['user_name']=$name;
   redirect('appointments.php');
  } catch (PDOException $e) { if ($e->getCode()==='23000') $error='Бұл email бұрын тіркелген.'; else throw $e; }
 }
}
$page_title='Тіркелу'; require 'includes/header.php'; ?>
<section class="form-panel"><h1>Тіркелу</h1><p>Жеке кабинетіңізді ашыңыз.</p><?php if($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Аты-жөні<input name="name" required minlength="2" maxlength="100" value="<?= e($name) ?>"></label><label>Email<input name="email" type="email" required maxlength="190" value="<?= e($email) ?>"></label><label>Құпиясөз<input name="password" type="password" minlength="8" required></label><button class="btn">Тіркелу</button></form><p>Аккаунтыңыз бар ма? <a href="login.php">Кіру</a></p></section>
<?php require 'includes/footer.php'; ?>
