<?php
require_once 'Task.php';

class Feature extends Task
{
    private string $featureType;
    private DateTime $targetDate;
    private $pdo;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->pdo = $pdo;
    }

    public function getFeatureType(): string
    {
        return $this->featureType;
    }

    public function getTargetDate(): DateTime
    {
        return $this->targetDate;
    }

    public function setFeatureType(string $featureType): self
    {
        $this->featureType = $featureType;
        return $this;
    }

    public function setTargetDate(DateTime $targetDate): self
    {
        $this->targetDate = $targetDate;
        return $this;
    }

    /**
     * Retrieve feature details by task ID.
     *
     * @param int $task_id
     * @return array|false
     */
    public function getByTaskId($task_id)
    {
        $sql = "SELECT * FROM features WHERE task_id = :task_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Additional methods as needed
}
