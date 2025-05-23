<?php 
session_start();
// Check if the user is logged in
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <title>Chat Assistant - Android Pentesting Academy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script>
    tailwind.config = {
      darkMode: 'class',
    }
  </script>

</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 text-gray-100 min-h-screen">

  <nav class="bg-black text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
      <div class="text-xl font-bold text-green-500">Android Pentest Academy</div>
      <ul class="flex space-x-4">
        <li><a href="home.php" class="hover:text-green-400 transition">Home</a></li>
        <li><a href="userdashboard.php" class="hover:text-green-400 transition">Dashboard</a></li>
        <li><a href="contact.php" class="hover:text-green-400 transition">Contact</a></li>
        <li><a href="logout.php" class="hover:text-green-400 transition">Logout</a></li>
      </ul>
    </div>
  </nav>

  <div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="bg-gray-800 rounded-lg shadow-2xl p-6">
      <h2 class="text-2xl font-bold text-blue-400 text-center mb-4 flex items-center justify-center">
        <i class="fas fa-robot mr-2 text-blue-500"></i> Ask Our Assistant ROH
      </h2>
      
          <h4 class="hover:text-green-400 transition" style="color: red;">
            <center>
              You Have 4 Prompt/Hour  

            </center>
            </h4>

      <textarea id="prompt"
        class="w-full h-32 p-4 text-base rounded-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Ask ROH directly like: How to bypass insecure data storage? or provide Roh with source code and ask about hint to pass this challenge and so on..."></textarea>

      <button onclick="sendPrompt()"
        class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 flex justify-center items-center">
        <i class="fas fa-paper-plane mr-2"></i> Ask
      </button>

      <div id="responseBox"
        class="mt-6 p-4 bg-gray-700 rounded-lg text-gray-100 whitespace-pre-wrap min-h-[100px]">
        
      </div>
    </div>
  </div>

  <script>
    function escapeHtml(unsafe) {
      return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
    }

    function stripThinkTags(text) {
      return text.replace(/<think>[\s\S]*?<\/think>/gi, '').trim();
    }

    async function sendPrompt() {
      const prompt = document.getElementById("prompt").value.trim();
      const responseBox = document.getElementById("responseBox");

      if (!prompt) {
        responseBox.innerHTML = "❗ Please enter a question.";
        return;
      }

      responseBox.innerHTML = "⏳ Thinking May take A Minute...";

      try {
        const res = await fetch("chat.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ prompt })
        });

        const data = await res.json();

        if (data.choices && data.choices[0]?.message?.content) {
          const rawOutput = data.choices[0].message.content;
          const cleanedOutput = stripThinkTags(rawOutput);
          const safeOutput = escapeHtml(cleanedOutput);
         responseBox.innerHTML = marked.parse(cleanedOutput);
        // responseBox.innerHTML = safeOutput;
        } else if (data.error) {
          responseBox.innerHTML = `❌ Error: ${escapeHtml(data.error)}`;
        } else {
          responseBox.innerHTML = "⚠️ Unexpected response.";
        }
      } catch (e) {
        responseBox.innerHTML = "❌ Rate Limit. You Used All Prompts Try again after 1 Hour.";
        console.error(e);
      }
    }
  </script>

</body>
</html>
