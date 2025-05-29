<?php
include  "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["face_image_data"])) {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $account_no = $_POST["account_no"];
    $initial_amount = $_POST["initial_amount"];
    $card_no = $_POST["card_no"];
    $pin = $_POST["pin"];

    // Convert Base64 Image to File
    $face_image_data = $_POST["face_image_data"];
    $target_dir = "images/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Create directory if not exists
    }

    $face_image = $target_dir . uniqid() . ".png"; // Unique filename
    $image_parts = explode(";base64,", $face_image_data);

    if (count($image_parts) == 2) {
        $image_base64 = base64_decode($image_parts[1]);
        file_put_contents($face_image, $image_base64); // Save the image
    } else {
        die("❌ Invalid image format.");
    }

    // Insert Data into Database
    $stmt = $conn->prepare("INSERT INTO users (name, phone, account_no, initial_amount, card_no, pin, face_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdsss", $name, $phone, $account_no, $initial_amount, $card_no, $pin, $face_image);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Registration Successful!'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('❌ Database Error: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATM Face Registration</title>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@latest/dist/face-api.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class=" flex items-center justify-center bg-gray-100">


<div class="bg-white shadow-lg rounded-lg flex flex-col md:flex-row w-full h-full overflow-hidden">
    <!-- Left Side: ATM Image -->
    <div class="md:w-1/2 w-full bg-gray-200 flex items-center justify-center p-6">
        <img src="Atm.jpg" alt="ATM Machine" class="w-full h-auto rounded-lg shadow-md object-cover">
    </div>

    <!-- Right Side: Login Section -->
    <div class="md:w-1/2 w-full flex flex-col items-center justify-center p-8 bg-white">
    <h2 class="text-2xl font-bold mb-6 text-gray-700">ATM Registration</h2>

<form id="registerForm" method="POST" class="w-full flex flex-col">
    <input type="text" name="name" placeholder="Full Name" required class="w-full p-3 mb-3 border rounded shadow-sm focus:ring focus:ring-blue-200">
    <input type="text" name="phone" placeholder="Phone Number" required class="w-full p-3 mb-3 border rounded shadow-sm focus:ring focus:ring-blue-200">
    <input type="text" name="account_no" placeholder="Account Number" required class="w-full p-3 mb-3 border rounded shadow-sm focus:ring focus:ring-blue-200">
    <input type="number" name="initial_amount" placeholder="Initial Amount" required class="w-full p-3 mb-3 border rounded shadow-sm focus:ring focus:ring-blue-200">
    <input type="text" name="card_no" placeholder="Card Number" required class="w-full p-3 mb-3 border rounded shadow-sm focus:ring focus:ring-blue-200">
    <input type="password" name="pin" placeholder="Set PIN" required class="w-full p-3 mb-3 border rounded shadow-sm focus:ring focus:ring-blue-200">
    <input type="hidden" name="face_image_data" id="faceImage">

    <!-- Capture Face -->
    <button type="button" id="openCamera" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded mb-3 transition-all">📷 Capture Face</button>
    <div class="flex flex-col items-center">
        <video id="video" autoplay class="hidden w-48 rounded-lg shadow-md"></video>
        <canvas id="canvas" class="hidden w-48 rounded-lg shadow-md"></canvas>
    </div>

    <!-- Register Button -->
    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded mt-4 transition-all">📝 Register</button>
</form>

<!-- Back Button -->
<button onclick="window.location.href='index.php'" class="w-full bg-gray-500 hover:bg-gray-600 text-white py-3 rounded mt-4 transition-all">🔙 Back</button>
    </div>
</div>


  



    <script>
        const video = document.getElementById("video");
        const canvas = document.getElementById("canvas");
        const faceImage = document.getElementById("faceImage");
        let isCameraOpen = false;

        async function loadModels() {
            await faceapi.nets.tinyFaceDetector.loadFromUri("models");
            await faceapi.nets.faceLandmark68Net.loadFromUri("models");
            await faceapi.nets.faceRecognitionNet.loadFromUri("models");
            await faceapi.nets.ssdMobilenetv1.loadFromUri("models");
            console.log("✅ Face API Models Loaded");
        }

        async function openCamera() {
            if (!isCameraOpen) {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: true
                    });
                    video.srcObject = stream;
                    video.classList.remove("hidden");
                    isCameraOpen = true;
                } catch (err) {
                    alert("❌ Camera access denied!");
                }
            }
        }

        async function captureImage() {
            if (!isCameraOpen) {
                alert("⚠️ Please open the camera first!");
                return;
            }

            const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();

            if (!detections) {
                alert("❌ No face detected! Try again.");
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext("2d");
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = canvas.toDataURL("image/png"); // Convert to Base64
            faceImage.value = imageData; // Store in hidden input

            alert("✅ Face captured successfully!");
        }

        document.getElementById("openCamera").addEventListener("click", async () => {
            await loadModels();
            await openCamera();
            setTimeout(captureImage, 1000);
        });
    </script>
</body>

</html>