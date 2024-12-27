<?php
require_once 'Task.php';

class Bug extends Task
{
    private string $severity;
    private string $priority;
    private $pdo;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->pdo = $pdo;
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setSeverity(string $severity): self
    {
        $this->severity = $severity;
        return $this;
    }

    public function setBugPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Retrieve bug details by task ID.
     *
     * @param int $task_id
     * @return array|false
     */
    public function getByTaskId($task_id)
    {
        $sql = "SELECT * FROM bugs WHERE task_id = :task_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Additional methods as needed
}
