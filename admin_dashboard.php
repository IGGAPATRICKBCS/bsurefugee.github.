<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.html');
    exit;
}

// Get all applicants
$stmt = $conn->query("SELECT * FROM applicants ORDER BY created_at DESC");
$applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Refugee Training Program</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color:dark blue;
            --secondary-color:rgb(208, 205, 238);
            --light-color:rgba(245, 245, 245, 0.7);
            --dark-color: purple;
            --success-color: #4CAF50;
            --warning-color: #ff9800;
            --danger-color:rgb(216, 78, 163);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
        }

        header {
            background-color: var(--primary-color);
            color:dark blue;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            background-color: var(--danger-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .applicants-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .applicants-table th, 
        .applicants-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .applicants-table th {
            background-color: var(--primary-color);
            color: darkblue;
        }

        .applicants-table tr:hover {
            background-color:orange;
        }

        .status-pending {
            color: var(--warning-color);
            font-weight: bold;
        }

        .status-approved {
            color: var(--success-color);
            font-weight: bold;
        }

        .status-rejected {
            color: var(--danger-color);
            font-weight: bold;
        }

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 5px;
        }

        .view-btn {
            background-color: orange;
            color: white;
        }

        .approve-btn {
            background-color: var(--success-color);
            color: white;
        }

        .reject-btn {
            background-color: var(--danger-color);
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: blue;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 5px;
            width: 80%;
            max-width: 800px;
        }

        .close-btn {
            float: right;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .applicant-details {
            margin-top: 1rem;
        }

        .detail-row {
            display: flex;
            margin-bottom: 1rem;
        }

        .detail-label {
            font-weight: bold;
            width: 150px;
        }

        .document-link {
            color: var(--secondary-color);
            text-decoration: none;
        }

        .document-link:hover {
            text-decoration: underline;
        }
        .logo-container {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .logo-container img {
      height: 70px;
    }

    .header-title h1 {
      font-size: 1.5rem;
    }
    </style>
</head>
<body>
    <header>
    <div class="logo-container">
      <img src="images/BSU-logo.png" alt="BSU Logo">
        <h1> Welcome To Refugee Training System - ADMIN DASHBOARD</h1><br>

        <div class="admin-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <form action="admin_logout.php" method="post">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <div class="dashboard-header">
            <h2>Applicant Management</h2>
            <div>
                <span>Total Applicants: <?php echo count($applicants); ?></span>
            </div>
        </div>

        <table class="applicants-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th>Applied On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicants as $applicant): ?>
                <tr>
                    <td><?php echo $applicant['id']; ?></td>
                    <td><?php echo htmlspecialchars($applicant['name']); ?></td>
                    <td><?php echo htmlspecialchars($applicant['email']); ?></td>
                    <td><?php echo htmlspecialchars($applicant['phone']); ?></td>
                    <td><?php echo htmlspecialchars($applicant['program']); ?></td>
                    <td class="status-<?php echo $applicant['status']; ?>">
                        <?php echo ucfirst($applicant['status']); ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($applicant['created_at'])); ?></td>
                    <td>
                        <button class="action-btn view-btn" 
                                onclick="viewApplicant(<?php echo $applicant['id']; ?>)">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="action-btn approve-btn" 
                                onclick="updateStatus(<?php echo $applicant['id']; ?>, 'approved')">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="action-btn reject-btn" 
                                onclick="updateStatus(<?php echo $applicant['id']; ?>, 'rejected')">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Applicant Details Modal -->
    <div id="applicantModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2>Applicant Details</h2>
            <div id="applicantDetails" class="applicant-details">
                <!-- Details will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <script>
        // View applicant details
        function viewApplicant(id) {
            fetch(`get_applicant.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const applicant = data.applicant;
                        const details = `
                            <div class="detail-row">
                                <div class="detail-label">Full Name:</div>
                                <div>${applicant.name}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Date of Birth:</div>
                                <div>${applicant.dob}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Email:</div>
                                <div>${applicant.email}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Phone:</div>
                                <div>${applicant.phone}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Country:</div>
                                <div>${applicant.country}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Language:</div>
                                <div>${applicant.language}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Program:</div>
                                <div>${applicant.program}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Education Level:</div>
                                <div>${applicant.education_level}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Background:</div>
                                <div>${applicant.background || 'N/A'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">ID Document:</div>
                                <div><a href="${applicant.id_document_path}" class="document-link" target="_blank">View Document</a></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Academic Document:</div>
                                <div><a href="${applicant.academic_document_path}" class="document-link" target="_blank">View Document</a></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Status:</div>
                                <div class="status-${applicant.status}">${applicant.status.charAt(0).toUpperCase() + applicant.status.slice(1)}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Applied On:</div>
                                <div>${new Date(applicant.created_at).toLocaleDateString()}</div>
                            </div>
                        `;
                        document.getElementById('applicantDetails').innerHTML = details;
                        document.getElementById('applicantModal').style.display = 'block';
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching applicant details');
                });
        }

        // Update applicant status
        function updateStatus(id, status) {
            if (confirm(`Are you sure you want to ${status} this application?`)) {
                fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&status=${status}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Application ${status} successfully`);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating status');
                });
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('applicantModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('applicantModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
   <footer>
    
    <p>FOR ONLY ADMINS</p>
    
  </footer>
</body>
</html>