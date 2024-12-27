<?php

class User
{
    private ?int $id = null;
    private string $name;
    private string $email;
    private array $assignedTasks;

    public function __construct(string $name = '', string $email = '')
    {
        $this->name = $name;
        $this->email = $email;
        $this->assignedTasks = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getAssignedTasks(): array
    {
        return $this->assignedTasks;
    }

    public function addAssignedTask(Task $task): self
    {
        if (!in_array($task, $this->assignedTasks)) {
            $this->assignedTasks[] = $task;
            $task->setAssignedTo($this);
        }
        return $this;
    }

    public function removeAssignedTask(Task $task): self
    {
        $key = array_search($task, $this->assignedTasks);
        if ($key !== false) {
            unset($this->assignedTasks[$key]);
        }
        return $this;
    }

    public static function fromArray(array $userData): User
    {
        $user = new User();
        $user->setId($userData['id']);
        $user->setName($userData['username']);
        $user->setEmail($userData['email']);
        return $user;
    }
}
