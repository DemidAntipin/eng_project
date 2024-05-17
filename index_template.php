<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
.site-header, .site-footer {
    background: #333;
    color: white;
}

.content {
    padding: 20px 0;
}

.container {
    width: 80%;
    margin: auto;
    padding-bottom: 10px;
}
.tests-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.tests-table th, .tests-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.tests-table th {
    background-color: #f2f2f2;
}

.site-header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 80%;
    margin: auto;
}

.create-test, .admin-login, .admin-logout {
    margin-top: 10px;
}

.btn {
    padding: 10px 15px;
    color: white;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.create-test-btn {
    background-color: #007bff;
}

.create-test-btn:hover {
    background-color: #0056b3;
}

.admin-auth-btn, .admin-logout-btn {
    background-color: #f44336;
}

.admin-auth-btn:hover, .admin-logout-btn:hover {
    background-color: #d32f2f;
}

.actions .btn {
    padding: 10px 10px; /* Устанавливаем одинаковые отступы для всех кнопок */
    margin-right: 5px;
    font-size: 10pt; /* Устанавливаем одинаковый размер шрифта */
    text-decoration: none;
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s; /* Добавляем плавное изменение цвета */
}

.start-btn {
    background-color: #4CAF50;
}

.start-btn:hover {
    background-color: #388E3C; /* Темно-зеленый цвет при наведении */
}

.delete-btn {
    background-color: #8B0000;
}

.delete-btn:hover {
    background-color: #a52a2a; /* Темно-красный цвет при наведении */
}
.delete-btn svg {
    transform: scale(2);
}
@media only screen and (max-width: 600px) {
            .container {
                width: 100%;
            }

            .tests-table {
                font-size: 10px;
            }

            .actions .btn {
                padding: 8px 8px;
                font-size: 10pt;
            }
	    .admin-login {
	        width: 150px;
		justify-content: center;
	    }
	    .admin-login input{
	        width: 150px;
		margin: 2px 0px;
	    }
	    .admin-auth-btn {
	    	margin-top: 6px;
		margin-left: 30px;
	    }
        }

    </style>
</head>
<body>
   <header class="site-header">
    <div class="container">
            <div class="create-test">
                <form action="/" method="post">
                    <button type="submit" name="create" class="btn create-test-btn">Create test</button>
                </form>
            </div>
        <?php if (!$isAdmin): ?>
            <div class="admin-login">
                <form action="/" method="post">
                    <input type="text" style="font-size: 14pt;" name="adm_login" placeholder="Admin_login">
                    <input type="password" style="font-size: 14pt;" name="adm_password" placeholder="Admin_password">
                    <button type="submit" name="admin_auth" class="btn admin-auth-btn">Authorize</button>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-logout">
                <form method="POST" action="/">Welcome, Administator
                    <button type="submit" name="logout" class="btn admin-logout-btn">Logout</button>	
                </form>
            </div>
        <?php endif; ?>
    </div>
</header>
   <main class="content">
        <div class="container">
            <table class="tests-table">
    <thead>
        <tr>
            <th>Test Title</th>
            <th>Test Description</th>
            <th>Created_at</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tests as $row): ?>
            <?php if (!isset($row['deleted'])): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= htmlspecialchars($row['created']) ?></td>
                    <td class="actions">
                        <form action="/" method="post">
                            <input type="hidden" name="filename" value="<?= urlencode($row['title']) ?>">
                            <button type="submit" name="start_test" class="btn start-btn">Start test</button>
                            <?php if ($isAdmin): ?>
                                <button type="submit" name="delete_test" class="btn delete-btn" id="delete_test_<?= $row['title'] ?>" value="<?= $row['title'] ?>">
                                    <label for="delete_test_<?= $row['title'] ?>"><?= $del_icon ?></label>
                                </button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>
        </div>
    </main>
</body>
</html>
