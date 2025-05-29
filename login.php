<?php
session_start();
include  "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $card_no = $_POST["card_no"];
     $pin = $_POST["pin"];
     $face_image_data = $_POST["face_image_data"];

     // Check Card & PIN
     $stmt = $conn->prepare("SELECT face_image FROM users WHERE card_no = ? AND pin = ?");
     $stmt->bind_param("ss", $card_no, $pin);
     $stmt->execute();
     $stmt->store_result();

     if ($stmt->num_rows > 0) {
          $stmt->bind_result($face_image);
          $stmt->fetch();

          if (!$face_image) {
               die("<script>alert('❌ Face data not found!'); window.location.href='login.php';</script>");
          }

          // Save captured face image temporarily
          $captured_image = "images/temp_" . uniqid() . ".png";
          file_put_contents($captured_image, base64_decode(explode(";base64,", $face_image_data)[1]));
          $_SESSION['card_no'] = $card_no;
          echo "<script>alert('✅ PIN Verified! Now verifying face...'); window.location.href='verify_face.php?original=$face_image&captured=$captured_image';</script>";
           $_SESSION['card_no'] = $card_no;
     } else {
          echo "<script>alert('❌ Invalid Card Number or PIN!');</script>";
     }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>ATM Face Login</title>
     <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@latest/dist/face-api.min.js"></script>
     <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class=" flex items-center justify-center  bg-gray-100">


<div class="bg-white shadow-lg rounded-lg flex flex-col md:flex-row w-full h-full overflow-hidden">
    <!-- Left Side: ATM Image -->
    <div class="md:w-1/2 w-full bg-gray-200 flex items-center justify-center p-6">
        <img src="Atm.jpg" alt="ATM Machine" class="w-full h-auto rounded-lg shadow-md object-cover">
    </div>

    <!-- Right Side: Login Section -->
    <div class="md:w-1/2 w-full flex flex-col items-center justify-center p-8 bg-white">
        <h2 class="text-2xl font-semibold mb-6 text-center">ATM Face Login</h2>

        <form id="loginForm" method="POST" class="w-full flex flex-col">
            <input type="text" name="card_no" placeholder="Card No" required 
                class="w-full p-3 mb-3 border rounded shadow-sm focus:ring focus:ring-blue-200">
            <input type="password" name="pin" placeholder="PIN" required 
                class="w-full p-3 mb-3 border rounded shadow-sm focus:ring focus:ring-blue-200">
            <input type="hidden" name="face_image_data" id="faceImage">

            <!-- Camera Capture -->
            <button type="button" id="openCamera" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded mb-3 transition-all">📷 Capture Face</button>
            <div class="flex flex-col items-center">
                <video id="video" autoplay class="hidden w-48 rounded-lg shadow-md"></video>
                <canvas id="canvas" class="hidden w-48 rounded-lg shadow-md"></canvas>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded mt-4 transition-all">🔓 Login</button>
        </form>

        <!-- Back Button -->
        <button onclick="window.location.href='index.php'" 
            class="w-full bg-gray-500 hover:bg-gray-600 text-white py-3 rounded mt-4 transition-all">🔙 Back</button>
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

               const imageData = canvas.toDataURL("image/png");
               faceImage.value = imageData;

               alert("✅ Face captured successfully!");
          }

          document.getElementById("openCamera").addEventListener("click", async () => {
               await loadModels();
               await openCamera();
               setTimeout(captureImage, 2000);
          });
     </script>
</body>

</html>