
<?php

require_once 'config.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

/**
 * Helper: Send JSON response
 */
function sendJson($data, int $status = 200): void
{
    http_response_code($status);

    echo json_encode(
        $data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

    exit;
}

/**
 * Helper: Read JSON request body
 */
function getJsonInput(): array
{
    $body = file_get_contents('php://input');

    $data = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJson([
            'error' => 'Invalid JSON',
            'details' => json_last_error_msg()
        ], 400);
    }

    return $data;
}

/**
 * 1. XML Parsing
 *
 * XML string -> PHP array -> JSON response
 */
function parseXml(string $xml): array
{
    libxml_use_internal_errors(true);

    $xmlObject = simplexml_load_string($xml);

    if ($xmlObject === false) {
        throw new InvalidArgumentException(
            'Invalid XML document'
        );
    }

    // Convert XML to PHP array
    $array = json_decode(
        json_encode($xmlObject),
        true
    );

    return $array ?: [];
}

/**
 * 2. JSON Encoding / Decoding
 */
function jsonDemo(): void
{
    $user = [
        'name' => 'Chris Kendrick',
        'email' => 'chris@example.com',
        'skills' => [
            'PHP',
            'MySQL',
            'JavaScript'
        ]
    ];

    // JSON encoding
    $jsonString = json_encode(
        $user,
        JSON_PRETTY_PRINT
    );

    // JSON decoding
    $decodedObject = json_decode(
        $jsonString,
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    sendJson([
        'encoded' => $jsonString,
        'decoded' => $decodedObject
    ]);
}

/**
 * 3. Insert User into MySQL
 *
 * Uses PDO prepared statements.
 */
function createUser(PDO $pdo): void
{
    $data = getJsonInput();

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');

    if ($name === '' || $email === '') {
        sendJson([
            'error' => 'Name and email are required'
        ], 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJson([
            'error' => 'Invalid email address'
        ], 400);
    }

    $sql = "
        INSERT INTO users (name, email)
        VALUES (:name, :email)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':name' => $name,
        ':email' => $email
    ]);

    sendJson([
        'success' => true,
        'userId' => (int) $pdo->lastInsertId(),
        'name' => $name,
        'email' => $email
    ], 201);
}

/**
 * 4. Retrieve Users from MySQL
 */
function getUsers(PDO $pdo): void
{
    $stmt = $pdo->query("
        SELECT id, name, email, created_at
        FROM users
        ORDER BY id DESC
    ");

    $users = $stmt->fetchAll();

    sendJson([
        'success' => true,
        'users' => $users
    ]);
}

/**
 * 5. XML -> JSON -> MySQL
 *
 * Parse XML, encode/decode JSON,
 * and insert into MySQL.
 */
function importXmlUser(PDO $pdo): void
{
    $xml = file_get_contents('php://input');

    if (!$xml) {
        sendJson([
            'error' => 'XML body is required'
        ], 400);
    }

    try {
        // Parse XML
        $parsedData = parseXml($xml);

        $name = trim((string) ($parsedData['name'] ?? ''));
        $email = trim((string) ($parsedData['email'] ?? ''));

        if ($name === '' || $email === '') {
            sendJson([
                'error' => 'Name and email are required'
            ], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendJson([
                'error' => 'Invalid email address'
            ], 400);
        }

        // JSON encoding
        $jsonString = json_encode([
            'name' => $name,
            'email' => $email
        ], JSON_THROW_ON_ERROR);

        // JSON decoding
        $decodedUser = json_decode(
            $jsonString,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        // MySQL insertion
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email)
            VALUES (:name, :email)
        ");

        $stmt->execute([
            ':name' => $decodedUser['name'],
            ':email' => $decodedUser['email']
        ]);

        sendJson([
            'success' => true,
            'message' => 'XML user imported into MySQL',
            'userId' => (int) $pdo->lastInsertId(),
            'user' => $decodedUser
        ], 201);

    } catch (Throwable $e) {
        error_log($e->getMessage());

        sendJson([
            'error' => 'XML import failed'
        ], 500);
    }
}

/**
 * 6. JSON -> XML
 */
function jsonToXml(): void
{
    $data = getJsonInput();

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');

    if ($name === '' || $email === '') {
        sendJson([
            'error' => 'Name and email are required'
        ], 400);
    }

    // Escape XML special characters
    $xml = new SimpleXMLElement('<user/>');

    $xml->addChild(
        'name',
        htmlspecialchars(
            $name,
            ENT_XML1 | ENT_COMPAT,
            'UTF-8'
        )
    );

    $xml->addChild(
        'email',
        htmlspecialchars(
            $email,
            ENT_XML1 | ENT_COMPAT,
            'UTF-8'
        )
    );

    header('Content-Type: application/xml');

    echo $xml->asXML();

    exit;
}

/**
 * API Routing
 */
try {

    if ($method === 'POST' && $path === '/json-demo') {
        jsonDemo();
    }

    if ($method === 'POST' && $path === '/users') {
        createUser($pdo);
    }

    if ($method === 'GET' && $path === '/users') {
        getUsers($pdo);
    }

    if ($method === 'POST' && $path === '/import-user-xml') {
        importXmlUser($pdo);
    }

    if ($method === 'POST' && $path === '/json-to-xml') {
        jsonToXml();
    }

    if ($method === 'POST' && $path === '/xml-to-json') {

        $xml = file_get_contents('php://input');

        $parsed = parseXml($xml);

        sendJson([
            'success' => true,
            'data' => $parsed
        ]);
    }

    sendJson([
        'error' => 'Endpoint not found'
    ], 404);

} catch (Throwable $e) {

    error_log($e->getMessage());

    sendJson([
        'error' => 'Internal server error'
    ], 500);
}
