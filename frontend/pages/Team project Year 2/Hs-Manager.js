// Hs-Manager.js
import { ManagerDashboard } from './dashboard.js';

document.addEventListener("DOMContentLoaded", () => {
  const root = document.getElementById("root");
  const dashboard = new ManagerDashboard(root);
  dashboard.init();
});
