<?php
// Database configuration
$servername = "localhost";
$username = "dbyazan"; // Replace with your MySQL username
$password = "0000"; // Replace with your MySQL password
$dbname = "yazan"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle deletion
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM Employees WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Handle creation and updating
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $salary = $_POST['salary'];

    if (isset($_POST['id'])) {
        // Update existing record
        $id = $_POST['id'];
        $sql = "UPDATE Employees SET Name='$name', Address='$address', Salary='$salary' WHERE id=$id";
    } else {
        // Insert new record
        $sql = "INSERT INTO Employees (Name, Address, Salary) VALUES ('$name', '$address', '$salary')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Retrieve employee for update
if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT id, Name, Address, Salary FROM Employees WHERE id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        echo "No employee found with ID $id";
        exit();
    }
}

// Retrieve all employees
$sql = "SELECT id, Name, Address, Salary FROM Employees";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employees</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        input[type="submit"] {
            margin-top: 20px;
            padding: 10px 15px;
        }
    </style>
</head>
<body>
    <h1>Employees</h1>
    <a href="index.php?action=create">Create New Employee</a>
    <br><br>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Salary</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["id"]; ?></td>
                    <td><?php echo $row["Name"]; ?></td>
                    <td><?php echo $row["Address"]; ?></td>
                    <td><?php echo $row["Salary"]; ?></td>
                    <td>
                        <a href="index.php?action=read&id=<?php echo $row["id"]; ?>">View</a> |
                        <a href="index.php?action=update&id=<?php echo $row["id"]; ?>">Update</a> |
                        <a href="index.php?action=delete&id=<?php echo $row["id"]; ?>" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No records found</td>
            </tr>
        <?php endif; ?>
    </table>

    <?php if (isset($_GET['action']) && ($_GET['action'] == 'create' || $_GET['action'] == 'update')): ?>
        <h2><?php echo isset($employee) ? 'Update' : 'Create'; ?> Employee</h2>
        <form method="post" action="index.php">
            <?php if (isset($employee)): ?>
                <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
            <?php endif; ?>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo isset($employee) ? htmlspecialchars($employee['Name']) : ''; ?>" required><br><br>
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo isset($employee) ? htmlspecialchars($employee['Address']) : ''; ?>" required><br><br>
            <label for="salary">Salary:</label>
            <input type="number" step="0.01" id="salary" name="salary" value="<?php echo isset($employee) ? htmlspecialchars($employee['Salary']) : ''; ?>" required><br><br>
            <input type="submit" value="<?php echo isset($employee) ? 'Update' : 'Create'; ?>">
        </form>
    <?php endif; ?>

    <?php if (isset($_GET['action']) && $_GET['action'] == 'read' && isset($_GET['id'])): ?>
        <h2>Employee Details</h2>
        <?php if (isset($employee)): ?>
            <p><strong>ID:</strong> <?php echo $employee["id"]; ?></p>
            <p><strong>Name:</strong> <?php echo $employee["Name"]; ?></p>
            <p><strong>Address:</strong> <?php echo $employee["Address"]; ?></p>
            <p><strong>Salary:</strong> <?php echo $employee["Salary"]; ?></p>
        <?php endif; ?>
        <br>
        <a href="index.php">Back to Employees</a>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>


