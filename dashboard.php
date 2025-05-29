<?php
session_start();
if (!isset($_SESSION["card_no"])) {
     header("Location: index.php");
     exit();
}
include "db.php";

$card_id = $_SESSION['card_no'];

// Fetch User Data
$user_sql = "SELECT id, name, account_no, initial_amount FROM users WHERE card_no='$card_id'";
$result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($result);
$name = $user['name'];
$account_no = $user['account_no'];
$balance = $user['initial_amount'];

// Handle Transactions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $type = $_POST['type'];
     $amount = intval($_POST['amount']);

     if ($amount > 0) {
          if ($type === 'withdraw' && $amount > $balance) {
               echo "<script>alert('Insufficient Balance!');</script>";
          } else {
               $new_balance = ($type === 'withdraw') ? ($balance - $amount) : ($balance + $amount);
               mysqli_query($conn, "UPDATE users SET initial_amount=$new_balance WHERE card_no='$card_id'");
               mysqli_query($conn, "INSERT INTO transactions (card_no, type, amount, balance) VALUES ('$card_id', '$type', $amount, $new_balance)");
               echo "<script>alert('Transaction Successful!'); window.location.href = window.location.href;</script>";
          }
     } else {
          echo "<script>alert('Invalid Amount!');</script>";
     }
}

// Fetch Transactions
$transactions = mysqli_query($conn, "SELECT * FROM transactions WHERE card_no='$card_id' ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>ATM Dashboard</title>
     <script src="https://cdn.tailwindcss.com"></script>
     <script>
          function openModal(type) {
               document.getElementById('transactionType').value = type;
               document.getElementById('transactionModal').classList.remove('hidden');
          }

          function closeModal() {
               document.getElementById('transactionModal').classList.add('hidden');
          }
     </script>
</head>

<body class="h-screen flex items-center justify-center bg-gray-100">


     <div class="bg-white shadow-lg rounded-lg flex flex-col md:flex-row w-full h-full overflow-hidden">
          <!-- Left Side: ATM Image -->
          <div class="md:w-1/2 w-full bg-gray-200 flex items-center justify-center p-6">
               <img src="Atm.jpg" alt="ATM Machine" class="w-full h-auto rounded-lg shadow-md object-cover">
          </div>

          <!-- Right Side: Login Section -->
          <div class="md:w-1/2 w-full flex flex-col items-center justify-center p-8 bg-white">

               <div class="bg-white shadow-lg rounded-lg w-full max-w-md p-6 text-center">
                    <h2 class="text-2xl font-bold text-gray-700">Welcome, <?= htmlspecialchars($name) ?></h2>
                    <p class="text-lg text-gray-500">Balance: ₹<?= number_format($balance, 2) ?></p>
                    <button onclick="openModal('withdraw')" class="bg-red-500 text-white w-full py-2 mt-4 rounded">Withdraw</button>
                    <button onclick="openModal('deposit')" class="bg-green-500 text-white w-full py-2 mt-4 rounded">Deposit</button>
                    <a href="logout.php" class="bg-gray-500 text-white w-full py-2 mt-4 rounded block">Logout</a>

                    <!-- Transaction Modal -->
                    <div id="transactionModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
                         <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                              <h3 class="text-xl font-semibold">Transaction</h3>
                              <form method="POST">
                                   <input type="hidden" id="transactionType" name="type">
                                   <input type="number" name="amount" class="w-full mt-4 p-2 border rounded" placeholder="Enter Amount" required>
                                   <button type="submit" class="bg-green-500 text-white w-full py-2 mt-4 rounded">Submit</button>
                              </form>
                              <button onclick="closeModal()" class="bg-gray-400 text-white w-full py-2 mt-2 rounded">Close</button>
                         </div>
                    </div>

                    <!-- Recent Transactions -->
                    <h3 class="text-lg font-semibold mt-6">Recent Transactions</h3>
                    <ul class="text-left">
                         <?php while ($row = mysqli_fetch_assoc($transactions)) { ?>
                              <li class="border-b py-2">🔹 <?= ucfirst(htmlspecialchars($row['type'])) ?>: ₹<?= number_format($row['amount'], 2) ?> (Balance: ₹<?= number_format($row['balance'], 2) ?>)</li>
                         <?php } ?>
                    </ul>
               </div>
          </div>
     </div>



</body>

</html>