<?php
require_once __DIR__ . '/testframework.php';
require_once __DIR__ . '/../config.php';;
require_once __DIR__ . '/../modules/database.php';
require_once __DIR__ . '/../modules/page.php';

$testFramework = new TestFramework();

// Тест 1: Проверка подключения к базе данных
function testDbConnection() {
    global $config;
    try {
        $db = new Database($config["db"]["path"]);
        return assertExpression(true, "Database connection successful", "Database connection failed");
    } catch (Exception $e) {
        return assertExpression(false, "Database connection successful", "Database connection failed: " . $e->getMessage());
    }
}

// Тест 2: Проверка метода Count
function testDbCount() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $count = $db->Count("page");
    return assertExpression($count >= 3, "Count method returned $count", "Count method failed");
}

// Тест 3: Проверка метода Create
function testDbCreate() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = [
        'title' => 'Test Page',
        'content' => 'Test Content'
    ];
    $id = $db->Create("page", $data);
    $record = $db->Read("page", $id);
    return assertExpression(
        $record['title'] === 'Test Page' && $record['content'] === 'Test Content',
        "Create method successful",
        "Create method failed"
    );
}

// Тест 4: Проверка метода Read
function testDbRead() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $record = $db->Read("page", 1);
    return assertExpression(
        $record['title'] === 'Page 1' && $record['content'] === 'Content 1',
        "Read method successful",
        "Read method failed"
    );
}

// Тест 5: Проверка метода Update
function testDbUpdate() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = [
        'title' => 'Updated Page',
        'content' => 'Updated Content'
    ];
    $db->Update("page", 1, $data);
    $record = $db->Read("page", 1);
    return assertExpression(
        $record['title'] === 'Updated Page' && $record['content'] === 'Updated Content',
        "Update method successful",
        "Update method failed"
    );
}

// Тест 6: Проверка метода Delete
function testDbDelete() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = [
        'title' => 'Page to Delete',
        'content' => 'Content to Delete'
    ];
    $id = $db->Create("page", $data);
    $db->Delete("page", $id);
    $record = $db->Read("page", $id);
    return assertExpression(
        $record === false,
        "Delete method successful",
        "Delete method failed"
    );
}

// Тест 7: Проверка метода Execute
function testDbExecute() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $sql = "CREATE TABLE test_table (id INTEGER PRIMARY KEY)";
    $result = $db->Execute($sql);
    return assertExpression(
        $result === true,
        "Execute method successful",
        "Execute method failed"
    );
}

// Тест 8: Проверка метода Fetch
function testDbFetch() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $sql = "SELECT * FROM page WHERE id = 1";
    $result = $db->Fetch($sql);
    return assertExpression(
        !empty($result) && $result[0]['title'] === 'Page 1',
        "Fetch method successful",
        "Fetch method failed"
    );
}

// Тест 9: Проверка рендеринга страницы
function testPageRender() {
    $template = __DIR__ . '/../site/templates/index.tpl';
    $page = new Page($template);
    $data = [
        'title' => 'Test Title',
        'content' => 'Test Content'
    ];
    $output = $page->Render($data);
    return assertExpression(
        strpos($output, 'Test Title') !== false && strpos($output, 'Test Content') !== false,
        "Page render successful",
        "Page render failed"
    );
}

// Добавление тестов в тестовый фреймворк
$testFramework->add('Database connection', 'testDbConnection');
$testFramework->add('Table count', 'testDbCount');
$testFramework->add('Data create', 'testDbCreate');
$testFramework->add('Data read', 'testDbRead');
$testFramework->add('Data update', 'testDbUpdate');
$testFramework->add('Data delete', 'testDbDelete');
$testFramework->add('Execute SQL', 'testDbExecute');
$testFramework->add('Fetch SQL', 'testDbFetch');
$testFramework->add('Page render', 'testPageRender');

// Запуск тестов
$testFramework->run();
echo $testFramework->getResult();
?>