# 📱 Android Pentesting Academy

**Android Pentesting Academy** bridges the gap between theory and practice in mobile app security. It offers a hands-on platform where users explore and exploit real-world Android vulnerabilities in a safe, educational environment.

Users analyze APKs, reverse engineer code, intercept traffic, and exploit vulnerabilities — gaining practical skills through guided challenges. A built-in AI mentor, **Roh**, enhances the learning experience by offering smart, non-spoiler hints and guidance.

---

## 👥 Who Is This For?

- 🧑‍💻 **Security Researchers** – Practice real Android exploitation techniques  
- 👨‍🏫 **Instructors** – Offer hands-on training for ethical hacking courses  
- 📱 **Android Developers** – Understand how attackers find and exploit vulnerabilities  
- 🎓 **University Students** – Learn through summer training or mobile security modules  

Whether you're a beginner or an expert, the academy provides real-world labs and AI-powered support to grow your skills.

---

## 🎯 Project Overview

The goal of **Android Pentesting Academy** is to:

- 🛡️ Help **security engineers** practice Android penetration testing  
- 📱 Enable **developers** to understand and avoid vulnerabilities  
- 🎓 Support **students and institutions** with practical training tools  

A built-in AI assistant, **Roh**, provides:

- 🤖 Short, helpful **hints** (no spoilers!)  
- 🧭 Clear guidance on lab objectives  
- 🚀 Boosted learning efficiency through AI-driven mentorship  

---

## 🚀 Features

- 🧠 **AI Assistant “Roh”** for in-lab guidance  
- ⚔️ Real-world vulnerability labs in **Competition Mode**  
- ✅ **Flag submission** system (valid only once per lab)  
- 🔐 **Attribute-Based Access Control (ABAC)** for role-based access  
- 🛠️ **Lab upload/review workflow** for Creators and Admins  
- 🧾 **Ticketing system** for support queries  
- 📊 **Scoreboard** to track user progress and competition  

---

## 🧩 Platform Roles

| Role       | Description                                      |
|------------|--------------------------------------------------|
| 🧑‍💻 **User**     | Solve labs, submit flags, view personal progress |
| 🧪 **Creator**  | Design/upload vulnerable labs for review     |
| 🛡️ **Admin**     | Review, approve, or reject labs             |
| 🛠️ **Support**   | Respond to user tickets and provide help     |

---

## 🛠️ Setup Instructions

### ⚙️ Step-by-Step Installation Guide

#### 📥 1. Download the Project

- Download the repository as a `.zip` file
- Extract it to a directory of your choice

#### 🧰 2. Install and Configure XAMPP

- [Download XAMPP](https://www.apachefriends.org/index.html) and install it
- Move the extracted project folder to the `htdocs` directory inside the XAMPP installation path  
  Example: `C:\xampp\htdocs\android_pentestacademy`

#### 🗃️ 3. Import the Database

- Start **Apache** and **MySQL** from the XAMPP control panel
- Open [phpMyAdmin](http://localhost/phpmyadmin)
- Create a new database, e.g., `android_pentest_academy`
- Click **Import**, select `android_pentest_academy.sql` from the project folder, and import it

#### 🔐 4. Configure Mail Settings

- Open `mail.php` in the project
- Replace with your own:
  ```php
  $mail->Username = 'your-email@example.com';
  $mail->Password = 'your-mail-password';
  ```
#### 🔑 5. Add AI Assistant API Key

    Open chat.php

   **Replace the placeholder with your Together API Key:**

    $api_key = 'your-openai-api-key';

#### ▶️ 6. Run the Project

    Open a browser and navigate to:http://localhost/android_pentestacademy/
 **You’re ready to explore Android Pentesting Academy!**



---

## 📚 Contributing

Interested in contributing labs or improving the system? Please submit a pull request or open an issue!

---

## 📜 License

This project is open source project.

---

## 🤝 Acknowledgments

Thanks to the mobile security and ethical hacking communities for the inspiration and support in building this academy.

---
