<?php
declare(strict_types=1);

$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'laboratorio';
$user = getenv('DB_USER') ?: 'appuser';
$password = getenv('DB_PASSWORD') ?: 'apppassword';

$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
$pdo = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$message = '';

function redirect_home(): void
{
    header('Location: /');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '' || $email === '') {
        $message = 'Nombre y correo son obligatorios.';
    } elseif (($_POST['form_action'] ?? '') === 'create') {
        $stmt = $pdo->prepare('INSERT INTO contacts (name, email, phone) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $phone]);
        redirect_home();
    } elseif (($_POST['form_action'] ?? '') === 'update' && $id > 0) {
        $stmt = $pdo->prepare('UPDATE contacts SET name = ?, email = ?, phone = ? WHERE id = ?');
        $stmt->execute([$name, $email, $phone, $id]);
        redirect_home();
    }
}

if ($action === 'delete' && $id > 0) {
    $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
    $stmt->execute([$id]);
    redirect_home();
}

$editing = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ?');
    $stmt->execute([$id]);
    $editing = $stmt->fetch() ?: null;
}

$contacts = $pdo->query('SELECT * FROM contacts ORDER BY id DESC')->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laboratorio Docker CRUD</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f8;
            color: #1d2733;
        }
        body {
            margin: 0;
        }
        header {
            background: #14532d;
            color: white;
            padding: 24px;
        }
        main {
            max-width: 980px;
            margin: 0 auto;
            padding: 24px;
        }
        nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 16px;
        }
        nav a, button, .button {
            background: #0f766e;
            color: white;
            border: 0;
            border-radius: 6px;
            padding: 10px 14px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }
        .button.secondary {
            background: #475569;
        }
        .button.danger {
            background: #b91c1c;
        }
        section {
            background: white;
            border: 1px solid #d7dee8;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        form {
            display: grid;
            gap: 12px;
        }
        label {
            display: grid;
            gap: 6px;
            font-weight: 700;
        }
        input {
            border: 1px solid #b9c2cf;
            border-radius: 6px;
            padding: 10px;
            font: inherit;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border-bottom: 1px solid #e2e8f0;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f8fafc;
        }
        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .alert {
            border-left: 4px solid #b91c1c;
            background: #fee2e2;
            padding: 10px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Laboratorio Docker CRUD</h1>
        <p>Aplicacion web dinamica con PHP, MariaDB, Docker Compose, volumen persistente y red virtual.</p>
        <nav>
            <a href="/?action=create">Crear registro</a>
            <a href="/">Leer registros</a>
            <a href="#tabla">Modificar registro</a>
            <a href="#tabla">Borrar registro</a>
        </nav>
    </header>

    <main>
        <?php if ($message !== ''): ?>
            <div class="alert"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($action === 'create' || $editing): ?>
            <section>
                <h2><?= $editing ? 'Modificar registro' : 'Crear registro' ?></h2>
                <form method="post">
                    <input type="hidden" name="form_action" value="<?= $editing ? 'update' : 'create' ?>">
                    <label>
                        Nombre
                        <input name="name" required value="<?= htmlspecialchars($editing['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </label>
                    <label>
                        Correo
                        <input name="email" type="email" required value="<?= htmlspecialchars($editing['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </label>
                    <label>
                        Telefono
                        <input name="phone" value="<?= htmlspecialchars($editing['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </label>
                    <div class="actions">
                        <button type="submit"><?= $editing ? 'Actualizar' : 'Guardar' ?></button>
                        <a class="button secondary" href="/">Cancelar</a>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <section id="tabla">
            <h2>Leer registros</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Telefono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                        <tr>
                            <td><?= (int) $contact['id'] ?></td>
                            <td><?= htmlspecialchars($contact['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($contact['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="actions">
                                <a class="button" href="/?action=edit&id=<?= (int) $contact['id'] ?>">Editar</a>
                                <a class="button danger" href="/?action=delete&id=<?= (int) $contact['id'] ?>" onclick="return confirm('Desea borrar este registro?')">Borrar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$contacts): ?>
                        <tr>
                            <td colspan="5">No hay registros cargados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
