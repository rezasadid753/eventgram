<?php
// Include database connection
include('db_connection.php');

// Get the webhook data (the message from the user)
$input = file_get_contents('php://input');
$update = json_decode($input, true);

// Get chat details
$user_id = $update['message']['from']['id'] ?? null; // Get user ID
$message = $update['message'] ?? null; // Check if message exists
$chat_id = $message['chat']['id'] ?? null; // Get chat ID
$chat_type = $update['message']['chat']['type'] ?? null; // Get chat type
$text = $message['text'] ?? null; // Get message text

// Define bot credentials and identifiers
define('BOT_TOKEN', 'AAAAA');
define('GROUP_ID', '-0000000000000');
define('TOPIC_ID', 00);
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

// Defining configuration variables for the bot
$group_name = "My Bot";
$group_name_calendar = "MyBot";
$group_username = "MyBotGroup";
$bot_name = "MyBot";
$bot_username = "mybot_Bot";
$bot_token = "AAAAA";
$error_message = "Please try again later. Thank you for your patience!";
$welcome_message_bot = "*Welcome to My Bot!";
$welcome_message_group = "*Hello there! 👋";
$welcome_cover = 'https://example.com/cover.png';
$type_online_note = "It's highly suggested to use GMeet for managing and verifying members.";
$type_inperson_note = "It's suggested to use the following format: *Class Number, Faculty Name, University Name* to provide clear and precise information.";
$announcement_header_message = "Join us for the next BOT NAME gathering!";
$group_id = "-0000000000000";
$topic_id = "00";

// Function to check if a user is a member of the group
function isUserMemberOfGroup($user_id) {
    // Use the defined BOT_TOKEN and GROUP_ID
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/getChatMember?chat_id=" . GROUP_ID . "&user_id=" . $user_id;

    // Make a request to the Telegram API
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Check if the request was successful and the user is in the group
    return isset($data['ok']) && $data['ok'] === true && isset($data['result']['status']) && 
        in_array($data['result']['status'], ['member', 'administrator', 'creator']);
}

