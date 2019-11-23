<?php

namespace App\Scheduler;

use Carbon\Carbon;
use DateTime;

class Kernel
{
    protected $tasks = [];

    protected $date;

    public function run()
    {
        foreach ($this->getTasks() as $task) {
            if ($task->isDueToRun($this->getDate())) $task->handle();
        }
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function add(Task $task)
    {
        $this->tasks[] = $task;
        // Return the task being added for further chaining
        return $task;
    }

    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date = $this->date ?: Carbon::now();
    }
}
