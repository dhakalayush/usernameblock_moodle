<?php

class block_usernameblock extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_usernameblock');
    }

    /**
     * @return stdClass|null
     */
    function get_content() {
        global $DB;

        if ($this->content === null) {
            $this->content = new stdClass;
            $this->content->text = '<style>
                .block-username {
                    border: 1px solid #ccc;
                    padding: 10px;
                    margin-bottom: 20px;
                }

                .block-username ul {
                    list-style-type: none;
                    padding: 0;
                }

                .block-username li {
                    margin-bottom: 5px;
                }

                .user-details {
                    display: none;
                    margin-top: 10px;
                }
            </style>';
            $this->content->text .= '<script>
                function toggleUserDetails(userId) {
                    var userDetails = document.getElementById("user-details-" + userId);
                    userDetails.style.display = userDetails.style.display === "none" ? "block" : "none";
                }
            </script>';
            $this->content->text .= '<div class="block-username">';
            $this->content->text .= '<ul>';

            // Get all users from the 'user' table
            $users = $DB->get_records('user');

            if (!empty($users)) {
                foreach ($users as $user) {
                    $this->content->text .= '<li><span style="text-decoration: underline; color: blue; cursor: pointer;" onclick="toggleUserDetails(' . $user->id . ')">' . fullname($user) . '</span>';
                    
                    // Get courses taken by the user
                    $courses = $DB->get_records_sql("
                        SELECT c.id, c.fullname
                        FROM {course} c
                        JOIN {user_enrolments} ue ON ue.enrolid = c.id
                        WHERE ue.userid = :userid
                    ", ['userid' => $user->id]);

                    // Display user details when clicked
                    $this->content->text .= '<div class="user-details" id="user-details-' . $user->id . '" style="display: none;">';
                    $this->content->text .= '<p>ID: ' . $user->id . '</p>';
                    $this->content->text .= '<p>Email: ' . $user->email . '</p>';

                    if (!empty($courses)) {
                        $this->content->text .= '<p>Courses:</p><ul>';
                        foreach ($courses as $course) {
                            $this->content->text .= '<li>' . $course->fullname . '</li>';
                        }
                        $this->content->text .= '</ul>';
                    } else {
                        $this->content->text .= '<p>No courses found for this user.</p>';
                    }

                    $this->content->text .= '</div>'; // Close user-details div
                    $this->content->text .= '</li>';
                }
            } else {
                $this->content->text .= '<li>No users found.</li>';
            }

            $this->content->text .= '</ul>';
            $this->content->text .= '</div>';
        }

        return $this->content;
    }
}
