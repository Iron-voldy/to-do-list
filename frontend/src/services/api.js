import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

export const taskApi = {
  /**
   * Get all recent tasks
   */
  getTasks: async () => {
    const response = await api.get('/tasks');
    return response.data;
  },

  /**
   * Create a new task
   */
  createTask: async (taskData) => {
    const response = await api.post('/tasks', taskData);
    return response.data;
  },

  /**
   * Mark a task as completed
   */
  completeTask: async (taskId) => {
    const response = await api.put(`/tasks/${taskId}/complete`);
    return response.data;
  },
};

export default api;
