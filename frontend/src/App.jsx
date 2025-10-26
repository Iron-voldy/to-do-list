import React, { useState, useEffect, useCallback } from 'react';
import TaskForm from './components/TaskForm';
import TaskList from './components/TaskList';
import { taskApi } from './services/api';
import './App.css';

function App() {
  const [tasks, setTasks] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchTasks = useCallback(async () => {
    try {
      setIsLoading(true);
      setError(null);
      const response = await taskApi.getTasks();
      if (response.success) {
        setTasks(response.data);
      } else {
        setError('Failed to load tasks');
      }
    } catch (err) {
      console.error('Error fetching tasks:', err);
      setError('Failed to connect to the server');
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchTasks();
  }, [fetchTasks]);

  const handleTaskCreated = async (taskData) => {
    try {
      const response = await taskApi.createTask(taskData);
      if (response.success) {
        await fetchTasks();
      } else {
        throw new Error(response.message || 'Failed to create task');
      }
    } catch (err) {
      throw new Error(err.response?.data?.message || err.message || 'Failed to create task');
    }
  };

  const handleTaskComplete = async (taskId) => {
    try {
      const response = await taskApi.completeTask(taskId);
      if (response.success) {
        setTasks((prevTasks) => prevTasks.filter((task) => task.id !== taskId));
      } else {
        throw new Error('Failed to complete task');
      }
    } catch (err) {
      console.error('Error completing task:', err);
      throw err;
    }
  };

  return (
    <div className="app-container">
      <h1 className="app-title">To-Do App</h1>
      {error && (
        <div className="global-error">
          {error}
          <button onClick={fetchTasks} className="retry-button">
            Retry
          </button>
        </div>
      )}
      <div className="content-wrapper">
        <TaskForm onTaskCreated={handleTaskCreated} />
        <TaskList
          tasks={tasks}
          onTaskComplete={handleTaskComplete}
          isLoading={isLoading}
        />
      </div>
    </div>
  );
}

export default App;
