<?php 
    include "exe_header.php"
?>
  <div class="top-bar">
    <input type="text" id="searchBar" placeholder="Search...">
    <select id="statusFilter">
      <option value="">All Status</option>
      <option value="pending">Pending</option>
      <option value="approved">Approved</option>
      <option value="denied">Denied</option>
    </select>
    <button id="addBtn">Add</button>
  </div>

  <table id="eventTable">
    <thead>
      <tr>
        <th>Sl No</th>
        <th>Event</th>
        <th>Month</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <!-- Table rows will be added here -->
    </tbody>
  </table>

  <script>
    // Sample initial data
    let events = [
      { slNo: 1, event: "Event A", month: "January", status: "pending" },
      { slNo: 2, event: "Event B", month: "February", status: "approved" },
      { slNo: 3, event: "Event C", month: "March", status: "denied" }
    ];

    const tableBody = document.querySelector('#eventTable tbody');

    function renderTable(data) {
      tableBody.innerHTML = "";
      data.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${item.slNo}</td>
          <td>${item.event}</td>
          <td>${item.month}</td>
          <td>${item.status}</td>
        `;
        tableBody.appendChild(row);
      });
    }

    // Render initial table
    renderTable(events);

    // "Add" button functionality: adds a new row with demo values
    document.getElementById('addBtn').addEventListener('click', () => {
      const newEvent = {
        slNo: events.length + 1,
        event: "New Event",
        month: "April",
        status: "pending"
      };
      events.push(newEvent);
      renderTable(events);
    });

    // Search functionality: filters table rows by event, month, or status
    document.getElementById('searchBar').addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      const filteredData = events.filter(item => {
        return item.event.toLowerCase().includes(searchTerm) ||
               item.month.toLowerCase().includes(searchTerm) ||
               item.status.toLowerCase().includes(searchTerm);
      });
      renderTable(filteredData);
    });

    // Dropdown filter functionality: filters by status
    document.getElementById('statusFilter').addEventListener('change', function() {
      const selectedStatus = this.value;
      const filteredData = selectedStatus 
        ? events.filter(item => item.status === selectedStatus)
        : events;
      renderTable(filteredData);
    });
  </script>

</body>
</html>
