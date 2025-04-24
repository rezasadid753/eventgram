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
    A[Start] --> B[Receive Webhook Data]
    B --> C{Is Message Valid?}
    C -- Yes --> D[Get User and Chat Details]
    C -- No --> E[Send Error Message]
    E --> F[End]

    D --> G{Is User in Group?}
    G -- Yes --> H{Is Chat Type Group?}
    G -- No --> I[Send Access Denied Message]
    I --> F[End]

    H -- Yes --> J{Is Command /start or /upcoming?}
    H -- No --> K[Ignore Message]
    K --> F[End]

    J -- Yes --> L[Send Welcome Message with Inline Keyboard]
    J -- No --> M[Ignore Message]
    M --> F[End]

    L --> N{Is Command /start?}
    N -- Yes --> O[Check User in Database]
    N -- No --> P[Ignore Message]
    P --> F[End]

    O -- New User --> Q[Insert User into Database]
    O -- Existing User --> R[Update User Step to 0]
    Q --> S[Send Welcome Message with Options]
    R --> S
    S --> T{User  Chooses Create Announcement?}
    
    T -- Yes --> U[Generate Unique Event ID]
    U --> V[Insert Event ID into Database]
    V --> W[Prompt for Presenter's Name]
    W --> X[Save Presenter's Name]
    X --> Y[Prompt for Year Selection]
    
    Y --> Z{Is Year Valid?}
    Z -- Yes --> AA[Save Year]
    AA --> AB[Prompt for Month Selection]
    Z -- No --> AC[Send Invalid Year Message]
    AC --> F[End]

    AB --> AD{Is Month Valid?}
    AD -- Yes --> AE[Save Month]
    AE --> AF[Prompt for Day Selection]
    AD -- No --> AG[Send Invalid Month Message]
    AG --> F[End]

    AF --> AH{Is Day Valid?}
    AH -- Yes --> AI[Save Day]
    AI --> AJ[Prompt for Hour Selection]
    AH -- No --> AK[Send Invalid Day Message]
    AK --> F[End]

    AJ --> AL{Is Hour Valid?}
    AL -- Yes --> AM[Save Hour]
    AM --> AN[Prompt for Minute Selection]
    AL -- No --> AO[Send Invalid Hour Message]
    AO --> F[End]

    AN --> AP{Is Minute Valid?}
    AP -- Yes --> AQ[Save Minute]
    AQ --> AR[Prompt for Duration Selection]
    AP -- No --> AS[Send Invalid Minute Message]
    AS --> F[End]

    AR --> AT{Is Duration Valid?}
    AT -- Yes --> AU[Save Duration]
    AU --> AV[Prompt for Event Type]
    AT -- No --> AW[Send Invalid Duration Message]
    AW --> F[End]

    AV --> AX{Is Type Online or In-Person?}
    AX -- Online --> AY[Prompt for Link]
    AX -- In-Person --> AZ[Prompt for Location]

    AY --> BA[Save Link]
    AZ --> BB[Save Location]
    BB --> BC[Prompt for Topic]
    BA --> BC

    BC --> BD[Save Topic]
    BD --> BE[Prompt for Notes]
    
    BE --> BF{Are Notes Provided?}
    BF -- Yes --> BG[Save Notes]
    BF -- No --> BH[Skip Notes]
    
    BG --> BI[Prepare Announcement Overview]
    BH --> BI

    BI --> BJ[Send Confirmation Preview]
    BJ --> BK{Is Confirmation Received?}
    
    BK -- Yes --> BL[Publish Event]
    BL --> BM[Send Success Message]
    BK -- No --> BN[Cancel Process]
    BN --> F[End]

    F[End]

---

## 🌍 Ready to get started? 
Try **EventGram** now and simplify your event planning in just a few steps. Your members will thank you! 🎉
