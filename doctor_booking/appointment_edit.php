<?php
require 'includes/functions.php'; require_login(); require 'includes/db.php';
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if(!$id) { http_response_code(404); exit('Жазылу табылмады.'); }
$stmt=$pdo->prepare('SELECT * FROM appointments WHERE id=? AND user_id=?'); $stmt->execute([$id,$_SESSION['user_id']]); $existing=$stmt->fetch();
if(!$existing) { http_response_code(404); exit('Жазылу табылмады.'); }
$a=['doctor_id'=>(int)$existing['doctor_id'],'date'=>$existing['appointment_date'],'time'=>substr($existing['appointment_time'],0,5),'reason'=>$existing['reason']];
$error=''; $doctors=$pdo->query('SELECT id,name,specialty FROM doctors ORDER BY name')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf(); $a=appointment_input(); $error=appointment_error($pdo,$a);
 if(!$error) {
  try {
   $stmt=$pdo->prepare('UPDATE appointments SET doctor_id=?,appointment_date=?,appointment_time=?,reason=? WHERE id=? AND user_id=?');
   $stmt->execute([$a['doctor_id'],$a['date'],$a['time'],$a['reason'],$id,$_SESSION['user_id']]); redirect('appointments.php?ok=1');
  } catch(PDOException $e) { if($e->getCode()==='23000') $error='Бұл дәрігер осы уақытта бос емес.'; else throw $e; }
 }
}
$page_title='Жазылуды өңдеу'; $button_text='Өзгерістерді сақтау'; require 'includes/header.php'; ?>
<section class="form-panel wide"><h1>Жазылуды өңдеу</h1><?php require 'includes/appointment_form.php'; ?></section>
<?php require 'includes/footer.php'; ?>
