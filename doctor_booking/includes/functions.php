<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function redirect(string $url): never { header('Location: '.$url); exit; }
function logged_in(): bool { return isset($_SESSION['user_id']); }
function require_login(): void { if (!logged_in()) redirect('login.php'); }
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf_token'] ?? ''))) {
        http_response_code(403); exit('Қауіпсіздік қатесі: CSRF токені сәйкес емес.');
    }
}
function valid_date(string $date): bool {
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    return $d !== false && $d->format('Y-m-d') === $date;
}
function valid_time(string $time): bool {
    return (bool)preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $time);
}
function appointment_input(): array {
    return [
      'doctor_id' => filter_var($_POST['doctor_id'] ?? null, FILTER_VALIDATE_INT),
      'date' => trim((string)($_POST['appointment_date'] ?? '')),
      'time' => substr(trim((string)($_POST['appointment_time'] ?? '')), 0, 5),
      'reason' => trim((string)($_POST['reason'] ?? ''))
    ];
}
function appointment_error(PDO $pdo, array $a): string {
    if (!$a['doctor_id'] || $a['doctor_id'] < 1) return 'Дәрігерді таңдаңыз.';
    $stmt=$pdo->prepare('SELECT COUNT(*) FROM doctors WHERE id = ?'); $stmt->execute([$a['doctor_id']]);
    if (!(int)$stmt->fetchColumn()) return 'Мұндай дәрігер жоқ.';
    if (!valid_date($a['date']) || $a['date'] < date('Y-m-d')) return 'Бүгінгі немесе келешек күнді таңдаңыз.';
    if (!valid_time($a['time']) || $a['time'] < '09:00' || $a['time'] > '17:00') return 'Уақыт 09:00–17:00 аралығында болуы тиіс.';
    if (mb_strlen($a['reason']) > 500) return 'Себеп 500 таңбадан аспасын.';
    return '';
}
