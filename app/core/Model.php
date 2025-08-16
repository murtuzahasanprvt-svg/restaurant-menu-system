<?php
/**
 * Base Model Class
 */

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $timestamps = true;
    protected $created_at = 'created_at';
    protected $updated_at = 'updated_at';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :value";
        $this->db->query($sql);
        $this->db->bind(':value', $value);
        return $this->db->resultSet();
    }

    public function whereFirst($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :value LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':value', $value);
        return $this->db->single();
    }

    public function create($data) {
        // Filter only fillable fields
        $data = $this->filterFillable($data);
        
        // Add timestamps if enabled
        if ($this->timestamps) {
            $data[$this->created_at] = date('Y-m-d H:i:s');
            $data[$this->updated_at] = date('Y-m-d H:i:s');
        }

        $columns = array_keys($data);
        $values = array_values($data);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (:" . implode(', :', $columns) . ")";
        
        $this->db->query($sql);
        
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    public function update($id, $data) {
        // Filter only fillable fields
        $data = $this->filterFillable($data);
        
        // Add updated timestamp if enabled
        if ($this->timestamps) {
            $data[$this->updated_at] = date('Y-m-d H:i:s');
        }

        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " 
                WHERE {$this->primaryKey} = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        return $this->db->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function paginate($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} LIMIT :offset, :limit";
        $this->db->query($sql);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        $this->db->bind(':limit', $perPage, PDO::PARAM_INT);
        
        $results = $this->db->resultSet();
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $this->db->query($countSql);
        $total = $this->db->single()['total'];
        
        return [
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }

    public function orderBy($column, $direction = 'ASC') {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $sql = "SELECT * FROM {$this->table} ORDER BY {$column} {$direction}";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function count() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result['total'];
    }

    public function exists($id) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result['count'] > 0;
    }

    public function raw($sql, $params = []) {
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->resultSet();
    }

    private function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    public function hideHidden($data) {
        if (empty($this->hidden)) {
            return $data;
        }
        
        if (isset($data[$this->primaryKey])) {
            // Single record
            foreach ($this->hidden as $field) {
                unset($data[$field]);
            }
        } else {
            // Multiple records
            foreach ($data as &$record) {
                foreach ($this->hidden as $field) {
                    unset($record[$field]);
                }
            }
        }
        
        return $data;
    }

    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    public function commit() {
        return $this->db->commit();
    }

    public function rollBack() {
        return $this->db->rollBack();
    }
}
?>