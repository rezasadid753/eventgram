# Telegram Event Announcement Bot

## About The Project
The Telegram Event Announcement Bot streamlines event creation and management in Telegram groups. It collects essential event details through a user-friendly interface and integrates with Google Calendar for seamless organization. This bot can send messages to a specific topic within a supergroup in Telegram, making it an excellent tool for targeted announcements.

## Key Features

- **Stepwise User Input:**  
  The bot employs a multi-step conversational flow, prompting users for specific details such as date, time, duration, event type, location, topic, and notes.

- **Database Integration:**  
  User inputs are stored in a MySQL database, allowing for efficient data management and retrieval.

- **Dynamic Keyboard Options:**  
  The bot generates dynamic keyboard options for user inputs, providing an intuitive interface for interaction.

- **Google Calendar Integration:**  
  Once an event is confirmed, the bot generates a Google Calendar link for easy access.

- **Inline Message Features:**  
  The bot sends structured messages to the Telegram group, including inline buttons for quick actions.

- **Confirmation Process:**  
  A confirmation step allows users to review all event details before submission.

- **Error Handling and Flexibility:**  
  The bot accounts for potential user errors throughout the input process.

## Technical Implementation

- **Database Management:**  
  Utilizes MySQL for data storage, employing prepared statements to prevent SQL injection.

- **Telegram Bot API:**  
  Communicates with the Telegram Bot API via HTTP requests to send messages.

- **DateTime Handling:**  
  Implements PHP's DateTime class for accurate date and time management.

- **Modular Design:**  
  Functions are designed for specific tasks, promoting code reusability and easier maintenance.

## Purpose and Use Case

The primary purpose of the Telegram Event Announcement Bot is to streamline the event management process for organizations or groups using Telegram. It simplifies event creation, ensures accurate communication of details, and provides easy access to calendar integration.

The bot fosters a sense of community by encouraging members to actively participate in events, strengthening group cohesion and collaboration.

## Installation

1. Upload the files to your web server.
2. Create a bot using the Telegram BotFather and obtain your API token.
3. Enter your bot details in the PHP file. The necessary variables are located at the top of the file for easy configuration.
4. Set up the database and ensure all dependencies are installed.
5. Enjoy seamless event management with the bot!

## Contributing

Contributions are pivotal to the growth of our project. Your input fuels innovation and enhances the user experience for everyone. Whether it's a bug fix, feature suggestion, or enhancement, your efforts are highly valued. Feel free to fork the repository, create a new branch, and share your ideas. If you have any suggestions to improve the project, don't hesitate to open an issue and tag it as "enhancement". Your contributions will be warmly welcomed, and together, we can make this project even better. Thank you for your support!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/YourFeature`)
3. Commit your Changes (`git commit -m 'Add some YourFeature'`)
4. Push to the Branch (`git push origin feature/YourFeature`)
5. Open a Pull Request

## License

Flasher is licensed under the MIT License. See the `LICENSE` file for details.

## Contact

For any inquiries or support, feel free to contact via email at <contact@rezasadid.com> or <rezasadid753@gmail.com>.
