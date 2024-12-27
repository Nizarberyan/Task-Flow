<?php
require_once __DIR__ . '../../../config/database.php';
require_once __DIR__ . '/User.php';
abstract class Task
{
    private ?int $id = null;
    private string $title;
    private string $description;
    private string $status;
    private string $type;
    private string $priority;
    private DateTime $createdAt;
    private ?User $assignedTo = null;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): self
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }

    public function validate()
    {
        $validStatus = ['pending', 'in_progress', 'completed'];
        if (!in_array($this->status, $validStatus)) {
            throw new Exception("Invalid status");
        }
        return true;
    }

    public static function fromArray(array $taskData, PDO $pdo): Task
    {
        $taskType = $taskData['type'];
        switch ($taskType) {
            case 'feature':
                $task = new Feature($pdo);
                // Fetch feature-specific data
                $stmt = $pdo->prepare('SELECT * FROM features WHERE task_id = ?');
                $stmt->execute([$taskData['id']]);
                $featureData = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($featureData) {
                    $task->setFeatureType($featureData['feature_type']);
                    $task->setTargetDate(new DateTime($featureData['target_date']));
                }
                break;

            case 'bug':
                $task = new Bug($pdo);
                // Fetch bug-specific data
                $stmt = $pdo->prepare('SELECT * FROM bugs WHERE task_id = ?');
                $stmt->execute([$taskData['id']]);
                $bugData = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($bugData) {
                    $task->setSeverity($bugData['severity']);
                    $task->setBugPriority($bugData['priority']);
                }
                break;

            default:
                throw new Exception("Unknown task type: {$taskType}");
        }

        // Set common task properties
        $task->setTitle($taskData['title']);
        $task->setDescription($taskData['description']);
        $task->setStatus($taskData['status']);
        $task->setType($taskData['type']);
        $task->setCreatedAt(new DateTime($taskData['created_at']));

        if (isset($taskData['priority'])) {
            $task->setPriority($taskData['priority']);
        } else {
            $task->setPriority('medium');
        }

        // Fetch assigned user details
        if (isset($taskData['assigned_to'])) {
            $userId = $taskData['assigned_to'];
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                $user = new User();
                $user->setName($userData['username']);
                $user->setEmail($userData['email']);
                $task->setAssignedTo($user);
            }
        }

        return $task;
    }



    public function create($data)
    {
        try {
            // Begin Transaction
            $this->pdo->beginTransaction();

            // Insert into tasks table
            $sql = "INSERT INTO tasks (title, description, type, status, priority, assigned_to)
                    VALUES (:title, :description, :type, :status, :priority, :assigned_to)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindParam(':type', $data['type'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
            $stmt->bindParam(':priority', $data['priority'], PDO::PARAM_STR);
            $stmt->bindParam(':assigned_to', $data['assigned_to'], PDO::PARAM_INT);

            // Debugging: Log the SQL statement
            error_log("SQL statement: " . $sql);

            if (!$stmt->execute()) {
                // Debugging: Log the error message
                error_log("Failed to create task: " . implode(", ", $stmt->errorInfo()));

                throw new Exception('Failed to create task.');
            }

            $task_id = $this->pdo->lastInsertId();

            // Debugging: Log the inserted task ID
            error_log("Inserted task ID: " . $task_id);


            if ($data['type'] === 'bug') {
                $sql_bug = "INSERT INTO bugs (task_id, severity, priority) VALUES (:task_id, :severity, :priority)";
                $stmt_bug = $this->pdo->prepare($sql_bug);
                $stmt_bug->bindParam(':task_id', $task_id, PDO::PARAM_INT);
                $stmt_bug->bindParam(':severity', $data['severity'], PDO::PARAM_STR);
                $stmt_bug->bindParam(':priority', $data['priority'], PDO::PARAM_STR);

                if (!$stmt_bug->execute()) {
                    throw new Exception('Failed to create bug details.');
                }
            } elseif ($data['type'] === 'feature') {
                $sql_feature = "INSERT INTO features (task_id, feature_type, target_date) VALUES (:task_id, :feature_type, :target_date)";
                $stmt_feature = $this->pdo->prepare($sql_feature);
                $stmt_feature->bindParam(':task_id', $task_id, PDO::PARAM_INT);
                $stmt_feature->bindParam(':feature_type', $data['feature_type'], PDO::PARAM_STR);
                $stmt_feature->bindParam(':target_date', $data['target_date'], PDO::PARAM_STR);

                if (!$stmt_feature->execute()) {
                    throw new Exception('Failed to create feature details.');
                }
            }

            // Commit Transaction
            $this->pdo->commit();

            return $task_id;
        } catch (Exception $e) {
            // Rollback Transaction on Failure
            $this->pdo->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // Additional methods like read, update, delete can be added here
}
