//EmployeeDashboard.js
export class EmployeeDashboard {
  constructor(root) {
    this.root = root;

    // Demo data
    this.tasks = [
      { id: 1, text: "Submit weekly report", completed: true, project: "Project Alpha" },
      { id: 2, text: "Attend team meeting", completed: false, project: "Project Beta" },
      { id: 3, text: "Update client records", completed: false, project: "Project Alpha" },
    ];
  }

  init() {
    this.root.innerHTML = `
      <h1 class="text-center mb-4">Employee Dashboard</h1>
      
      <div class="row text-center mb-4">
        <div class="col"><h5>My Tasks</h5><p>${this.tasks.length}</p></div>
        <div class="col"><h5>Completed</h5><p>${this.completedCount()}</p></div>
        <div class="col"><h5>Progress</h5><p>${this.progressPercent()}%</p></div>
      </div>

      <div class="mb-4">
        <h5>My Progress</h5>
        <div class="progress" style="height: 25px;">
          <div class="progress-bar bg-success" role="progressbar"
            style="width: ${this.progressPercent()}%;" 
            aria-valuenow="${this.progressPercent()}" 
            aria-valuemin="0" 
            aria-valuemax="100">
            ${this.progressPercent()}%
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title">Add a New Task</h5>
          <form id="add-task-form" class="row g-2">
            <div class="col-md-5">
              <input type="text" id="new-task-text" class="form-control" placeholder="Task description" required>
            </div>
            <div class="col-md-4">
              <input type="text" id="new-task-project" class="form-control" placeholder="Project name (tag)" required>
            </div>
            <div class="col-md-3">
              <button class="btn btn-primary w-100" type="submit">Add Task</button>
            </div>
          </form>
        </div>
      </div>

      <div class="mb-4">
        <h5>Your Tasks</h5>
        <ul class="list-group" id="task-list">
          ${this.tasks.map(task => `
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <strong>${task.text}</strong>
                <small class="text-muted">(${task.project})</small>
              </div>
              <div class="d-flex align-items-center">
                <input type="checkbox" class="form-check-input me-2" ${task.completed ? "checked" : ""} data-id="${task.id}">
                <button class="btn btn-sm btn-outline-danger delete-task" data-id="${task.id}">✕</button>
              </div>
            </li>
          `).join('')}
        </ul>
      </div>

      <div class="mb-4">
        <h5>Project Overview</h5>
        <canvas id="projectChart"></canvas>
      </div>
    `;

    this.renderChart();
    this.bindEvents();
  }

  completedCount() {
    return this.tasks.filter(t => t.completed).length;
  }

  progressPercent() {
    return this.tasks.length
      ? Math.round((this.completedCount() / this.tasks.length) * 100)
      : 0;
  }

  bindEvents() {
    const checkboxes = this.root.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => {
      cb.addEventListener('change', e => {
        const id = parseInt(e.target.dataset.id);
        const task = this.tasks.find(t => t.id === id);
        if (task) task.completed = e.target.checked;
        this.init();
      });
    });

    const deleteButtons = this.root.querySelectorAll('.delete-task');
    deleteButtons.forEach(btn => {
      btn.addEventListener('click', e => {
        const id = parseInt(e.target.dataset.id);
        this.tasks = this.tasks.filter(t => t.id !== id);
        this.init();
      });
    });

    const form = this.root.querySelector('#add-task-form');
    form.addEventListener('submit', e => {
      e.preventDefault();
      const text = this.root.querySelector('#new-task-text').value.trim();
      const project = this.root.querySelector('#new-task-project').value.trim();
      if (!text || !project) return;

      this.tasks.push({
        id: Date.now(),
        text,
        project,
        completed: false
      });
      this.init();
    });
  }

  renderChart() {
    const projectNames = [...new Set(this.tasks.map(t => t.project))];
    const taskCounts = projectNames.map(name => this.tasks.filter(t => t.project === name).length);

    const ctx = this.root.querySelector('#projectChart');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: projectNames,
        datasets: [{
          label: 'Tasks per Project',
          data: taskCounts,
          backgroundColor: '#0d6efd'
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } }
      }
    });
  }
}