// Function: Generates a unique 15-character alphanumeric event ID.
function generateEventID($conn) {
    do {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $event_id = '';
        for ($i = 0; $i < 15; $i++) {
            $event_id .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Check if the generated event_id already exists in the database
        $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->bind_param("s", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } while ($result->num_rows > 0); // Repeat if ID already exists

    return $event_id; // Return unique event ID
}

// Function: Sends an error message to the user
function sendErrorMessage($chat_id, $errorMessage, $error_message) {
    $reply = "*An error occurred:*\n\n```$errorMessage```\n\n$error_message";
    sendMessage($chat_id, $reply);
}

// Function: Sends a message with a keyboard to the user
function sendMessageWithKeyboard($chat_id, $text, $keyboard) {
    $url = API_URL . "sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($text) . "&parse_mode=Markdown&reply_markup=" . json_encode($keyboard);
    file_get_contents($url);
}

// Function: Sends a message with an inline keyboard to the user
function sendMessageWithInlineKeyboard($chat_id, $text, $inline_keyboard) {
    $url = API_URL . "sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($text) . "&parse_mode=Markdown&reply_markup=" . json_encode($inline_keyboard);
    file_get_contents($url);
}

// Function: Sends a message with an inline keyboard and photo to the user
function sendPhotoWithInlineKeyboard($chat_id, $welcome_cover, $welcome_message_group, $inline_keyboard) {
    $url = API_URL . "sendPhoto?chat_id=" . $chat_id . "&photo=" . urlencode($welcome_cover) . "&caption=" . urlencode($welcome_message_group) . "&parse_mode=Markdown&reply_markup=" . json_encode($inline_keyboard);
    file_get_contents($url);
}

// Function: Sends a simple text message to the user
function sendMessage($chat_id, $text) {
    $url = API_URL . "sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($text) . "&parse_mode=Markdown";
    file_get_contents($url);
}

// Function: Sends a message to a specific group topic with an inline keyboard.
function sendMessageToGroup($bot_token, $group_id, $topic_id, $text, $button_text, $button_url) {
    $url = "https://api.telegram.org/bot$bot_token/sendMessage";
    
    // Inline keyboard button configuration
    $keyboard = [
        "inline_keyboard" => [
            [
                [
                    "text" => $button_text,
                    "url" => $button_url
                ]
            ]
        ]
    ];

    // Prepare the payload
    $data = [
        'chat_id' => $group_id,
        'message_thread_id' => $topic_id, // Specify the topic (thread) ID in the group
        'text' => $text,
        'reply_markup' => json_encode($keyboard), // Encode the keyboard array to JSON
    ];

    // Initialize CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    // Execute CURL request
    $response = curl_exec($ch);

    // Close CURL
    curl_close($ch);

    // Return the response from Telegram API (optional)
    return $response;
}

// Function: Create Google Calendar link
function createGoogleCalendarLink($title, $startDate, $endDate, $description = "", $group_name_calendar) {
    // Subtract 3 hours and 30 minutes from both the start and end times
    $startDate->modify('-3 hours -30 minutes');
    $endDate->modify('-3 hours -30 minutes');

    // Ensure valid date and time formats (YYYYMMDD'T'HHMMSS'Z')
    $formattedStartDate = $startDate->format('Ymd\THis\Z');
    $formattedEndDate = $endDate->format('Ymd\THis\Z');

    // Replace spaces and encode special characters for URL
    $encodedTitle = urlencode($title);
    $encodedDescription = urlencode($description);

    // Build the URL with formatted parameters
    $url = "https://calendar.google.com/calendar/render?action=TEMPLATE&dates={$formattedStartDate}%2F{$formattedEndDate}&details={$encodedDescription}&location=&text={$group_name_calendar}: {$encodedTitle}&ctz=Asia%2FTehran";

    return $url;
}

try {
    if ($chat_type == 'private' && !isUserMemberOfGroup($user_id)) {
        // Check if the user is a member of the specified group
        
        sendMessage($chat_id, "*Access Denied!*\n\nIt seems you're not a member of the $group_name group. To create announcements, you'll need to join the group first. Once you're a member, feel free to use this bot to manage your events!");
        
        exit(); // Stop further execution
    } else if ($chat_type == 'group' && $chat_id !== GROUP_ID) {
        // Check if the message is from the specified group
        
        if ($text == "/start@$bot_username" || $text = "/upcoming@$bot_username") {
            // Send welcome message with inline keyboard
            sendMessage($chat_id, "*Access Denied!*\n\nThis bot only operates within the $group_name group. Please join the group to access its features and create announcements!");
            exit(); // Stop further execution for non-command messages
        } else {
            exit(); // Stop further execution for non-command messages
        }
    } else if ($chat_id == GROUP_ID) {
        // Check if the message is from the group and not a command
        
        // Ignore all messages except for /start and /upcoming
        if ($text == '/start' || $text == "/start@$bot_username") {
            // Send welcome message with inline keyboard
            
            sendPhotoWithInlineKeyboard($chat_id, $welcome_cover, $welcome_message_group, [
                'inline_keyboard' => [[
                    ['text' => 'Start', 'url' => "https://t.me/$bot_username"]
                ]]
            ]);
            exit(); // Stop further execution for non-command messages
        } elseif ($text = '/upcoming' || $text = "/upcoming@$bot_username") {
            exit(); // Stop further execution for non-command messages
        } else {
            exit(); // Stop further execution for non-command messages
        }
    } else if ($text == '/start') { 
        // Step: Welcome Message
        
        // Check if the user is already in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE chat_id = ?");
        $stmt->bind_param("i", $chat_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // If the user is not found, insert them into the users table
        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO users (chat_id, step) VALUES (?, ?)");
            $step = 0; // Default step for new users
            $stmt->bind_param("ii", $chat_id, $step);
            $stmt->execute();
        } else {
            // User already exists, update their step
            $stmt = $conn->prepare("UPDATE users SET step = ? WHERE chat_id = ?");
            $step = 0; // Reset step
            $stmt->bind_param("ii", $step, $chat_id);
            $stmt->execute();
        }

        // Send the welcome message with description and keyboard buttons
        $keyboard = [
            'keyboard' => [
                [['text' => 'Create Announcement'], ['text' => 'Upcoming Events']]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
        
        sendMessageWithKeyboard($chat_id, $welcome_message_bot, $keyboard);

    } else if ($text == 'Create Announcement') { 
        // Step: Create Announcement

        // Generate unique alphanumeric event ID and insert it into the events table
        $event_id = generateEventID($conn);
        $stmt = $conn->prepare("INSERT INTO events (id) VALUES (?)");
        $stmt->bind_param("s", $event_id);
        $stmt->execute();

        // Save the event_id in the users table for the current user
        $stmt = $conn->prepare("UPDATE users SET event_id = ?, step = ? WHERE chat_id = ?");
        $step = 101; // Set step to indicate the user is entering their name
        $stmt->bind_param("sii", $event_id, $step, $chat_id);
        $stmt->execute();

        sendMessage($chat_id, "Great! Let's get started with creating your event announcement.");

        // Prompt the user for their full name to be displayed as the presenter
        $reply = "*Name?*\n\nPlease enter your *full name* to be shown as the presenter of the event:\n\n_Step 1 of 12_";
        $keyboard = [
            'keyboard' => [
                [['text' => 'Cancel Process']]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
        sendMessageWithKeyboard($chat_id, $reply, $keyboard);

    } else if ($text == 'Cancel Process') { 
        // Step: Cancel event creation

        // Get the current user's event_id
        $stmt = $conn->prepare("SELECT event_id FROM users WHERE chat_id = ?");
        $stmt->bind_param("i", $chat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row && !empty($row['event_id'])) {
            // Remove the event from the events table with the event_id
            $event_id = $row['event_id'];
            $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
            $stmt->bind_param("s", $event_id);
            $stmt->execute();

            // Reset user's event_id and step in the users table
            $stmt = $conn->prepare("UPDATE users SET event_id = NULL, step = 0 WHERE chat_id = ?");
            $stmt->bind_param("i", $chat_id);
            $stmt->execute();
        }

        sendMessage($chat_id, "The announcement creation process has been *canceled*.");

        // Send the user back to the main menu with confirmation
        $reply = "You can start again by choosing *Create Announcement* or *Upcoming Events* from the buttons below:";
        $keyboard = [
            'keyboard' => [
                [['text' => 'Create Announcement'], ['text' => 'Upcoming Events']]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
        sendMessageWithKeyboard($chat_id, $reply, $keyboard);
    } else if ($text != null) { 
        // Step: Save presenter's name when user sends their name

        // Check the user's current step
        $stmt = $conn->prepare("SELECT step, event_id FROM users WHERE chat_id = ?");
        $stmt->bind_param("i", $chat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row && $row['step'] == 101) {
            // Get the event_id of the user
            $event_id = $row['event_id'];

            // Save the presenter's name in the events table
            $stmt = $conn->prepare("UPDATE events SET presenter = ? WHERE id = ?");
            $stmt->bind_param("ss", $text, $row['event_id']); // Use event_id from the users table
            $stmt->execute();

            // Prepare to set the announcement year (next step)

            // Get the current and next year
            $current_year = date("Y");
            $next_year = $current_year + 1;

            sendMessage($chat_id, "*Name Recorded!*\n\nSaved Value: `$text`");

            // Send a message to guide the user to select the year
            $reply = "*Year?*\n\nNow let's set the date for your event. Please choose the *year* from the buttons below:\n\n_Step 2 of 12_";
            $keyboard = [
                'keyboard' => [
                    [['text' => $current_year], ['text' => $next_year]], // Current year and next year
                    [['text' => 'Cancel Process']] // Cancel option
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ];

            // Send the message with the custom keyboard
            sendMessageWithKeyboard($chat_id, $reply, $keyboard);

            // Update user's step to 102 (waiting for the year selection)
            $stmt = $conn->prepare("UPDATE users SET step = 102 WHERE chat_id = ?");
            $stmt->bind_param("i", $chat_id);
            $stmt->execute();
        } else if ($row && $row['step'] == 102) {
            // Check if the input is a valid year (current or next year)
            $current_year = date("Y");
            $next_year = $current_year + 1;
    
            if ($text == $current_year || $text == $next_year) {
                // Save the selected year (as integer) in the events table
                $year = (int)$text; // Convert the year to integer
                $event_id = $row['event_id']; // Get the event_id from the user row
    
                $stmt = $conn->prepare("UPDATE events SET year = ? WHERE id = ?");
                $stmt->bind_param("is", $year, $event_id); // Bind year as integer and event_id as string
                $stmt->execute();
    
                sendMessage($chat_id, "*Year Recorded!*\n\nSaved Value: `$text`");
    
                // Send a message to ask for the month selection with a custom keyboard
                $reply = "*Month?*\n\nGreat! Now, please choose the *month* from the buttons below:\n\n_Step 3 of 12_";
                $keyboard = [
                    'keyboard' => [
                        [['text' => 'January'], ['text' => 'February'], ['text' => 'March']],
                        [['text' => 'April'], ['text' => 'May'], ['text' => 'June']],
                        [['text' => 'July'], ['text' => 'August'], ['text' => 'September']],
                        [['text' => 'October'], ['text' => 'November'], ['text' => 'December']],
                        [['text' => 'Cancel Process']]
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ];
    
                // Send the message with the month selection keyboard
                sendMessageWithKeyboard($chat_id, $reply, $keyboard);
    
                // Update user's step to 103 (waiting for the month selection)
                $stmt = $conn->prepare("UPDATE users SET step = 103 WHERE chat_id = ?");
                $stmt->bind_param("i", $chat_id);
                $stmt->execute();
    
            } else {
                // Invalid year, prompt the user again
                sendMessage($chat_id, "*Invalid input!*\n\nPlease select a valid year from the options provided.");
            }
        } else if ($row && $row['step'] == 103) {
            // Define an associative array to map month names to their corresponding numbers
            $months = [
                'January' => 1,
                'February' => 2,
                'March' => 3,
                'April' => 4,
                'May' => 5,
                'June' => 6,
                'July' => 7,
                'August' => 8,
                'September' => 9,
                'October' => 10,
                'November' => 11,
                'December' => 12
            ];
    
            // Check if the user input matches any month name
            if (array_key_exists($text, $months)) {
                // Get the month number
                $month = $months[$text];
                $event_id = $row['event_id']; // Get the event_id from the user row
    
                // Save the selected month (as integer) in the events table
                $stmt = $conn->prepare("UPDATE events SET month = ? WHERE id = ?");
                $stmt->bind_param("is", $month, $event_id); // Bind month as integer and event_id as string
                $stmt->execute();
    
                // Determine the number of days in the selected month
                $days_in_month = 31; // Default to 31
                if ($month == 2) {
                    $days_in_month = 28; // February has 28 days
                } elseif (in_array($month, [4, 6, 9, 11])) {
                    $days_in_month = 30; // April, June, September, and November have 30 days
                }
    
                sendMessage($chat_id, "*Month Recorded!*\n\nSaved Value: `$text`");
    
                // Create the message to ask for the day selection
                $reply = "*Day?*\n\nAwesome! Now, please select the *day* from the buttons below:\n\n_Step 4 of 12_";
                $keyboard = [
                    'keyboard' => [],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ];
    
                // Add day buttons to the keyboard (1 to days_in_month)
                $row_buttons = []; // To hold buttons for the current row
                for ($day = 1; $day <= $days_in_month; $day++) {
                    $row_buttons[] = ['text' => (string)$day]; // Add the day button to the current row
    
                    // Add the row to the keyboard after 3 buttons
                    if (count($row_buttons) == 3) {
                        $keyboard['keyboard'][] = $row_buttons; // Add the full row to the keyboard
                        $row_buttons = []; // Reset for the next row
                    }
                }
    
                // If there are remaining buttons that did not fill a full row, add them
                if (!empty($row_buttons)) {
                    $keyboard['keyboard'][] = $row_buttons; // Add the remaining buttons as a row
                }
    
                // Add cancel process button in the last row
                $keyboard['keyboard'][] = [['text' => 'Cancel Process']];
    
                // Send the message with the day selection keyboard
                sendMessageWithKeyboard($chat_id, $reply, $keyboard);
    
                // Update user's step to 104 (waiting for the day selection)
                $stmt = $conn->prepare("UPDATE users SET step = 104 WHERE chat_id = ?");
                $stmt->bind_param("i", $chat_id);
                $stmt->execute();
            } else {
                // Invalid month, prompt the user again
                sendMessage($chat_id, "*Invalid input!*\n\nPlease select a valid month from the options provided.");
            }
        } else if ($row && $row['step'] == 104) {
            // Validate user input to ensure it's a valid day (1-31)
            $day = intval($text); // Convert input to integer
    
            // Check if the day is valid (between 1 and 31)
            if ($day >= 1 && $day <= 31) {
                $event_id = $row['event_id']; // Get the event_id from the user row
    
                // Save the selected day (as integer) in the events table
                $stmt = $conn->prepare("UPDATE events SET day = ? WHERE id = ?");
                $stmt->bind_param("is", $day, $event_id); // Bind day as integer and event_id as string
                $stmt->execute();
    
                sendMessage($chat_id, "*Day Recorded!*\n\nSaved Value: `$text`");
    
                // Send message asking for the hour
                $reply = "*Hour?*\n\nGreat! Now, choose the *hour* for your event from the buttons below:\n\n_Step 5 of 12_";
                $keyboard = [
                    'keyboard' => [],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ];
    
                // Add hour buttons to the keyboard (00 to 23)
                $row_buttons = []; // To hold buttons for the current row
                for ($hour = 0; $hour < 24; $hour++) {
                    $row_buttons[] = ['text' => str_pad($hour, 2, '0', STR_PAD_LEFT)]; // Add hour button with leading zeros
    
                    // Add the row to the keyboard after 3 buttons
                    if (count($row_buttons) == 3) {
                        $keyboard['keyboard'][] = $row_buttons; // Add the full row to the keyboard
                        $row_buttons = []; // Reset for the next row
                    }
                }
    
                // If there are remaining buttons that did not fill a full row, add them
                if (!empty($row_buttons)) {
                    $keyboard['keyboard'][] = $row_buttons; // Add the remaining buttons as a row
                }
    
                // Add cancel process button in the last row
                $keyboard['keyboard'][] = [['text' => 'Cancel Process']];
    
                // Send the message with the hour selection keyboard
                sendMessageWithKeyboard($chat_id, $reply, $keyboard);
    
                // Update user's step to 105 (waiting for the hour selection)
                $stmt = $conn->prepare("UPDATE users SET step = 105 WHERE chat_id = ?");
                $stmt->bind_param("i", $chat_id);
                $stmt->execute();
            } else {
                // Invalid day, prompt the user again
                sendMessage($chat_id, "*Invalid input!*\n\nPlease select a valid day between 1 and 31.");
            }
        } else if ($row && $row['step'] == 105) {
            // Validate user input to ensure it's a valid hour (00 to 23)
            $hour = intval($text); // Convert input to integer
    
            // Check if the hour is valid (between 0 and 23)
            if ($hour >= 0 && $hour <= 23) {
                $event_id = $row['event_id']; // Get the event_id from the user row
    
                // Save the selected hour (as integer) in the events table
                $stmt = $conn->prepare("UPDATE events SET hour = ? WHERE id = ?");
                $stmt->bind_param("is", $hour, $event_id); // Bind hour as integer and event_id as string
                $stmt->execute();
    
                sendMessage($chat_id, "*Hour Recorded!*\n\nSaved Value: `$text`");
    
                // Send message asking for the minutes
                $reply = "*Minute?*\n\nAwesome! Now, choose the *minute* for your event from the buttons below:\n\n_Step 6 of 12_";
                $keyboard = [
                    'keyboard' => [],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ];
    
                // Add minute buttons to the keyboard (00, 10, 20, 30, 40, 50)
                $minute_buttons = []; // To hold buttons for minutes
                for ($minute = 0; $minute <= 50; $minute += 10) {
                    $minute_buttons[] = ['text' => str_pad($minute, 2, '0', STR_PAD_LEFT)]; // Add minute button with leading zeros
                }
    
                // Add buttons to the keyboard in two rows
                $keyboard['keyboard'][] = array_slice($minute_buttons, 0, 3); // First row (00, 10, 20)
                $keyboard['keyboard'][] = array_slice($minute_buttons, 3, 3); // Second row (30, 40, 50)
    
                // Add cancel process button in the last row
                $keyboard['keyboard'][] = [['text' => 'Cancel Process']];
    
                // Send the message with the minute selection keyboard
                sendMessageWithKeyboard($chat_id, $reply, $keyboard);
    
                // Update user's step to 106 (waiting for the minute selection)
                $stmt = $conn->prepare("UPDATE users SET step = 106 WHERE chat_id = ?");
                $stmt->bind_param("i", $chat_id);
                $stmt->execute();
            } else {
                // Invalid hour, prompt the user again
                sendMessage($chat_id, "*Invalid input!*\n\nPlease select a valid hour between 00 and 23.");
            }
        } else if ($row && $row['step'] == 106) {
            // Validate user input to ensure it's a valid minute (00, 10, 20, 30, 40, 50)
            $minute = intval($text); // Convert input to integer
    
            // Check if the minute is valid (00, 10, 20, 30, 40, 50)
            if (in_array($minute, [0, 10, 20, 30, 40, 50])) {
                $event_id = $row['event_id']; // Get the event_id from the user row
    
                // Save the selected minute (as integer) in the events table
                $stmt = $conn->prepare("UPDATE events SET minute = ? WHERE id = ?");
                $stmt->bind_param("is", $minute, $event_id); // Bind minute as integer and event_id as string
                $stmt->execute();
    
                sendMessage($chat_id, "*Minute Recorded!*\n\nSaved Value: `$text`");
    
                // Send message asking for the duration
                $reply = "*Duration?*\n\nGreat! Now, please specify the *duration* of your event in minutes by choosing from the buttons below:\n\n_Step 7 of 12_";
                $keyboard = [
                    'keyboard' => [],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ];
    
                // Add duration buttons to the keyboard
                $durations = [15, 30, 45, 60, 75, 90, 105, 120, 135, 150, 165, 180];
                foreach (array_chunk($durations, 3) as $row) {
                    $keyboard['keyboard'][] = array_map(function($duration) {
                        return ['text' => $duration];
                    }, $row);
                }
    
                // Add cancel process button in the last row
                $keyboard['keyboard'][] = [['text' => 'Cancel Process']];
    
                // Send the message with the duration selection keyboard
                sendMessageWithKeyboard($chat_id, $reply, $keyboard);
    
                // Update user's step to 107 (waiting for the duration selection)
                $stmt = $conn->prepare("UPDATE users SET step = 107 WHERE chat_id = ?");
                $stmt->bind_param("i", $chat_id);
                $stmt->execute();
            } else {
                // Invalid minute, prompt the user again
                sendMessage($chat_id, "*Invalid input!*\n\nPlease select a valid minute option (00, 10, 20, 30, 40, 50).");
            }
        } else if ($row && $row['step'] == 107) {
            // Validate user input to ensure it's a valid duration (numeric value)
            $duration = intval($text); // Convert input to integer
    
            // Check if the duration is valid (15, 30, 45, ..., 180)
            if (in_array($duration, [15, 30, 45, 60, 75, 90, 105, 120, 135, 150, 165, 180])) {
                $event_id = $row['event_id']; // Get the event_id from the user row
    
                // Save the selected duration (as integer) in the events table
                $stmt = $conn->prepare("UPDATE events SET duration = ? WHERE id = ?");
                $stmt->bind_param("is", $duration, $event_id); // Bind duration as integer and event_id as string
                $stmt->execute();
    
                sendMessage($chat_id, "*Duration Recorded!*\n\nSaved Value: `$text`");
    
                // Send message asking for the type of announcement
                $reply = "*Type?*\n\nAwesome! Now, please select the *type of event* from the buttons below:\n\n_Step 8 of 18_";
                $keyboard = [
                    'keyboard' => [
                        [['text' => 'Online'], ['text' => 'In-Person']], // First row with two options
                        [['text' => 'Cancel Process']] // Second row with cancel option
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ];
    
                // Send the message with the type selection keyboard
                sendMessageWithKeyboard($chat_id, $reply, $keyboard);
    
                // Update user's step to 108 (waiting for the type selection)
                $stmt = $conn->prepare("UPDATE users SET step = 108 WHERE chat_id = ?");
                $stmt->bind_param("i", $chat_id);
                $stmt->execute();
            } else {
                // Invalid duration, prompt the user again
                sendMessage($chat_id, "*Invalid input!*\n\nPlease select a valid duration option.");
            }
        } else if ($row && $row['step'] == 108) {
            $event_id = $row['event_id']; // Get the event_id from the user row
    
            // Save the selected type in the events table
            $stmt = $conn->prepare("UPDATE events SET type = ? WHERE id = ?");
            $stmt->bind_param("ss", $text, $event_id); // Bind type as string and event_id as string
            $stmt->execute();
    
            // Check the type and send the appropriate message
            if ($text == "Online") {
                sendMessage($chat_id, "*Event Type Recorded!*\n\nSaved Value: `Online`");
                
                // Ask for the link
                $reply = "*Link?*\n\nGreat choice! Now, please provide the *link to the online platform* where the meeting will be held. $type_online_note Just type in the link below:\n\n_Step 9 of 12_";
            } elseif ($text == "In-Person") {
                sendMessage($chat_id, "*Event Type Recorded!*\n\nSaved Value: `In-Person`");
                
                // Ask for the location
                $reply = "*Location?*\n\nGreat choice! Now, please provide the *location for the announcement*. $type_inperson_note Type the location below:\n\n_Step 9 of 12_";
            } else {
                // If the type is not recognized
                $reply = "*Invalid input!*\n\nIt seems there was an error with the type selection.";
            }
    
            // Add a cancel process button
            $keyboard = [
                'keyboard' => [
                    [['text' => 'Cancel Process']] // Cancel button on the next row
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ];
    
            // Send the message asking for additional info with the cancel button
            sendMessageWithKeyboard($chat_id, $reply, $keyboard);
    
            // Update user's step to 109 (waiting for link or location input)
            $stmt = $conn->prepare("UPDATE users SET step = 109 WHERE chat_id = ?");
            $stmt->bind_param("i", $chat_id);
            $stmt->execute();
        } else if ($row && $row['step'] == 109) {
            $event_id = $row['event_id']; // Get the event_id from the user row
    
            // Save the address (link or location) in the events table
            $stmt = $conn->prepare("UPDATE events SET address = ? WHERE id = ?");
            $stmt->bind_param("ss", $text, $event_id); // Bind address as string and event_id as string
            $stmt->execute();
    
            sendMessage($chat_id, "*Location Recorded!*\n\nSaved Value: `$text`");
    
            // Send message asking for the topic of the announcement
            $reply = "*Topic?*\n\nAwesome! Now, let's set the *topic* for your event. Feel free to be creative or concise, just type your topic below:\n\n_Step 10 of 12_";
    
            // Add a cancel process button
            $keyboard = [
                'keyboard' => [
                    [['text' => 'Cancel Process']] // Cancel button on the next row
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ];
    
            // Send the message asking for the topic with the cancel button
            sendMessageWithKeyboard($chat_id, $reply, $keyboard);
    
            // Update user's step to 110 (waiting for topic input)
            $stmt = $conn->prepare("UPDATE users SET step = 110 WHERE chat_id = ?");
            $stmt->bind_param("i", $chat_id);
            $stmt->execute();
        }  else if ($row && $row['step'] == 110) {
            $event_id = $row['event_id']; // Get the event_id from the user row
    
            // Save the topic in the events table
            $stmt = $conn->prepare("UPDATE events SET topic = ? WHERE id = ?");
            $stmt->bind_param("ss", $text, $event_id); // Bind topic as string and event_id as string
            $stmt->execute();
    
            sendMessage($chat_id, "*Topic Recorded!*\n\nSaved Value: `$text`");
    
            // Send message asking for optional notes
            $reply = "*Notes?*\n\nNow, if you have any *additional notes* you'd like to include, type them below. You can skip this step if you don't have any notes:\n\n_Step 11 of 12_";
    
            // Create keyboard with buttons to skip and cancel process
            $keyboard = [
                'keyboard' => [
                    [['text' => 'Skip']],
                    [['text' => 'Cancel Process']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ];
    
            // Send the message asking for notes with the keyboard
            sendMessageWithKeyboard($chat_id, $reply, $keyboard);
    
            // Update user's step to 111 (waiting for notes input)
            $stmt = $conn->prepare("UPDATE users SET step = 111 WHERE chat_id = ?");
            $stmt->bind_param("i", $chat_id);
            $stmt->execute();
        } else if ($row && $row['step'] == 111) {
            $event_id = $row['event_id']; // Get the event_id from the user row
    
            // Check if the user skipped notes or provided them
            if (strcasecmp($text, 'Skip') === 0) {
                $notes = null; // Leave notes as null
            } else {
                $notes = $text; // Save the provided notes
            }
    
            // Save notes in the events table
            $stmt = $conn->prepare("UPDATE events SET notes = ? WHERE id = ?");
            $stmt->bind_param("ss", $notes, $event_id); // Bind notes as string and event_id as string
            $stmt->execute();
    
            // Fetch event details to prepare the announcement overview
            $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->bind_param("s", $event_id); // Bind event_id
            $stmt->execute();
            $event_result = $stmt->get_result();
            $event = $event_result->fetch_assoc();
    
            // Prepare the announcement message
            $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $monthName = $monthNames[$event['month'] - 1]; // Get month name
            $dateFormatted = "$monthName {$event['day']}, {$event['year']}";
            $timeFormatted = sprintf("%02d:%02d", $event['hour'], $event['minute']); // Format time with leading zeros
    
            // Build description for Google Calendar
            $description = "Type: {$event['type']}\nLocation: {$event['address']}\n\n";
            if ($notes) {
                $description .= "Notes: $notes\n\n";
            }
            $description .= "Presenter: {$event['presenter']}";
    
            // Create Google Calendar link
            $startDate = new DateTime("{$event['year']}-{$event['month']}-{$event['day']} {$event['hour']}:{$event['minute']}:00");
            $endDate = clone $startDate; // Clone start date
            $endDate->modify("+{$event['duration']} minutes"); // Calculate end date using duration
            $calendarLink = createGoogleCalendarLink($event['topic'], $startDate, $endDate, $description, $group_name_calendar);
    
            // Save the generated link in the events table
            $stmt = $conn->prepare("UPDATE events SET link = ? WHERE id = ?");
            $stmt->bind_param("ss", $calendarLink, $event_id); // Bind link as string and event_id as string
            $stmt->execute();
    
            // Prepare final message with event details
            $finalMessage = "$announcement_header_message\n\n";
            $finalMessage .= "Date: $dateFormatted\n";
            $finalMessage .= "Time: $timeFormatted\n";
            $finalMessage .= "Duration: {$event['duration']} min\n\n";
            $finalMessage .= "Type: {$event['type']}\n";
            $finalMessage .= "Location: {$event['address']}\n\n";
            $finalMessage .= "Topic: {$event['topic']}\n";
            if ($notes) {
                $finalMessage .= "Notes: $notes\n";
            }
            $finalMessage .= "\nPresenter: {$event['presenter']}\n\n";
            $finalMessage .= "Can’t wait to see you there!\n\n#MarkYourCalendar";
    
            // Send message with "Confirm Announcement" and "Cancel Process" buttons
            $keyboard = [
                'keyboard' => [
                    [['text' => 'Confirm Announcement']],
                    [['text' => 'Cancel Process']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ];
            
            if ($notes) {
                $text_notes = "`$notes`";
            } else {
                $text_notes = "None";
            }
            
            sendMessage($chat_id, "*Notes Recorded!*\n\nSaved Value: $text_notes");
            
            sendMessageWithKeyboard($chat_id, "*Confirm?*\n\nHere's a *preview* of your event announcement:\n\n```\n$finalMessage\n```\n\nPlease confirm that everything looks good. If everything is correct, click *Confirm* below, or you can *Cancel* to start over.\n\n_Step 12 of 12_", $keyboard);
    
            // Update user's step to 112 (waiting for confirmation)
            $stmt = $conn->prepare("UPDATE users SET step = 112 WHERE chat_id = ?");
            $stmt->bind_param("i", $chat_id);
            $stmt->execute();
        } else if ($row && $row['step'] == 112 && $text == 'Confirm Announcement') {
            // Get the event_id from the user row
            $event_id = $row['event_id'];
        
            // Prepare a statement to get the user id from users table where chat_id matches the current user
            $stmt = $conn->prepare("SELECT id FROM users WHERE chat_id = ?");
            $stmt->bind_param("s", $chat_id); // Bind the chat_id of the current user
            $stmt->execute();
            
            // Get the result and fetch the user id
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            // Save the user id in the variable userdb_id
            $userdb_id = $row['id'];
        
            // Update conformation column to 1 in events table
            $stmt = $conn->prepare("UPDATE events SET conformation = 1 WHERE id = ?");
            $stmt->bind_param("s", $event_id); // Bind event_id
            $stmt->execute();
        
            // Reset user step and event id
            $stmt = $conn->prepare("UPDATE users SET step = 0, event_id = NULL WHERE id = ?");
            $stmt->bind_param("i", $userdb_id);
            $stmt->execute();
        
            // Fetch event details to create the announcement message
            $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->bind_param("s", $event_id); // Bind event_id
            $stmt->execute();
            $event_result = $stmt->get_result();
            $event = $event_result->fetch_assoc();
        
            // Prepare the announcement message
            $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $monthName = $monthNames[$event['month'] - 1]; // Get month name
            $dateFormatted = "$monthName {$event['day']}, {$event['year']}";
            $timeFormatted = sprintf("%02d:%02d", $event['hour'], $event['minute']); // Format time with leading zeros
        
            // Construct the announcement text
            $announcement = "Join us for the next Chitchat English Club gathering!\n\n";
            $announcement .= "Date: $dateFormatted\n";
            $announcement .= "Time: $timeFormatted\n";
            $announcement .= "Duration: {$event['duration']} min\n\n";
            $announcement .= "Type: {$event['type']}\n";
            $announcement .= "Location: {$event['address']}\n\n";
            $announcement .= "Topic: {$event['topic']}\n";
        
            // Check if there are notes and add them to the announcement
            if ($event['notes']) {
                $announcement .= "Notes: {$event['notes']}\n\n";
            }
        
            $announcement .= "Presenter: {$event['presenter']}\n\n";
            $announcement .= "Can’t wait to see you there!\n\n";
            $announcement .= "#MarkYourCalendar";
        
            // Get the calendar link from the events table
            $link = $event['link'];
        
            // Send announcement
            $text = $announcement;
            $button_text = "Add to Calendar";
            $button_url = $link;
            sendMessageToGroup($bot_token, $group_id, $topic_id, $text, $button_text, $button_url);

            sendMessage($chat_id, "*New Event Scheduled!*\n\nPlease check the group to ensure the event is published successfully.");
            
            // Send the user back to the main menu with confirmation
            $reply = "You can start again by choosing *Create Announcement* or *Upcoming Events* from the buttons below:";
            $keyboard = [
                'keyboard' => [
                    [['text' => 'Create Announcement'], ['text' => 'Upcoming Events']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ];
            sendMessageWithKeyboard($chat_id, $reply, $keyboard);
        }
    }

} catch (Exception $e) {
    // Send error message if an exception occurs
    sendErrorMessage($chat_id, $e->getMessage(), $error_message);
}

?>
