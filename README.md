# 📅 EventGram – Your Telegram Event Assistant

Meet EventGram - your smart Telegram bot for effortless event planning. Create, manage, and share events with ease, right inside Telegram.

## 🚀 About The Project

Say goodbye to messy event planning and hello to **EventGram** – your friendly, automated Telegram event assistant! Whether you're managing a club, a study group, or organizing meetups, EventGram makes event creation and management a breeze.

This bot walks you through the entire process step-by-step, from setting the date to creating Google Calendar reminders — all without ever leaving Telegram. It ensures every team member is on the same page, and every event is neatly organized. Easy to use, easy to share!

---

## 🌟 Key Features

- **Stepwise User Input:**  
  📝 No need for long forms! EventGram asks for the key details like date, time, and location, all through a fun, conversational flow.  

- **Send Announcements to Specific Topics:**  
  📣 Need to target a specific part of your group? EventGram lets you send event announcements to a particular **topic** within a Telegram supergroup, so everyone gets the info where it matters most.

- **Database Integration:**  
  💾 All event data is stored securely in a MySQL database, keeping things organized for easy access.

- **Dynamic Keyboard Options:**  
  ⌨️ Forget endless typing! EventGram uses dynamic keyboards to make interacting with the bot smooth and intuitive.

- **Google Calendar Integration:**  
  📅 Once you confirm your event, EventGram sends you a link to add it to your Google Calendar — so you and your group can stay on top of things!

- **Inline Message Features:**  
  📲 When the event is ready, EventGram sends structured messages to your Telegram group with clickable buttons for easy access and confirmation.

- **Confirmation Process:**  
  ✅ Before finalizing, you can review all your event details to ensure everything looks perfect!

- **Error Handling:**  
  🚧 Mistakes happen! EventGram checks for errors along the way and guides you to correct them, ensuring smooth event setup.

---

## 🛠️ Technical Implementation

- **Database Management:**  
  💻 Built on MySQL, with prepared statements for security and easy data management.

- **Telegram Bot API:**  
  🤖 Communicates seamlessly with the Telegram Bot API to send messages and receive user input.

- **DateTime Handling:**  
  ⏰ Utilizes PHP’s DateTime class for precise handling of dates and times.

- **Modular Design:**  
  🔧 Easy-to-maintain, with functions tailored to specific tasks, making it simple to extend and tweak.

---

## 🎯 Purpose and Use Case

EventGram was designed with one goal in mind: **to make event announcements fun and easy** for Telegram groups! Whether you’re running a club, a study session, or just trying to coordinate with friends, this bot ensures everyone is on the same page.

It’s the perfect tool for groups where communication is key, and everyone needs quick, easy access to important dates. Plus, Google Calendar integration means that no one will miss a meeting again! 

With the option to send announcements to a specific **topic** in a supergroup, you can ensure the right people see the event details at the right time. Perfect for organizing large groups with multiple discussion threads!

---

## 💻 Installation

1. Upload the `eventgram.php` file to your web server.  
2. Create a bot on **Telegram BotFather** and grab your API token.  
3. Update the bot details in the PHP file — it’s all set up for you right at the top!  
4. Set up your MySQL database, and make sure your server is ready to handle the requests.  
5. Let the bot handle the rest and enjoy effortless event management! 🎉

---

## 🧩 Why EventGram?

- **Time-Saver:** With just a few clicks, everyone in your group gets event reminders and Google Calendar invites.
- **Targeted Announcements:** Send event updates to specific topics within a Telegram group for better organization and focus.
- **No Stress:** Say goodbye to missed events and lost details — everything’s organized and automated.
- **Fun to Use:** We believe event management should be simple, interactive, and even a little fun!

---

## 🧬 How It Works

```mermaid
flowchart TD
    A[Telegram sends update to webhook] --> B[Parse incoming JSON]
    B --> C{Message or Callback?}
    C -- Message --> D[Extract chat_id, message_text, username]
    C -- Callback --> E[Extract data from callback]
    
    D --> F{Starts with '/'}
    F -- Yes --> G[Route to command handler]
    F -- No --> H[Store input in context (for multi-step flows)]

    G --> G1{/start: send welcome}
    G --> G2{/add_event: check admin}
    G2 --> G2a{Is admin?}
    G2a -- Yes --> G2b[Set context: waiting for event name]
    G2a -- No --> G2c[Send access denied]

    H --> H1{In context: waiting for event name?}
    H1 -- Yes --> H2[Store event name and ask for date]
    H2 --> H3[Wait for date input]
    H3 --> H4[Store date and ask for location]
    H4 --> H5[Store location and ask for description]
    H5 --> H6[Store description and ask for image URL]
    H6 --> H7[Store URL and insert event into DB]
    H7 --> H8[Send success confirmation]

    G --> G3{/list_events: query DB}
    G3 --> G3a{Events found?}
    G3a -- Yes --> G3b[Format and send list]
    G3a -- No --> G3c[Send "no events" message]

    G --> G4{/delete_event: check admin}
    G4 --> G4a{Is admin?}
    G4a -- Yes --> G4b[Query events and send inline buttons]
    G4a -- No --> G4c[Send access denied]

    E --> I[Match callback with event ID for deletion]
    I --> J[Delete event from DB]
    J --> K[Send deletion success message]
```

---

## 🌍 Ready to get started? 
Try **EventGram** now and simplify your event planning in just a few steps. Your members will thank you! 🎉
