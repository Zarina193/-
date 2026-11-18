<?php
require 'includes/functions.php'; require_login(); require 'includes/db.php';
$error=''; $a=['doctor_id'=>0,'date'=>'','time'=>'','reason'=>''];
$doctors=$pdo->query('SELECT id,name,specialty FROM doctors ORDER BY name')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf(); $a=appointment_input(); $error=appointment_error($pdo,$a);
 if(!$error) {
  try {
   $stmt=$pdo->prepare('INSERT INTO appointments (user_id,doctor_id,appointment_date,appointment_time,reason) VALUES (?,?,?,?,?)');
   $stmt->execute([$_SESSION['user_id'],$a['doctor_id'],$a['date'],$a['time'],$a['reason']]); redirect('appointments.php?ok=1');
  } catch(PDOException $e) { if($e->getCode()==='23000') $error='Бұл дәрігер осы уақытта бос емес.'; else throw $e; }
 }
}
$page_title='Жаңа жазылу'; $button_text='Жазылуды сақтау'; require 'includes/header.php'; ?>
<section class="form-panel wide"><h1>Дәрігерге жазылу</h1><p>Күн мен уақытты таңдаңыз (09:00–17:00).</p><?php require 'includes/appointment_form.php'; ?></section>
<?php require 'includes/footer.php'; ?>
