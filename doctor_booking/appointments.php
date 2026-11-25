<?php
require 'includes/functions.php'; require_login(); require 'includes/db.php';
function query_value(string $key): string {
    return is_string($_GET[$key] ?? null) ? trim($_GET[$key]) : '';
}
$q = query_value('q');
$specialty = query_value('specialty');
$date = query_value('date');
$sort = query_value('sort');
$orders = ['date_asc'=>'a.appointment_date ASC, a.appointment_time ASC', 'date_desc'=>'a.appointment_date DESC, a.appointment_time DESC', 'doctor_asc'=>'d.name ASC', 'doctor_desc'=>'d.name DESC'];
if (!isset($orders[$sort])) $sort = 'date_asc';
$specialties = $pdo->query('SELECT DISTINCT specialty FROM doctors ORDER BY specialty')->fetchAll(PDO::FETCH_COLUMN);
if (!in_array($specialty, $specialties, true)) $specialty = '';
if (!valid_date($date)) $date = '';
$where = ['a.user_id = ?']; $params = [$_SESSION['user_id']];
if ($q !== '') { $where[] = '(d.name LIKE ? OR a.reason LIKE ?)'; $params[] = '%'.$q.'%'; $params[] = '%'.$q.'%'; }
if ($specialty !== '') { $where[] = 'd.specialty = ?'; $params[] = $specialty; }
if ($date !== '') { $where[] = 'a.appointment_date = ?'; $params[] = $date; }
$from = ' FROM appointments a JOIN doctors d ON d.id=a.doctor_id WHERE '.implode(' AND ', $where);
$count = $pdo->prepare('SELECT COUNT(*)'.$from); $count->execute($params);
$total = (int)$count->fetchColumn(); $perPage = 5;
$pages = max(1, (int)ceil($total/$perPage));
$page = min($pages, max(1, (int)query_value('page')));
$stmt = $pdo->prepare('SELECT a.id,a.appointment_date,a.appointment_time,a.reason,a.status,d.name AS doctor,d.specialty'.$from.' ORDER BY '.$orders[$sort].', a.id ASC LIMIT ? OFFSET ?');
foreach ($params as $i=>$value) $stmt->bindValue($i+1, $value);
$stmt->bindValue(count($params)+1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(count($params)+2, ($page-1)*$perPage, PDO::PARAM_INT);
$stmt->execute(); $items = $stmt->fetchAll();
function page_url(int $page): string {
    global $q,$specialty,$date,$sort;
    return 'appointments.php?'.http_build_query(compact('q','specialty','date','sort','page'));
}
$page_title='Менің жазылуларым'; require 'includes/header.php'; ?>
<div class="section-heading"><div><h1>Менің жазылуларым</h1><p>Қабылдауларды бақылау және басқару</p></div><a class="btn" href="appointment_create.php">+ Жаңа жазылу</a></div>
<?php if(isset($_GET['ok'])): ?><div class="success">Әрекет сәтті орындалды.</div><?php endif; ?>
<form class="card filters" method="get" action="appointments.php">
<label>Іздеу<input name="q" value="<?= e($q) ?>" placeholder="Дәрігер немесе қабылдау себебі"></label>
<label>Мамандығы<select name="specialty"><option value="">Барлығы</option><?php foreach($specialties as $sp): ?><option value="<?= e($sp) ?>" <?= $sp===$specialty?'selected':'' ?>><?= e($sp) ?></option><?php endforeach; ?></select></label>
<label>Қабылдау күні<input type="date" name="date" value="<?= e($date) ?>"></label>
<label>Сұрыптау<select name="sort"><?php foreach(['date_asc'=>'Күні: ертеден кешке','date_desc'=>'Күні: кештен ертеге','doctor_asc'=>'Дәрігер: А–Я','doctor_desc'=>'Дәрігер: Я–А'] as $key=>$label): ?><option value="<?= e($key) ?>" <?= $sort===$key?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
<div class="filter-actions"><button class="btn" type="submit">Іздеу / Сүзу</button><a href="appointments.php">Тазарту</a></div>
</form>
<p>Табылған жазылулар: <?= $total ?></p>
<?php if(!$items): ?><div class="card empty"><h2>Жазылулар табылмады</h2><p>Сүзгілерді өзгертіңіз немесе жаңа қабылдау қосыңыз.</p></div><?php else: ?><div class="table-scroll"><table><thead><tr><th>Дәрігер</th><th>Мамандығы</th><th>Күні</th><th>Уақыты</th><th>Себебі</th><th>Әрекет</th></tr></thead><tbody><?php foreach($items as $item): ?><tr><td><?= e($item['doctor']) ?></td><td><?= e($item['specialty']) ?></td><td><?= e($item['appointment_date']) ?></td><td><?= e(substr($item['appointment_time'],0,5)) ?></td><td><?= e($item['reason'] ?: '—') ?></td><td class="actions"><a href="appointment_edit.php?id=<?= (int)$item['id'] ?>">Өңдеу</a><form method="post" action="appointment_delete.php" onsubmit="return confirm('Жазылуды жоясыз ба?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="link-danger" type="submit">Жою</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
<?php if($pages>1): ?><nav class="pagination" aria-label="Беттер">
<?php if($page>1): ?><a href="<?= e(page_url($page-1)) ?>">← Алдыңғы</a><?php endif; ?>
<?php for($i=max(1,$page-2);$i<=min($pages,$page+2);$i++): ?>
<?php if($i===$page): ?><span aria-current="page"><?= $i ?></span><?php else: ?><a href="<?= e(page_url($i)) ?>"><?= $i ?></a><?php endif; ?>
<?php endfor; ?>
<?php if($page<$pages): ?><a href="<?= e(page_url($page+1)) ?>">Келесі →</a><?php endif; ?>
<small><?= $page ?> / <?= $pages ?> бет</small></nav><?php endif; ?>
<?php require 'includes/footer.php'; ?>
