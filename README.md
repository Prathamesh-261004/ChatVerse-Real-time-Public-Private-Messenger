💬 ChatVerse – Real-time Public & Private Messenger
ChatVerse is a modern, full-featured chat application built using PHP and MySQL. It offers seamless real-time messaging with support for both public chatrooms and private conversations (only after a friend request is accepted). The app is designed with a sleek dark mode UI and floating action buttons for a mobile-friendly, stylish experience.
🚀 Features
•	🌐 Public Chatroom – Anyone can participate.
•	🔐 Private Chats – Available only after mutual friend request acceptance.
•	📂 Media Support – Share images, videos, audio, PDFs.
•	✅ Delivery & Read Receipts:
   - '✅ Delivered'
   - '✅ Seen at [time]' once read.
•	🧑‍🤝‍🧑 Friend Request System:
   - Send, accept/reject, view requests.
   - 🔔 Red notification alert for pending requests.
•	🖼️ Profile Pictures with clickable modal preview.
•	✨ Floating Buttons:
   - File Upload (top-left)
   - Send Message (bottom-right)
•	🧑‍💻 Responsive Design – Works great on both desktop and mobile.
•	🔄 Auto-refreshing messages with AJAX.
•	🗑️ Inline Delete Button for sender's own messages.
📂 Project Structure
/chatverse/
•	├── index.php              # Login/Register landing
•	├── register.php
•	├── login.php
•	├── chat_with.php          # Private chat UI
•	├── public_chat.php        # Public chat UI
•	├── fetch_messages.php     # Fetch messages via AJAX
•	├── send_message.php       # Handle message + file upload
•	├── delete_message.php     # Delete individual messages
•	├── friend_requests.php    # Accept/Reject friend requests
•	├── send_request.php       # Send friend request
•	├── db.php                 # PDO DB connection
•	├── logout.php
•	├── uploads/               # Folder for uploaded files
•	├── schema.sql             # DB creation script
🛠️ Technologies Used
•	PHP (Procedural)
•	MySQL with PDO
•	AJAX (vanilla JS fetch)
•	HTML5 + Inline CSS
•	Responsive Design
📸 Screenshots
📦 Setup Instructions
1.	✅ Clone this repo or download ZIP.
2.	✅ Import `schema.sql` into your MySQL database.
3.	✅ Update `db.php` with your database credentials.
4.	✅ Create a folder named `/uploads/` and make it writable.
5.	✅ Host using XAMPP / InfinityFree / any PHP-supporting server.
6.	✅ Register a few users and start chatting!

✉️ Feel free to connect or report bugs via GitHub/Email
📄 License
MIT License.
Free to use, modify, and share with attribution.
