// Hs-Employee.js
import { EmployeeDashboard } from './EmployeeDashboard.js';

document.addEventListener("DOMContentLoaded", () => {
  const root = document.getElementById("employee-root");
  const dashboard = new EmployeeDashboard(root);
  dashboard.init();
});
