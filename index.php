<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>ATM Login</title>
     <script src="https://cdn.tailwindcss.com"></script>
     <link rel="manifest" href="manifest.json">
    <script>
   
        if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('service-worker.js')
        .then(() => console.log('Service Worker Registered'))
        .catch(error => console.error('Service Worker Registration Failed:', error));
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
              
               <h2 class="text-2xl font-bold mb-6 text-gray-700">Welcome to ATM System</h2>


<a href="login.php" class="w-full bg-green-500 text-white py-3 rounded-lg text-lg font-semibold mb-3 hover:bg-green-600 transition text-center block">
     Login
</a>

<a href="register.php" class="w-full bg-gray-500 text-white py-3 rounded-lg text-lg font-semibold mb-3 hover:bg-gray-600 transition text-center block">
     Register
</a>
              </div>
     </div>
    </body>

</html>