<?php
require 'includes/functions.php'; require_login();
if($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); exit('Әдіс рұқсат етілмеген.'); }
verify_csrf(); $id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if(!$id) { http_response_code(400); exit('ID дұрыс емес.'); }
require 'includes/db.php'; $stmt=$pdo->prepare('DELETE FROM appointments WHERE id=? AND user_id=?'); $stmt->execute([$id,$_SESSION['user_id']]);
redirect('appointments.php?ok=1');
