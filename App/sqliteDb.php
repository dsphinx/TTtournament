<?php
declare(strict_types=1);

class SQLiteDB
{
    private SQLite3 $db;
    private string $filename;
    private bool $debug;

    public function __construct(string $filename, bool $debug = false)
    {
        $this->filename = $filename;
        $this->debug = $debug;
        if (isset($_SESSION['PATHS']['DB']) && file_exists($_SESSION['PATHS']['DB'])) {
            $this->filename = $_SESSION['PATHS']['DB'];
        }
        try {
            $this->db = new SQLite3($this->filename,SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
        } catch (\Exception $e) {
            throw new \RuntimeException("Unable to open database: {$this->filename}. Error: " . $e->getMessage());
        }

        $this->debugLog("Database connection opened: {$this->filename}");
    }

    public function close(): void
    {
        $this->debugLog("Closing database.");
        $this->db->close();
    }

    public function getLastInsertedId(): int
    {
        return $this->db->lastInsertRowID();
    }

    public function exec(string $sql): bool
    {
        $this->debugLog("Exec: $sql");
        return $this->db->exec($sql);
    }

    public function query(string $sql): SQLite3Result|false
    {
        $this->debugLog("Query: $sql");
        return $this->db->query($sql);
    }

    public function querySingle(string $sql): mixed
    {
        $this->debugLog("Query single value: $sql");
        return $this->db->querySingle($sql, false);
    }

    public function fetchAssoc(string $sql): array
    {
        $this->debugLog("Fetch row: $sql");
        $result = $this->db->querySingle($sql, true);
        return $result !== false ? $result : [];
    }

    public function fetchAll(string $sql): array
    {
        $this->debugLog("Fetch all: $sql");
        $results = [];
        $query = $this->query($sql);

        if ($query !== false) {
            while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
                $results[] = $row;
            }
        }

        return $results;
    }

    public function prepare(string $sql): SQLite3Stmt
    {
        $this->debugLog("Prepare: $sql");
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new \RuntimeException("Failed to prepare statement: " . $this->db->lastErrorMsg());
        }

        return $stmt;
    }

    public function getErrorMessage(): string
    {
        return $this->db->lastErrorMsg();
    }

    public function getErrorCode(): int
    {
        return $this->db->lastErrorCode();
    }

    private function debugLog(string $message): void
    {
        if ($this->debug) {
            error_log("[SQLiteDB] " . $message);
        }
    }


    /*
 * get_row
 * Return a single row from a query, formatted as array or object
 *
 * Args:
 * $query - (string) - The SQLite query (MUST be properly sanitized beforehand)
 * $return_array - (bool) - Return associative array instead of object
 *
 * @return array|object|false - The row as an array or object, or false if no rows were found
 */
    public function get_row(string $query, bool $return_array = false)
    {
        // Perform the query
        $result = $this->query($query);
        if ($result === false) {
            return false;
        }

        // Get the first row
        $row = $result->fetchArray(SQLITE3_ASSOC); // Πάρε την πρώτη γραμμή

        return $row !== false ? ($return_array ? $row : (object)$row) : false;
    }

    /*
     * get_rows
     * Return multiple rows from a query as an array of objects
     *
     * Args:
     * $query - (string) - The SQLite query (MUST be properly sanitized beforehand)
     * $return_array - (bool) - Each row is an array instead of an object
     *
     * @return array|false - An array of rows (as objects or arrays), or false if no rows were found
     */
    public function get_rows(string $query = '', bool $return_array = false)
    {
        // Perform the query
        $result = $this->query($query);
        if ($result === false) {
            return false;
        }

        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $return_array ? $row : (object)$row;
        }

        return $rows;
    }

    /*
     * Get Result Handle
     *
     * When arbitrarily called, returns the reference pointer
     * to where a SQLite result variable will be stored.
     *
     * Args:
     * $handle - (string) - Unique string ID of the handle to retrieve
     *
     * Returns: PDOStatement|false - PDOStatement Object or false if handle does not exist
     */
    private function get_handle(string $handle = '')
    {
        // No handle specified, use the main hardcoded block
        if (!$handle) {
            return $this->default_result;
        }

        // Return the specific handle or false if not found
        return $this->results[$handle] ?? false;
    }

    /*
     * Set Result Handle
     *
     * Stores a query result that can be retrieved later
     * for simultaneous query handling.
     * If no handle is supplied, the default store is used.
     *
     * Args:
     * $result - (PDOStatement|null) - Query result object (or null if no result)
     * $handle - (string) - Unique ID to refer to this result object
     *
     * Returns: bool - Whether the handle was successfully set
     */
    private function set_handle(?PDOStatement $result = null, string $handle = ''): bool
    {
        // If no result is provided, return false
        if (!$result) {
            return false;
        }

        // Store the result either in the default or with the provided handle
        if (!$handle) {
            $this->default_result = $result;
        } else {
            $this->results[$handle] = $result;
        }

        return true;
    }

    public function insertRow(string $table, array $data): int
    {
        if (empty($table) || empty($data)) {
            throw new \InvalidArgumentException("Table name and data array cannot be empty.");
        }

        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value, is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT);
        }

        if (!$stmt->execute()) {
            throw new \RuntimeException("Failed to execute insert: " . $this->getErrorMessage());
        }

        return $this->getLastInsertedId();
    }

    public function deleteRow(string $table, string $column, mixed $value): bool
    {
        if (empty($table) || empty($column)) {
            throw new \InvalidArgumentException("Table name and column cannot be empty.");
        }

        $sql = sprintf("DELETE FROM %s WHERE %s = :value", $table, $column);
        $stmt = $this->prepare($sql);

        $type = is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT;
        $stmt->bindValue(':value', $value, $type);

        if (!$stmt->execute()) {
            throw new \RuntimeException("Failed to execute delete: " . $this->getErrorMessage());
        }

        return true;
    }


    public function updateRow(string $table, array $data, string $whereColumn, mixed $whereValue): bool
    {
        if (empty($table) || empty($data) || empty($whereColumn)) {
            throw new \InvalidArgumentException("Table, data, and whereColumn cannot be empty.");
        }

        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key = :$key";
        }

        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s = :whereValue",
            $table,
            implode(', ', $setClauses),
            $whereColumn
        );

        $stmt = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value, is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT);
        }

        $stmt->bindValue(':whereValue', $whereValue, is_int($whereValue) ? SQLITE3_INTEGER : SQLITE3_TEXT);

        if (!$stmt->execute()) {
            throw new \RuntimeException("Failed to execute update: " . $this->getErrorMessage());
        }

        return true;
    }


}
