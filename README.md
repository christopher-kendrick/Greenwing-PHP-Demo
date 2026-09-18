# Greenwing PHP Demo

Demo XML parsing, JSON coding/encoding, and mySQL.

# PHP XML, JSON & MySQL Integration

A PHP-based integration API demonstrating XML parsing, JSON encoding/decoding, and MySQL database operations using PDO.

This project demonstrates backend integration techniques commonly used in enterprise applications, REST APIs, and data exchange systems.

## Features

- XML parsing using PHP SimpleXML
- JSON encoding and decoding
- MySQL database integration using PDO
- Prepared statements for secure database queries
- REST-style API endpoints
- XML-to-JSON data transformation
- JSON-to-XML conversion
- XML data import into MySQL
- Input validation and error handling
- Environment-based database configuration

## Technologies

- PHP 8+
- MySQL 5.7+ / 8+
- PDO (PHP Data Objects)
- SimpleXML
- JSON
- cURL
- REST API concepts

## Project Structure

```text
php-integration/
│
├── database.sql       # Database and table creation
├── config.php         # MySQL database connection
├── index.php          # API endpoints and integration logic
└── README.md          # Project documentation
```

## Requirements

Before running the application, install:

1. PHP 8.0 or later
2. MySQL Server
3. PHP PDO MySQL extension
4. PHP SimpleXML extension
5. cURL (for API testing)

### Verify PHP Installation

```bash
php -v
```

### Verify Required PHP Extensions

```bash
php -m | grep -E "PDO|pdo_mysql|SimpleXML"
```

## Database Setup

### 1. Create the Database

Run the SQL script:

```bash
mysql -u root -p < database.sql
```

Or open the MySQL console:

```bash
mysql -u root -p
```

Then execute:

```sql
SOURCE database.sql;
```

### 2. Database Schema

The application uses a `users` table:

| Column | Type | Description |
|---|---|---|
| id | INT | Auto-increment primary key |
| name | VARCHAR(255) | User's full name |
| email | VARCHAR(255) | Unique email address |
| created_at | TIMESTAMP | Record creation time |

## Configuration

The database connection is configured in `config.php`.

The application supports environment variables:

| Variable | Description | Default |
|---|---|---|
| DB_HOST | MySQL hostname | localhost |
| DB_NAME | Database name | integration_demo |
| DB_USER | Database username | root |
| DB_PASSWORD | Database password | Empty |

### Example Environment Configuration

Linux/macOS:

```bash
export DB_HOST=localhost
export DB_NAME=integration_demo
export DB_USER=root
export DB_PASSWORD='your_password'
```

Windows PowerShell:

```powershell
$env:DB_HOST="localhost"
$env:DB_NAME="integration_demo"
$env:DB_USER="root"
$env:DB_PASSWORD="your_password"
```

**Security:** Do not commit database passwords or other secrets to version control.

## Running the Application

Start the PHP development server from the project directory:

```bash
php -S localhost:8000
```

The API will be available at:

```text
http://localhost:8000
```

## API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| POST | `/xml-to-json` | Parse XML and return JSON |
| POST | `/json-demo` | Demonstrate JSON encoding/decoding |
| POST | `/users` | Create a user in MySQL |
| GET | `/users` | Retrieve users from MySQL |
| POST | `/import-user-xml` | Import XML user data into MySQL |
| POST | `/json-to-xml` | Convert JSON to XML |

## API Testing

### 1. XML to JSON

Parse an XML document and return a JSON response.

```bash
curl -X POST http://localhost:8000/xml-to-json \
  -H "Content-Type: application/xml" \
  -d '<user><name>Chris Kendrick</name><email>chris@example.com</email></user>'
```

Example response:

```json
{
  "success": true,
  "data": {
    "name": "Chris Kendrick",
    "email": "chris@example.com"
  }
}
```

### 2. JSON Encoding and Decoding

Demonstrate JSON serialization and deserialization.

```bash
curl -X POST http://localhost:8000/json-demo
```

The endpoint demonstrates:

- `json_encode()` - Convert PHP data into JSON
- `json_decode()` - Convert JSON into PHP data

### 3. Create User

Insert a user into MySQL using a prepared statement.

```bash
curl -X POST http://localhost:8000/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Chris Kendrick",
    "email": "chris@example.com"
  }'
```

Example response:

```json
{
  "success": true,
  "userId": 1,
  "name": "Chris Kendrick",
  "email": "chris@example.com"
}
```

### 4. Retrieve Users

Retrieve all users from the MySQL database.

```bash
curl http://localhost:8000/users
```

Example response:

```json
{
  "success": true,
  "users": [
    {
      "id": 1,
      "name": "Chris Kendrick",
      "email": "chris@example.com",
      "created_at": "2026-09-17 12:00:00"
    }
  ]
}
```

### 5. Import XML User into MySQL

Parse XML, transform the data, and insert it into MySQL.

```bash
curl -X POST http://localhost:8000/import-user-xml \
  -H "Content-Type: application/xml" \
  -d '<user><name>John Doe</name><email>john@example.com</email></user>'
```

Example response:

```json
{
  "success": true,
  "message": "XML user imported into MySQL",
  "userId": 2,
  "user": {
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

### 6. JSON to XML

Convert JSON input into an XML document.

```bash
curl -X POST http://localhost:8000/json-to-xml \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Chris Kendrick",
    "email": "chris@example.com"
  }'
```

Example response:

```xml
<?xml version="1.0"?>
<user>
  <name>Chris Kendrick</name>
  <email>chris@example.com</email>
</user>
```

## Integration Workflow

### XML to MySQL

```text
XML Request
    |
    v
XML Parsing (SimpleXML)
    |
    v
PHP Array
    |
    v
JSON Encoding / Decoding
    |
    v
Input Validation
    |
    v
PDO Prepared Statement
    |
    v
MySQL Database
    |
    v
JSON Response
```

## Security Considerations

- Use PDO prepared statements to reduce SQL injection risk.
- Validate and sanitize incoming user input.
- Use environment variables for database credentials.
- Avoid exposing database error details in production.
- Configure appropriate database user permissions.
- Validate XML structure before processing.
- Use HTTPS when transmitting sensitive information.
- Configure production error logging without displaying internal errors.

## Error Handling

The API includes error handling for:

- Invalid XML documents
- Invalid JSON input
- Missing required fields
- Invalid email addresses
- Database connection failures
- Database query failures
- Unknown API routes

Errors are logged on the server where appropriate, while clients receive structured responses.

## Enterprise Integration Use Cases

This project provides a foundation for integrating:

- ERP systems
- CRM platforms
- Legacy XML-based services
- REST APIs
- E-commerce applications
- Data import/export workflows
- Internal business applications
- External partner integrations

The same integration patterns can be extended to support SOAP services, scheduled data synchronization, and enterprise application workflows.

## Future Improvements

- Add authentication and authorization
- Implement request logging
- Add unit and integration tests
- Add database migrations
- Introduce service and repository layers
- Add pagination for user records
- Implement XML schema validation
- Add Docker support
- Add CI/CD pipeline integration
- Implement API documentation using OpenAPI

## Author

**Chris Kendrick**

Senior Full Stack Software Engineer

Experienced in PHP, MySQL, JavaScript, REST APIs, and enterprise application development.

## License

This project is intended for educational, demonstration, and technical assessment purposes.

