# Android Pentesting Academy

An educational platform that helps users learn Android app security through real-world vulnerable labs — guided by AI assistance.

## 🎯 Project Overview

**Android Pentesting Academy** is designed to help:
- 🛡️ Security engineers practice Android app penetration testing
- 📱 Android developers understand common vulnerabilities
- 🎓 Students and institutions offer practical mobile security training

The platform integrates **AI (Roh)**, a smart assistant that:
- Provides short responses (max ~300 words)
- Helps users with labs by offering **hints** (not full solutions)
- Improves learning efficiency without spoiling the challenge

---

## 🚀 Features

- 🧠 **AI assistant “Roh”** for hints and guidance
- 🎯 Real-world vulnerability-based labs (e.g., insecure storage, insecure communication, etc.)
- ✅ Flag submission system (one-time valid submission)
- 🔐 Attribute-Based Access Control (ABAC) for secure access per role
- 🛠️ Lab upload/review workflow for creators and admins
- 🧾 Ticketing system for support queries
- 📊 Scoreboard tracking progress

---

## 🧩 Roles

| Role    | Capabilities |
|---------|--------------|
| **User** | Solve labs, submit flags, view progress |
| **Creator** | Create labs with real-world flaws |
| **Admin** | Review/approve/reject labs |
| **Support** | Handle tickets and assist users |

---

## 🛠️ Setup Instructions

### 🔧 Option 1: Run from Source Code

#### Prerequisites

- PHP ≥ 8.4
- MySQL
- Composer (optional)
- OpenAI API Key (for AI Assistant)

#### Steps

1. Clone the repo:
   ```bash
   git clone https://github.com/your-org/android-pentest-academy.git
   cd android-pentest-academy
```
