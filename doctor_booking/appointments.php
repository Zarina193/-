<?php
require 'includes/functions.php'; require_login(); require 'includes/db.php';
$stmt=$pdo->prepare('SELECT a.id,a.appointment_date,a.appointment_time,a.reason,a.status,d.name AS doctor,d.specialty FROM appointments a JOIN doctors d ON d.id=a.doctor_id WHERE a.user_id=? ORDER BY a.appointment_date,a.appointment_time');
$stmt->execute([$_SESSION['user_id']]); $items=$stmt->fetchAll();
$page_title='Менің жазылуларым'; require 'includes/header.php'; ?>
<div class="section-heading"><div><h1>Менің жазылуларым</h1><p>Қабылдауларды бақылау және басқару</p></div><a class="btn" href="appointment_create.php">+ Жаңа жазылу</a></div>
<?php if(isset($_GET['ok'])): ?><div class="success">Әрекет сәтті орындалды.</div><?php endif; ?>
<?php if(!$items): ?><div class="card empty"><h2>Әзірге жазылулар жоқ</h2><p>Алғашқы қабылдауыңызды жоспарлаңыз.</p></div><?php else: ?><div class="table-scroll"><table><thead><tr><th>Дәрігер</th><th>Мамандығы</th><th>Күні</th><th>Уақыты</th><th>Себебі</th><th>Әрекет</th></tr></thead><tbody><?php foreach($items as $item): ?><tr><td><?= e($item['doctor']) ?></td><td><?= e($item['specialty']) ?></td><td><?= e($item['appointment_date']) ?></td><td><?= e(substr($item['appointment_time'],0,5)) ?></td><td><?= e($item['reason'] ?: '—') ?></td><td class="actions"><a href="appointment_edit.php?id=<?= (int)$item['id'] ?>">Өңдеу</a><form method="post" action="appointment_delete.php" onsubmit="return confirm('Жазылуды жоясыз ба?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="link-danger" type="submit">Жою</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
<?php require 'includes/footer.php'; ?>
